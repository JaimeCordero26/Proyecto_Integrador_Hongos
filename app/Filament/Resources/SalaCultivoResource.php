<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalaCultivoResource\Pages;
use App\Filament\Resources\SalaCultivoResource\RelationManagers;
use App\Models\SalaCultivo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SalaCultivoResource extends Resource
{
    protected static ?string $model = SalaCultivo::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre_sala')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('descripcion')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('proposito')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_sala')
                    ->searchable(),
                Tables\Columns\TextColumn::make('proposito')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListSalaCultivos::route('/'),
            'create' => Pages\CreateSalaCultivo::route('/create'),
            'view' => Pages\ViewSalaCultivo::route('/{record}'),
            'edit' => Pages\EditSalaCultivo::route('/{record}/edit'),
        ];
    }
}
