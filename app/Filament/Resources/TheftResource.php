<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TheftResource\Pages;
use App\Filament\Resources\TheftResource\RelationManagers;
use App\Models\Theft;
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

class TheftResource extends Resource
{
    protected static ?string $model = Theft::class;

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
                Forms\Components\Textarea::make('items')
                ->rows(3)
                ->cols(10)
                ->maxLength(15534)
                ->columnSpanFull(),
                
               Placeholder::make('Image')
                ->content(function ($record): HtmlString {
                $im = 'https://ecg.codepps.online/storage/'.str_replace(['[', ']', '"'], '', $record->images);
                return new HtmlString("<img style='width:70%;' src= '" . $im . "')>");
})->hiddenOn('create'),

// ViewColumn::make('images')
//     ->view('components.image-array-column'), // Reference a custom Blade view
                  
                FileUpload::make('images')
                ->label('Images')
                ->multiple()
                ->hiddenOn('view'), // Enable multiple file uploads
                //->image() // Restrict to image files
                //->directory('/thefts') // Specify the upload directory
                //->disk('storage') // Use the public disk
                //->imagePreviewHeight('100')
               // ->maxFiles(5) ,
                // ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateTheft),, // Limit the number of files
                
              
                
                
                //  Map::make('location')
                // ->defaultLocation(function(Theft $record){ 
                //   return [latitude:$record->lat, longitude:$record->lng]
                    
                // })
                // ->showMarker(true)
                // ->clickable(true)
                // ->tilesUrl("https://tile.openstreetmap.de/{z}/{x}/{y}.png")
                // ->zoom(12)
                // ->mutateDehydratedStateUsing(function (Theft $state) {
                // if (!($state instanceof Point))
                
                //     dd($state->lng);
                //     //return new Point($state['lat'], $state['lng']);

                //     //return $state;
                // })
                // ->afterStateHydrated(function ($state, callable $set) {
                //     if ($state instanceof Point) {
                //         /** @var Point $state */
                //         $set('location', ['lat' => $state->getLat(), 'lng' => $state->getLng()]);
                //     }
                // }),
                
                
                 Forms\Components\TextInput::make('location')
                ->label('Location')
                ->required()
                ->maxLength(255),
                Forms\Components\DatePicker::make('date_stolen')
                ->required()
                ->native(false),
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
                    ->default('pending')
                    ->options([
                        'pending'=> 'pending',
                        'completed'=> 'completed',
                        //'Neglected'=> 'Neglected'
                    ])
                    
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

               Tables\Columns\TextColumn::make('date_stolen')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('time_stolen')
                    ->dateTime('g:i a')
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
                     Tables\Actions\Action::make('View Theft')
                //      ->visible( function (Builder $query) {
                //     $roles = auth()->user()->roles->pluck('name')->toArray();
                //         if(in_array('super_admin', $roles))
                //         {
                //             return true;
                //         }
                //         else
                //         {
                //             return false;
                //         }
                // }
                //     )
                    ->label('View Theft')->url(fn(Theft $record): string => $record->id ? route('view.theft', ['tID' => $record->id]): '#')->openUrlInNewTab(),
                    //  Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListThefts::route('/'),
            'create' => Pages\CreateTheft::route('/create'),
            'edit' => Pages\EditTheft::route('/{record}/edit'),
        ];
    }
}
