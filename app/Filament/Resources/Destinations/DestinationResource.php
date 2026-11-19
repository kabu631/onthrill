<?php

namespace App\Filament\Resources\Destinations;

use App\Filament\Resources\Destinations\Pages\CreateDestination;
use App\Filament\Resources\Destinations\Pages\EditDestination;
use App\Filament\Resources\Destinations\Pages\ListDestinations;
use App\Filament\Support\Fields;
use App\Filament\Support\LinksRecordsById;
use App\Models\Destination;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DestinationResource extends Resource
{
    use LinksRecordsById;

    protected static ?string $model = Destination::class;

    // Admin URLs use the numeric id; the public site uses the slug.
    protected static ?string $recordRouteKeyName = 'id';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static string|\UnitEnum|null $navigationGroup = 'Tours';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make([
                    Tabs::make('destination')->persistTabInQueryString()->tabs([
                        Tab::make('Details')->icon(Heroicon::OutlinedDocumentText)->schema([
                            Fields::name(),
                            Fields::slug('/destinations'),
                            Grid::make(2)->schema([
                                Select::make('country')->options(array_combine(array_keys(Destination::COUNTRIES), array_keys(Destination::COUNTRIES)))->required()
                                    ->helperText('The website menu groups destinations by country.'),
                                TextInput::make('region')->placeholder('Kathmandu, Bagmati Province')->maxLength(150),
                            ]),
                            Textarea::make('summary')->rows(2)->maxLength(400)
                                ->helperText('One or two plain sentences: shown on cards, under the page title and in Google results.'),
                            RichEditor::make('description')
                                ->fileAttachmentsDisk('public')->fileAttachmentsDirectory('destinations/content'),
                        ]),
                        Tab::make('Key facts')->icon(Heroicon::OutlinedInformationCircle)->schema([
                            Grid::make(2)->schema([
                                TextInput::make('altitude_m')->label('Altitude (metres)')->numeric()->minValue(0)->maxValue(9000),
                                TextInput::make('best_time')->label('Best time to visit')->maxLength(200),
                                TextInput::make('opening_hours')->maxLength(200),
                                TextInput::make('nearest_airport')->maxLength(200),
                            ]),
                            Textarea::make('how_to_reach')->rows(3)->maxLength(1000),
                            Grid::make(2)->schema([
                                TextInput::make('latitude')->numeric()->minValue(-90)->maxValue(90)->placeholder('27.7104'),
                                TextInput::make('longitude')->numeric()->minValue(-180)->maxValue(180)->placeholder('85.3488'),
                            ]),
                            Section::make()->compact()->schema([])
                                ->description('Coordinates power the map link and help search engines place the destination. In Google Maps, right-click the place and click the numbers to copy them.'),
                        ]),
                        Tab::make('FAQs')->icon(Heroicon::OutlinedQuestionMarkCircle)->schema([
                            Repeater::make('faqs')->hiddenLabel()
                                ->helperText('Short questions and answers. They also appear in Google results.')
                                ->schema([
                                    TextInput::make('question')->required()->maxLength(200),
                                    Textarea::make('answer')->required()->rows(2)->maxLength(1000),
                                ])
                                ->itemLabel(fn (array $state) => $state['question'] ?? null)
                                ->addActionLabel('Add question')->reorderable()->collapsible()->defaultItems(0),
                        ]),
                        Tab::make('Photos')->icon(Heroicon::OutlinedPhoto)->schema([
                            Fields::image('image', 'destinations', 'Main photo'),
                            Grid::make(2)->schema([
                                TextInput::make('image_credit')->label('Photo credit')->maxLength(255)
                                    ->helperText('Required for photos under a Creative Commons licence, e.g. “Photo: Name / Wikimedia Commons, CC BY-SA 4.0”.'),
                                TextInput::make('image_credit_url')->label('Credit link')->url()->maxLength(255),
                            ]),
                            Fields::gallery('destinations'),
                        ]),
                    ]),
                    Fields::seo(),
                ])->columnSpan(['lg' => 2]),

                Group::make([
                    Section::make('Publishing')->schema([
                        Toggle::make('is_active')->label('Visible on website')->default(true),
                        Toggle::make('is_featured')->label('Show on homepage'),
                        TextInput::make('sort_order')->numeric()->default(0)->helperText('Menu order: lower first.'),
                    ]),
                ])->columnSpan(['lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount('packages'))
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('image')->disk('public')->square()->label(''),
                TextColumn::make('name')->searchable()->sortable()->weight('medium')
                    ->description(fn (Destination $record) => $record->region),
                TextColumn::make('country')->badge()->sortable(),
                TextColumn::make('packages_count')->label('Packages')->badge()->color('gray'),
                ToggleColumn::make('is_featured')->label('Homepage')->disabled(fn ($record) => auth()->user()->cannot('update', $record)),
                ToggleColumn::make('is_active')->label('Visible')->disabled(fn ($record) => auth()->user()->cannot('update', $record)),
            ])
            ->filters([
                SelectFilter::make('country')->options(array_combine(array_keys(Destination::COUNTRIES), array_keys(Destination::COUNTRIES))),
            ])
            ->recordActions([
                Action::make('view')->icon(Heroicon::OutlinedArrowTopRightOnSquare)->label('View')
                    ->url(fn (Destination $record) => $record->url())->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDestinations::route('/'),
            'create' => CreateDestination::route('/create'),
            'edit' => EditDestination::route('/{record}/edit'),
        ];
    }
}
