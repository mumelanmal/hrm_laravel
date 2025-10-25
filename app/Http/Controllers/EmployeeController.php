<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $employees = Employee::orderBy('id', 'desc')->paginate(25);

        return view('pages.employees.index', compact('employees'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('csv_file')->getRealPath();

        if (($handle = fopen($path, 'r')) === false) {
            return back()->with('error', 'Unable to open uploaded file.');
        }

        // Read header
        $header = fgetcsv($handle);
        if (! $header) {
            return back()->with('error', 'CSV file is empty or invalid.');
        }

        // Normalize a header string for robust matching (case/spacing/punctuation)
        $normalize = function (?string $s): string {
            $s = (string) $s;
            // Strip UTF-8 BOM if present
            $s = preg_replace('/^\xEF\xBB\xBF/', '', $s ?? '') ?? '';
            $s = trim($s);
            $s = Str::of($s)
                ->replace(["\n", "\r"], ' ')
                ->lower()
                ->replace(['.', '/', '(', ')', ':', '-', '  '], ' ')
                ->squish();
            return (string) $s;
        };

        $normalized = array_map($normalize, $header);

        // Deterministic mapping from normalized header -> attribute
        $map = [
            'no' => null, // skip row numbers
            'nama' => 'name',
            'nomor induk pegawai yayasan' => 'employee_number',
            'nomor induk pegawai' => 'employee_number',
            'nomor pegawai' => 'employee_number',
            'no pegawai' => 'employee_number',
            'nomor' => 'employee_number',
            'nip' => 'employee_number',
            'nipy' => 'employee_number',
            'no nip' => 'employee_number',
            'no. nip' => 'employee_number',
            'no nipy' => 'employee_number',
            'no. nipy' => 'employee_number',
            // NIK / KTP variations
            'nik' => 'nik',
            'nik ktp' => 'nik', // from header like "NIK/KTP"
            'ktp' => 'nik',
            'no ktp' => 'nik',
            'no. ktp' => 'nik',
            'nomor induk kependudukan' => 'nik',
            'no induk kependudukan' => 'nik',
            'no. induk kependudukan' => 'nik',
            'amanah' => 'position',
            'status kepegawaian' => 'status_kepegawaian',
            'tahun masuk' => 'tahun_masuk',
            'golongan' => 'golongan',
            'pangkat' => 'pangkat',
            '' => 'job_level', // handle empty header column (often job level like "Pegawai Madya")
            'place of birth' => 'place_of_birth',
            'date of birth' => 'date_of_birth',
            'alamat' => 'alamat',
            'jenis kelamin' => 'jenis_kelamin',
            // Important: exact 'status' means marital status, not "status kepegawaian"
            'status' => 'marital_status',
            'jml anggota keluarga' => 'jml_anggota_keluarga',
            'jumlah anak' => 'jumlah_anak',
            'no hp' => 'phone',
            'no hp wa' => 'phone',
            'no. hp' => 'phone',
            'no. hp wa' => 'phone',
            'kesehatan' => 'kesehatan',
            'pendidikan terakhir' => 'pendidikan_terakhir',
            'ijazah tambahan' => 'ijazah_tambahan',
            'lembaga' => 'lembaga',
            'aktif tdk aktif' => 'aktif',
        ];

        // Build index -> attribute mapping using normalized exact matches or best-effort aliasing
        $indexMap = [];
        foreach ($normalized as $i => $h) {
            if (array_key_exists($h, $map)) {
                $indexMap[$i] = $map[$h];
                continue;
            }
            // If header tokens contain an explicit keyword, map directly
            $tokens = preg_split('/\s+/', $h) ?: [];
            if (in_array('nik', $tokens, true)) {
                $indexMap[$i] = 'nik';
                continue;
            }
            if (in_array('nipy', $tokens, true) || in_array('nip', $tokens, true)) {
                $indexMap[$i] = 'employee_number';
                continue;
            }
            // Best-effort: try to match by starts-with, preferring longer keys first
            $keysByLength = array_keys($map);
            usort($keysByLength, function ($a, $b) {
                return strlen((string)$b) <=> strlen((string)$a);
            });
            foreach ($keysByLength as $key) {
                if (! $key) { continue; }
                if (Str::startsWith($h, $key)) {
                    $indexMap[$i] = $map[$key];
                    break;
                }
            }
        }

        $imported = 0;
        while (($row = fgetcsv($handle)) !== false) {
            // skip empty rows
            if (count(array_filter($row)) === 0) {
                continue;
            }

            $data = [];
            foreach ($row as $i => $value) {
                if (! isset($indexMap[$i])) {
                    continue;
                }
                $attr = $indexMap[$i];
                $val = trim((string)$value);

                // normalize some fields
                if ($attr === 'date_of_birth') {
                    $val = $this->parseDate($val);
                }
                if ($attr === 'aktif') {
                    // Normalize variants to a consistent set (Aktif | Nonaktif)
                    $v = Str::lower($val);
                    if (in_array($v, ['aktif','ya','yes','y'], true)) {
                        $val = 'Aktif';
                    } elseif (in_array($v, ['tdk aktif','tidak aktif','nonaktif','no','n'], true)) {
                        $val = 'Nonaktif';
                    }
                }
                if (in_array($attr, ['jml_anggota_keluarga', 'jumlah_anak'])) {
                    $val = is_numeric($val) ? (int)$val : null;
                }

                // Skip empty values to avoid overwriting existing data on upsert
                if ($val === '') {
                    continue;
                }
                $data[$attr] = $val;
            }

            if (empty($data['name']) && empty($data['employee_number'])) {
                continue; // nothing useful
            }

            // upsert by employee_number when possible, otherwise create
            if (! empty($data['employee_number'])) {
                Employee::updateOrCreate([
                    'employee_number' => $data['employee_number'],
                ], $data);
            } else {
                Employee::create($data);
            }

            $imported++;
        }

        fclose($handle);

        return back()->with('status', "Imported {$imported} rows.");
    }

    public function export(Request $request)
    {
        $this->authorize('viewAny', Employee::class);

        $q = Employee::query();
        if ($request->filled('q')) {
            $s = trim($request->string('q'));
            $q->where(function ($w) use ($s) {
                $w->where('name', 'like', "%{$s}%")
                    ->orWhere('employee_number', 'like', "%{$s}%")
                    ->orWhere('nik', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%");
            });
        }
        if ($request->filled('department')) {
            $q->where('department', $request->string('department'));
        }
        if ($request->filled('status')) {
            if ($request->string('status') === 'tetap') {
                $q->where('status_kepegawaian', 'Pegawai Tetap');
            } elseif ($request->string('status') === 'kontrak') {
                $q->where('status_kepegawaian', 'Kontrak');
            }
        }
        if ($request->filled('aktif')) {
            $q->where('aktif', $request->string('aktif'));
        }

        $filename = 'employees_export_' . now()->format('Ymd_His') . '.csv';
        $columns = [
            'id','employee_number','nik','name','email','phone','position','department','status_kepegawaian','aktif','lembaga','date_joined','created_at'
        ];

        return response()->streamDownload(function () use ($q, $columns) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);
            $q->orderBy('id')->chunk(500, function ($rows) use ($out, $columns) {
                foreach ($rows as $row) {
                    $line = [];
                    foreach ($columns as $col) {
                        $val = $row->{$col};
                        if ($val instanceof \Carbon\CarbonInterface) {
                            $val = $val->toDateString();
                        }
                        $line[] = $val;
                    }
                    fputcsv($out, $line);
                }
            });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function destroy(Request $request, Employee $employee)
    {
        $this->authorize('delete', $employee);
        $employee->delete();
        return back()->with('status', 'Employee deleted');
    }

    private function parseDate($value)
    {
        $value = trim((string)$value);
        if ($value === '') {
            return null;
        }

        // Replace Indonesian month names with English equivalents for parsing
        $indoMonths = [
            'januari' => 'january',
            'februari' => 'february',
            'maret' => 'march',
            'april' => 'april',
            'mei' => 'may',
            'juni' => 'june',
            'juli' => 'july',
            'agustus' => 'august',
            'september' => 'september',
            'oktober' => 'october',
            'november' => 'november',
            'desember' => 'december',
            // common misspelling seen: "sepember"
            'sepember' => 'september',
        ];
        $lower = Str::lower($value);
        foreach ($indoMonths as $id => $en) {
            $lower = str_replace($id, $en, $lower);
        }
        $value = $lower;

        // try common formats
        $formats = [
            'd F Y',
            'd M Y',
            'd F Y',
            'd m Y',
            'Y-m-d',
            'd-m-Y',
            'd/m/Y',
            'j F Y',
            'j M Y',
        ];

        foreach ($formats as $fmt) {
            try {
                $dt = Carbon::createFromFormat($fmt, $value);
                if ($dt) {
                    return $dt->toDateString();
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }

        // fallback: try Carbon parse
        try {
            $dt = Carbon::parse($value);
            return $dt->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
