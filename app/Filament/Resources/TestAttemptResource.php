<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestAttemptResource\Pages;
use App\Models\TestAttempt;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\{ViewAction, EditAction, DeleteAction};
use Filament\Tables\Actions\DeleteBulkAction;

class TestAttemptResource extends Resource
{
    protected static ?string $model = TestAttempt::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Testiranje';
    protected static ?string $navigationLabel = 'Rješeni testovi';
    protected static ?string $pluralModelLabel = 'Rješeni testovi';
    protected static ?string $modelLabel = 'Rješeni test';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return true;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Korisnik'),
                TextColumn::make('test.naziv')->label('Naziv testa'),
                TextColumn::make('ime_prezime')->label('Ime i prezime'),
                TextColumn::make('radno_mjesto')->label('Radno mjesto'),
                TextColumn::make('datum_rodjenja')->date()->label('Datum rođenja'),
                TextColumn::make('bodovi_osvojeni')->label('Bodovi'),
                TextColumn::make('rezultat')->label('Rezultat (%)')->suffix('%'),
                BadgeColumn::make('prolaz')
    ->label('Prolaz')
    ->enum([
        1 => 'Da',
        0 => 'Ne',
    ])
    ->colors([
        'success' => fn ($state) => (int) $state === 1, // zeleno za 1
        'danger'  => fn ($state) => (int) $state === 0, // crveno za 0
    ]),
                TextColumn::make('created_at')->dateTime('d.m.Y H:i')->label('Datum slanja'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make()
    ->url(fn (TestAttempt $record) => route('test-attempts.show', $record))
    ->openUrlInNewTab()
    ->label('Prikaži')
    ->icon('heroicon-o-eye'),
    Tables\Actions\DeleteAction::make('delete')
    ->label('Obriši')
    ->record(fn ($record) => $record)
    ->modalHeading('Obriši Test')
    ->modalSubheading('Jeste li sigurni da želite obrisati ovaj Test?')
    ->successNotificationTitle('Test je obrisan.'),
        ])
    ->bulkActions([
    DeleteBulkAction::make()
        ->modalHeading('Obriši Testove')
        ->modalSubheading('Jeste li sigurni da želite obrisati ove Testove?')
        ->successNotificationTitle('Testovi su obrisani.'),
               ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTestAttempts::route('/'),
        ];
    }
    public static function shouldRegisterNavigation(): bool
{
    return auth()->check() && auth()->user()->is_admin;
}

public static function canAccess(): bool
{
    return auth()->user()?->role === 'admin';
}

}