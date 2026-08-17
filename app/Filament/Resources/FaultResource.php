<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaultResource\Pages;
use App\Filament\Resources\FaultResource\RelationManagers;
use App\Models\Fault;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Image;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Field;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FaultResource extends Resource
{
    protected static ?string $model = Fault::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Requests Services';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Forms\Components\TextInput::make('fulname')
                ->label('Full Name')
                ->required()
                ->maxLength(255),
                
                 Forms\Components\TextInput::make('fault')
                ->label('Fault Descrition')
                ->required()
                ->maxLength(255),
                
                 Placeholder::make('Image')
                ->content(function ($record): HtmlString {
                    $im = 'https://ecg.codepps.online/storage/'.str_replace(['[', ']', '"'], '', $record->images);
                return new HtmlString("<img style='width:70%;' src= '" . $im . "')>");
                })->hiddenOn('create'),
                
                
                // TextInput::make('lat')
                //     ->hiddenLabel()
                //     ->hidden(),
                
                // TextInput::make('lng')
                //     ->hiddenLabel()
                //     ->hidden(),
            //   Map::make('location')
            //     ->defaultLocation(latitude: 40.4168, longitude: -3.7038)
            //     ->showMarker(true)
            //     ->clickable(true)
            //     ->tilesUrl("https://tile.openstreetmap.de/{z}/{x}/{y}.png")
            //     ->zoom(12)
            //     ->mutateDehydratedStateUsing(function ($state) {
            //     if (!($state instanceof Point))
            //         return new Point($state['lat'], $state['lng']);

            //     return $state;
            // })
            //     ->afterStateHydrated(function ($state, callable $set) {
            //         if ($state instanceof Point) {
            //             /** @var Point $state */
            //             $set('location', ['lat' => $state->getLat(), 'lng' => $state->getLng()]);
            //         }
            //     }),
                Forms\Components\TextInput::make('location')
                ->label('Location')
                ->required()
                ->maxLength(255),
                  Forms\Components\Select::make('status')
                    // ->visible(function (Builder $query) {
                    //     $roles = auth()->user()->roles->pluck('name')->toArray();
                    //     //dd($roles);
                    //     if (in_array('panel_user', $roles) || in_array('organizer', $roles)) {
                    //         return false;
                    //     } else {
                    //         return true;
                    //     }
                    // })
                    ->default('Pending')
                    ->options([
                        'pending'=> 'pending',
                        'completed'=> 'completed',
                        //'Neglected'=> 'Neglected'
                    ]),
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
                    ->label('Date & Time Sent')
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
                      Tables\Actions\Action::make('View Fault Details')
                    ->label('View Fault Details')->url(fn(Fault $record): string => $record->id ? route('view.fault', ['tID' => $record->id]): '#')->openUrlInNewTab(),
                    // Tables\Actions\ViewAction::make(),
                    // Tables\Actions\EditAction::make(),
                //Tables\Actions\Action::make('viewOrders')->label('View Orders')->url(fn(Event $record): string => route('event.orders', $record))->openUrlInNewTab(), // Action::make('create_ticket')
               
                //     ->url(route('createTicket'))
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
            'index' => Pages\ListFaults::route('/'),
            'create' => Pages\CreateFault::route('/create'),
            'edit' => Pages\EditFault::route('/{record}/edit'),
        ];
    }
}
