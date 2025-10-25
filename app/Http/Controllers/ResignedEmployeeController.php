<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\ResignedEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ResignedEmployeeController extends Controller
{
    public function cleanup(Request $request)
    {
        $this->authorize('update', Employee::class);

        // Collect identifiers from resigned_employees
        $numbers = ResignedEmployee::query()
            ->whereNotNull('employee_number')
            ->pluck('employee_number')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $niks = ResignedEmployee::query()
            ->whereNotNull('nik')
            ->pluck('nik')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($numbers) && empty($niks)) {
            return back()->with('status', 'Tidak ada data resigned untuk dibersihkan.');
        }

        $affected = 0;
        Employee::query()
            ->when($numbers, fn($q) => $q->whereIn('employee_number', $numbers))
            ->when($niks, fn($q) => $q->orWhereIn('nik', $niks))
            ->chunkById(500, function($chunk) use (&$affected) {
                foreach ($chunk as $emp) {
                    if ($emp->aktif !== 'Nonaktif') {
                        $emp->aktif = 'Nonaktif';
                        $emp->save();
                        $affected++;
                    }
                }
            });

        return back()->with('status', "Bersih-bersih selesai. Ditandai Nonaktif: $affected pegawai.");
    }

    public function purge(Request $request)
    {
        $this->authorize('delete', Employee::class);

        $count = ResignedEmployee::count();
        ResignedEmployee::query()->delete();

        return back()->with('status', "Semua data resign dihapus: $count baris.");
    }
    public function import(Request $request)
    {
        $this->authorize('create', Employee::class); // reuse employee policy for admin gate

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('csv_file')->getRealPath();
        $handle = fopen($path, 'r');
        if (!$handle) {
            return back()->with('error', 'Tidak bisa membuka file CSV');
        }

        $normalize = function ($s) {
            if ($s === null) return '';
            $s = preg_replace("/\x{FEFF}/u", '', (string)$s); // strip BOM
            $s = Str::of($s)->lower()->trim()->toString();
            $s = preg_replace('/[^a-z0-9]+/i', '', $s);
            return $s;
        };

        $cleanVal = function ($v) {
            if ($v === null) return null;
            $v = (string)$v;
            // remove NBSP and control chars
            $v = preg_replace('/[\x00-\x1F\x7F\x{00A0}]+/u', ' ', $v);
            $v = trim($v);
            if ($v === '') return null;
            $lower = Str::lower($v);
            $lowerPlain = preg_replace('/[^a-z0-9]+/i', '', $lower);
            if (in_array($lowerPlain, ['-','—','na','n/a','null','kosong','tidakada','tdkada','tdk'])) return null;
            return $v;
        };

        $parseDate = function ($v) {
            if (!$v) return null;
            $orig = trim((string)$v);
            if ($orig === '') return null;
            $s = str_replace(['\\', '.'], ['/', '/'], $orig);
            $s = str_replace('-', '/', $s);
            // Normalize Indonesian month names to English
            $indo = [
                'januari' => 'January','jan' => 'Jan',
                'februari' => 'February','feb' => 'Feb',
                'maret' => 'March','mar' => 'Mar',
                'april' => 'April','apr' => 'Apr',
                'mei' => 'May',
                'juni' => 'June','jun' => 'Jun',
                'juli' => 'July','jul' => 'Jul',
                'agustus' => 'August','agu' => 'Aug','agt' => 'Aug',
                'september' => 'September','sep' => 'Sep',
                'oktober' => 'October','okt' => 'Oct',
                'november' => 'November','nov' => 'Nov',
                'desember' => 'December','des' => 'Dec',
            ];
            $s2 = $s;
            foreach ($indo as $k => $v2) {
                $s2 = preg_replace('/\b'.preg_quote($k, '/').'\b/i', $v2, $s2);
            }
            foreach ([$s2, $s, $orig] as $cand) {
                try { return Carbon::parse($cand); } catch (\Throwable $e) {}
            }
            return null;
        };

        $header = null;
        $map = [];
        $rowNum = 0;
        $created = 0; $updated = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            if ($rowNum === 1) {
                $header = array_map($normalize, $row);
                // Build column map
                foreach ($header as $i => $col) {
                    if (in_array($col, ['nama','name','namalengkap'])) $map[$i] = 'name';
                    elseif (in_array($col, ['nip','nipy','nomorpegawai','employeeid','employeenumber','nipy_nip'])) $map[$i] = 'employee_number';
                    elseif (in_array($col, ['amanahpokok','tugasutama','amanah'])) $map[$i] = 'amanah_pokok';
                    elseif (in_array($col, ['statuskepegawaian'])) $map[$i] = 'status_kepegawaian';
                    elseif (in_array($col, ['nik','ktp'])) $map[$i] = 'nik';
                    elseif (in_array($col, ['email'])) $map[$i] = 'email';
                    elseif (in_array($col, ['nohp','hp','phone','telepon','telp','nohpwa','nohppawa'])) $map[$i] = 'phone';
                    elseif (in_array($col, ['jabatan','position'])) $map[$i] = 'position';
                    elseif (in_array($col, ['lembaga','unit','instansi'])) $map[$i] = 'lembaga';
                    elseif (in_array($col, ['aktif','aktiftdkaktif','aktiftidakaktif'])) $map[$i] = 'aktif';
                    elseif (in_array($col, ['tanggalmasuk','tglmasuk','masuk','datejoined','joined'])) $map[$i] = 'date_joined';
                    elseif (in_array($col, ['tahunmasuk'])) $map[$i] = 'tahun_masuk';
                    elseif (in_array($col, ['tanggalresign','tglresign','resign','tanggalberhenti','tglberhenti','tanggalkeluar','tglkeluar','resigneddate'])) $map[$i] = 'date_resigned';
                    elseif (in_array($col, ['alasan','alasanresign','reason'])) $map[$i] = 'alasan_resign';
                    elseif (in_array($col, ['keterangan','catatan','notes'])) $map[$i] = 'keterangan';
                    elseif (in_array($col, ['golongan'])) $map[$i] = 'golongan';
                    elseif (in_array($col, ['pangkat'])) $map[$i] = 'pangkat';
                    elseif (in_array($col, ['joblevel','level'])) $map[$i] = 'job_level';
                    elseif (in_array($col, ['tempatlahir','tplahir','placeofbirth'])) $map[$i] = 'place_of_birth';
                    elseif (in_array($col, ['tanggallahir','tgllahir','dob','dateofbirth'])) $map[$i] = 'date_of_birth';
                    elseif (in_array($col, ['jeniskelamin','jk','gender'])) $map[$i] = 'jenis_kelamin';
                    elseif (in_array($col, ['status','statusnikah','statuspernikahan','maritalstatus'])) $map[$i] = 'marital_status';
                    elseif (in_array($col, ['jmlanggotakeluarga','jumlahanggotakeluarga'])) $map[$i] = 'jml_anggota_keluarga';
                    elseif (in_array($col, ['jumlahanak'])) $map[$i] = 'jumlah_anak';
                    elseif (in_array($col, ['kesehatan'])) $map[$i] = 'kesehatan';
                    elseif (in_array($col, ['pendidikanterakhir'])) $map[$i] = 'pendidikan_terakhir';
                    elseif (in_array($col, ['ijazahtambahan'])) $map[$i] = 'ijazah_tambahan';
                    elseif (in_array($col, ['alamat','address'])) $map[$i] = 'alamat';
                }
                // Heuristic: empty header between 'pangkat' and 'tempatlahir' -> job_level
                foreach ($header as $i => $col) {
                    if ($col === '' || $col === null) {
                        $prev = $header[$i-1] ?? null;
                        $next = $header[$i+1] ?? null;
                        if (in_array($prev, ['pangkat']) && in_array($next, ['tempatlahir','tplahir','placeofbirth'])) {
                            $map[$i] = 'job_level';
                        }
                    }
                }
                continue;
            }

            // Build data from row
            $data = [];
            foreach ($row as $i => $val) {
                $field = $map[$i] ?? null;
                if (!$field) continue;
                $val = $cleanVal($val);
                if ($val === null) continue;
                if (in_array($field, ['date_joined','date_resigned','date_of_birth'])) {
                    $data[$field] = $parseDate($val);
                } else {
                    $data[$field] = $val;
                }
            }

            // Normalize Aktif values
            if (isset($data['aktif'])) {
                $v = Str::lower((string)$data['aktif']);
                $v = preg_replace('/[^a-z]/', '', $v);
                $data['aktif'] = in_array($v, ['aktif','active','ya','yes']) ? 'Aktif' : 'Nonaktif';
            }

            // Clean integer fields
            foreach (['jml_anggota_keluarga', 'jumlah_anak'] as $intField) {
                if (isset($data[$intField])) {
                    $v = trim((string)$data[$intField]);
                    // Remove if it's a placeholder or non-numeric
                    if (!is_numeric($v) || $v === '' || $v === '-' || $v === '0') {
                        unset($data[$intField]);
                    } else {
                        $data[$intField] = (int)$v;
                    }
                }
            }

            // Tahun masuk: keep original (as year text) and derive date_joined if missing
            if (isset($data['tahun_masuk'])) {
                $tm = $parseDate($data['tahun_masuk']);
                if ($tm) {
                    $data['tahun_masuk'] = (string)$tm->year;
                    if (empty($data['date_joined'])) {
                        $data['date_joined'] = $tm;
                    }
                } else {
                    $data['tahun_masuk'] = trim((string)$data['tahun_masuk']);
                }
            }

            // Fallback: if position is empty but amanah_pokok exists, use first part as position
            if (empty($data['position'] ?? null) && !empty($data['amanah_pokok'] ?? null)) {
                $first = explode(',', (string)$data['amanah_pokok'])[0] ?? null;
                if ($first) { $data['position'] = trim($first); }
            }

            if (!isset($data['name'])) {
                // skip incomplete rows
                continue;
            }

            // Try link to employees by employee_number or nik
            $employeeId = null;
            if (!empty($data['employee_number'])) {
                $employeeId = optional(Employee::where('employee_number', $data['employee_number'])->first())->id;
            }
            if (!$employeeId && !empty($data['nik'])) {
                $employeeId = optional(Employee::where('nik', $data['nik'])->first())->id;
            }
            if ($employeeId) {
                $data['employee_id'] = $employeeId;
            }

            // Upsert by employee_number if available, else by nik+date_resigned
            $match = [];
            if (!empty($data['employee_number'])) {
                $match = ['employee_number' => $data['employee_number']];
            } elseif (!empty($data['nik']) && !empty($data['date_resigned'])) {
                $match = ['nik' => $data['nik'], 'date_resigned' => $data['date_resigned']];
            }

            if ($match) {
                $existing = ResignedEmployee::where($match)->first();
                if ($existing) {
                    $existing->fill($data);
                    $existing->save();
                    $updated++;
                } else {
                    ResignedEmployee::create($data);
                    $created++;
                }
            } else {
                // no reliable match keys; just create
                ResignedEmployee::create($data);
                $created++;
            }
        }

        fclose($handle);

        return back()->with('status', "Import selesai. Ditambahkan: $created, Diperbarui: $updated");
    }
}
