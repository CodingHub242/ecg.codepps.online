<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ElectricityRequestResource\Pages;
use App\Filament\Resources\ElectricityRequestResource\RelationManagers;
use App\Models\ElectricityRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ElectricityRequestResource extends Resource
{
    protected static ?string $model = ElectricityRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Requests Services';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fulname')
                 ->label('Full Name')
                ->numeric()
                ->sortable(),

                Tables\Columns\TextColumn::make('location')
                 ->label('Location')
                ->numeric()
                ->sortable(),

               Tables\Columns\TextColumn::make('created_at')
                    ->label('Sent On - Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date & Time Requested')
                    ->dateTime('F j, Y, g:i a') // Format the date and time
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn(string $state): string => match ($state) {
                    'pending' => 'warning',
                    'completed' => 'success',
                    //'Neglected' => 'danger',
                })
                ->sortable(),
            ])
            ->filters([
                //Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'pending',
                        'completed' => 'completed',
                    ])
                    ->attribute('status'),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->actions([
                 Tables\Actions\Action::make('View Request Details')
                    ->label('View Request Details')->url(fn(ElectricityRequest $record): string => $record->id ? route('view.light', ['tID' => $record->id]): '#')->openUrlInNewTab(),
                // Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListElectricityRequests::route('/'),
            'create' => Pages\CreateElectricityRequest::route('/create'),
            'edit' => Pages\EditElectricityRequest::route('/{record}/edit'),
        ];
    }
}
