<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $navigationLabel = 'Pegawai';
    
    protected static ?string $modelLabel = 'Pegawai';
    
    protected static ?string $pluralModelLabel = 'Pegawai';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('employee_number')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('nik')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('name')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('position')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('department')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('status_kepegawaian')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('tahun_masuk')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\DatePicker::make('date_joined'),
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
                Forms\Components\Textarea::make('alamat')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('jenis_kelamin')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('marital_status')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('jml_anggota_keluarga')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('jumlah_anak')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Textarea::make('npwp')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('bank_account')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('bpjs_number')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('profile_photo_path')
                    ->maxLength(255)
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
                Forms\Components\TextInput::make('lembaga')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('aktif')
                    ->maxLength(255)
                    ->default(null),
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
                Tables\Columns\TextColumn::make('status_kepegawaian')
                    ->label('Status Kepegawaian')
                    ->searchable(),
                Tables\Columns\TextColumn::make('aktif')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Aktif' => 'success',
                        'Nonaktif' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('phone')
                    ->label('No HP')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('date_joined')
                    ->label('Tanggal Masuk')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('aktif')
                    ->options([
                        'Aktif' => 'Aktif',
                        'Nonaktif' => 'Nonaktif',
                    ]),
                Tables\Filters\SelectFilter::make('lembaga')
                    ->options(fn () => Employee::pluck('lembaga', 'lembaga')->unique()->toArray()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
