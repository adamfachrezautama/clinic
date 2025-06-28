<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Clinic;
use App\Models\Specialization;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->hidden(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord) // hidden saat update
                    ->maxLength(255),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->label('Password')
                    ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null) // hanya update jika diisi
                    ->required(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord) // hanya required saat create
                    ->maxLength(255),
                TextInput::make('role')
                    ->required()
                    ->maxLength(255),
                    // ->default('patient'),
                TextInput::make('google_id')
                ->hidden(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord) // hidden saat update
                    ->maxLength(255),
                TextInput::make('ktp_number')
                    ->maxLength(255),
                DatePicker::make('birth_date'),
                TextInput::make('gender')
                    ->maxLength(255),
                Select::make('registration_type')
                    ->options([
                        'patient' => 'Patient',
                        'doctor' => 'Doctor',
                    ])
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(255),
                TextInput::make('address')
                    ->maxLength(255),
                 Select::make('status')
                    ->options([
                        'offline' => 'offline',
                        'online' => 'online',
                    ])
                    ->required(),
                TextInput::make('certification')
                    ->maxLength(255),
                FileUpload::make('document')->acceptedFileTypes(['application/pdf'])
                    ->minSize(512)
                    ->maxSize(1024),
                TextInput::make('telemedicine_fee')
                    ->tel()
                    ->required()
                    ->numeric()
                    ->default(0),
                FileUpload::make('photo')
                    ->label('Foto')
                    ->image()
                    ->directory('user/photos') // folder tempat penyimpanan
                    ->visibility('public') // agar bisa diakses di /storage/user/
                    ->preserveFilenames() // opsional
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg'])
                    ->maxSize(2048)
                    ->imagePreviewHeight('250')
                    ->columnSpanFull()
                    ->dehydrated(fn ($state) => filled($state)) // pastikan hanya disimpan jika ada isinya
                    ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord), // hanya required saat create
                TextInput::make('chat_fee')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('start_time'),
                TextInput::make('end_time'),
                Select::make('clinic_id')
                    ->label('Clinic')
                    ->options(Clinic::all()->pluck('name','id'))
                    ->searchable(),
                Select::make('specialization_id')
                    ->label('specialization')
                    ->options(Specialization::all()->pluck('name','id'))
                    ->searchable(),
                 Select::make('status_registration')
                    ->options([
                        'pending' => 'pending',
                        'verified' => 'verified',
                        'rejected' => 'rejected',
                    ])
                    ->required(),

                // relasi dinamis tergantung input clinic
                // Forms\Components\Select::make('specialization_id')
                //     ->label('spcecialization')
                //     ->options(function(callable $get){
                //         $clinicId = $get('clinic_id');
                //         return \App\Models\Specialization::where('clinic_id',$clinicId)
                //         ->pluck('name','id');
                //     })
                //     ->reactive()
                //     ->numeric(),


                // kalau pakai relasi seperti belongsTo, bisa gunakan relationship
                // Forms\Components\Select::make('clinic_id')
                //         ->relationship('clinic','name')
                //         ->searchable()
                //         ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role')
                    ->searchable(),
                Tables\Columns\TextColumn::make('google_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ktp_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('birth_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('gender')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('certification')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telemedicine_fee')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('photo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('chat_fee')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time'),
                Tables\Columns\TextColumn::make('end_time'),
                Tables\Columns\TextColumn::make('clinic_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('specialization_id')
                    ->numeric()
                    ->sortable(),
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
