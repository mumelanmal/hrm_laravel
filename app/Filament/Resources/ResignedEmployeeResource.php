<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResignedEmployeeResource\Pages;
use App\Filament\Resources\ResignedEmployeeResource\RelationManagers;
use App\Models\ResignedEmployee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ResignedEmployeeResource extends Resource
{
    protected static ?string $model = ResignedEmployee::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-start-on-rectangle';
    
    protected static ?string $navigationLabel = 'Pegawai Resign';
    
    protected static ?string $modelLabel = 'Pegawai Resign';
    
    protected static ?string $pluralModelLabel = 'Pegawai Resign';
    
    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        // Hide this resource; resigned_employees table has been consolidated into employees via Soft Deletes.
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('employee_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('employee_number')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('amanah_pokok')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('status_kepegawaian')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('nik')
                    ->maxLength(100)
                    ->default(null),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(100)
                    ->default(null),
                Forms\Components\TextInput::make('kesehatan')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('pendidikan_terakhir')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('ijazah_tambahan')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('position')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('lembaga')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('aktif')
                    ->maxLength(20)
                    ->default(null),
                Forms\Components\DatePicker::make('date_joined'),
                Forms\Components\TextInput::make('tahun_masuk')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\DatePicker::make('date_resigned'),
                Forms\Components\TextInput::make('golongan')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('pangkat')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('job_level')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('place_of_birth')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\DatePicker::make('date_of_birth'),
                Forms\Components\TextInput::make('jenis_kelamin')
                    ->maxLength(20)
                    ->default(null),
                Forms\Components\TextInput::make('marital_status')
                    ->maxLength(50)
                    ->default(null),
                Forms\Components\TextInput::make('jml_anggota_keluarga')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('jumlah_anak')
                    ->numeric()
                    ->default(null),
                Forms\Components\Textarea::make('alamat')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('alasan_resign')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_number')
                    ->label('NIPY')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('position')
                    ->label('Jabatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lembaga')
                    ->label('Lembaga')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_resigned')
                    ->label('Tanggal Resign')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('alasan_resign')
                    ->label('Alasan')
                    ->limit(30)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('lembaga')
                    ->options(fn () => ResignedEmployee::pluck('lembaga', 'lembaga')->unique()->toArray()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date_resigned', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResignedEmployees::route('/'),
            'create' => Pages\CreateResignedEmployee::route('/create'),
            'edit' => Pages\EditResignedEmployee::route('/{record}/edit'),
        ];
    }
}
