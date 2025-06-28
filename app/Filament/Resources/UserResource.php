<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\Clinic;
use App\Models\Specialization;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('email_verified_at'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->label('Password')
                    ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null) // hanya update jika diisi
                    ->required(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord) // hanya required saat create
                    ->maxLength(255),
                Forms\Components\TextInput::make('role')
                    ->required()
                    ->maxLength(255),
                    // ->default('patient'),
                Forms\Components\TextInput::make('google_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('ktp_number')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('birth_date'),
                Forms\Components\TextInput::make('gender')
                    ->maxLength(255),
                Forms\Components\Select::make('registration_type')
                    ->options([
                        'patient' => 'Patient',
                        'doctor' => 'Doctor',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')
                    ->maxLength(255),
                 Forms\Components\Select::make('status')
                    ->options([
                        'offline' => 'offline',
                        'online' => 'online',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('certification')
                    ->maxLength(255),
                Forms\Components\TextInput::make('telemedicine_fee')
                    ->tel()
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('photo')
                    ->maxLength(255),
                Forms\Components\TextInput::make('chat_fee')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('start_time'),
                Forms\Components\TextInput::make('end_time'),
                Forms\Components\Select::make('clinic_id')
                    ->label('Clinic')
                    ->options(Clinic::all()->pluck('name','id'))
                    ->searchable(),

                Forms\Components\Select::make('specialization_id')
                    ->label('specialization')
                    ->options(Specialization::all()->pluck('name','id'))
                    ->searchable(),
                 Forms\Components\Select::make('status_registration')
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
