<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonInChargeResource\Pages;
use App\Models\PersonInCharge;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PersonInChargeResource extends Resource
{
    protected static ?string $model = PersonInCharge::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'IoT Management';

    protected static ?string $modelLabel = 'Person In Charge';

    protected static ?string $pluralModelLabel = 'People In Charge';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Work Information')
                    ->schema([
                        Forms\Components\TextInput::make('department')
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('position')
                            ->maxLength(255),
                            
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('department')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('position')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('devices_count')
                    ->counts('devices')
                    ->label('Devices'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('department')
                    ->options(fn () => PersonInCharge::query()
                        ->distinct()
                        ->pluck('department', 'department')
                        ->toArray()
                    ),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersonInCharges::route('/'),
            'create' => Pages\CreatePersonInCharge::route('/create'),
            'edit' => Pages\EditPersonInCharge::route('/{record}/edit'),
        ];
    }
} 