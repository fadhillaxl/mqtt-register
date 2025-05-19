<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeviceResource\Pages;
use App\Models\Device;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DeviceResource extends Resource
{
    protected static ?string $model = Device::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $navigationGroup = 'IoT Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Device Information')
                    ->schema([
                        Forms\Components\TextInput::make('mac_address')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->label('MAC Address')
                            ->placeholder('00:11:22:33:44:55')
                            ->regex('/^([0-9A-F]{2}:){5}[0-9A-F]{2}$/')
                            ->formatStateUsing(fn ($state) => strtoupper($state))
                            ->helperText('Format: XX:XX:XX:XX:XX:XX (where X is a hexadecimal digit)'),
                            
                        Forms\Components\TextInput::make('name')
                            ->label('Device Name')
                            ->placeholder('Living Room ESP32'),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Responsibility')
                    ->schema([
                        Forms\Components\Select::make('person_in_charge_id')
                            ->relationship('personInCharge', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->unique('person_in_charges', 'email')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('department')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('position')
                                    ->maxLength(255),
                            ])
                            ->label('Person In Charge'),
                    ]),
                    
                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\KeyValue::make('metadata')
                            ->label('Metadata')
                            ->addButtonLabel('Add Field')
                            ->keyLabel('Key')
                            ->valueLabel('Value')
                            ->columnSpan('full'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('mac_address')
                    ->label('MAC Address')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Device Name')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('personInCharge.name')
                    ->label('Person In Charge')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('last_seen_at')
                    ->label('Last Seen')
                    ->dateTime()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registered At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),
                Tables\Filters\SelectFilter::make('person_in_charge_id')
                    ->label('Person In Charge')
                    ->relationship('personInCharge', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDevices::route('/'),
            'create' => Pages\CreateDevice::route('/create'),
            'edit' => Pages\EditDevice::route('/{record}/edit'),
        ];
    }
} 