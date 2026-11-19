<?php

namespace App\Filament\Resources\Packages;

use App\Filament\Resources\Packages\Pages\CreatePackage;
use App\Filament\Resources\Packages\Pages\EditPackage;
use App\Filament\Resources\Packages\Pages\ListPackages;
use App\Filament\Support\Fields;
use App\Filament\Support\LinksRecordsById;
use App\Models\Package;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
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
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class PackageResource extends Resource
{
    use LinksRecordsById;

    protected static ?string $model = Package::class;

    // Admin URLs use the numeric id; the public site uses the slug.
    protected static ?string $recordRouteKeyName = 'id';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;

    protected static string|\UnitEnum|null $navigationGroup = 'Tours';

    protected static ?string $navigationLabel = 'Packages';

    protected static ?string $modelLabel = 'tour package';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make([
                    Tabs::make('package')->persistTabInQueryString()->tabs([
                        Tab::make('Details')->icon(Heroicon::OutlinedDocumentText)->schema([
                            Fields::name(),
                            Fields::slug('/packages'),
                            Grid::make(4)->schema([
                                TextInput::make('duration_days')->label('Days')->numeric()->minValue(1)->maxValue(60)->required()->default(1),
                                TextInput::make('duration_nights')->label('Nights')->numeric()->minValue(0)->maxValue(60)->default(0),
                                TextInput::make('start_point')->label('Starts in')->placeholder('Kathmandu')->maxLength(120),
                                TextInput::make('end_point')->label('Ends in')->placeholder('Kathmandu')->maxLength(120),
                            ]),
                            Textarea::make('summary')->rows(2)->maxLength(400)
                                ->helperText('One or two sentences, shown on package cards and in search results.'),
                            RichEditor::make('overview')->label('About this tour')
                                ->toolbarButtons([['bold', 'italic', 'link'], ['h3', 'bulletList', 'orderedList'], ['undo', 'redo']]),
                            Repeater::make('highlights')->simple(TextInput::make('item')->required()->maxLength(160))
                                ->addActionLabel('Add highlight')->reorderable()->defaultItems(0),
                        ]),
                        Tab::make('Itinerary')->icon(Heroicon::OutlinedCalendarDays)->schema([
                            Repeater::make('itinerary')->hiddenLabel()
                                ->helperText('One item per day, in order. They are numbered Day 1, Day 2… on the website.')
                                ->schema([
                                    TextInput::make('title')->required()->maxLength(160),
                                    Textarea::make('description')->rows(2)->maxLength(1000),
                                ])
                                ->itemLabel(fn (array $state) => $state['title'] ?? null)
                                ->addActionLabel('Add day')->reorderable()->collapsible()->cloneable()->defaultItems(0),
                        ]),
                        Tab::make('Inclusions')->icon(Heroicon::OutlinedCheckCircle)->schema([
                            Grid::make(2)->schema([
                                Repeater::make('inclusions')->label('Included')->simple(TextInput::make('item')->required()->maxLength(200))
                                    ->addActionLabel('Add')->reorderable()->defaultItems(0),
                                Repeater::make('exclusions')->label('Not included')->simple(TextInput::make('item')->required()->maxLength(200))
                                    ->addActionLabel('Add')->reorderable()->defaultItems(0),
                            ]),
                            RichEditor::make('notes')->label('Before you go')
                                ->helperText('Documents, permits, health and altitude advice.')
                                ->toolbarButtons([['bold', 'italic', 'link'], ['bulletList'], ['undo', 'redo']]),
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
                            Fields::image('image', 'packages', 'Main photo'),
                            Fields::gallery('packages'),
                        ]),
                    ]),
                    Fields::seo(),
                ])->columnSpan(['lg' => 2]),

                Group::make([
                    Section::make('Publishing')->schema([
                        Toggle::make('is_active')->label('Visible on website')->default(true),
                        Toggle::make('is_featured')->label('Show on homepage'),
                        Select::make('tour_category_id')->label('Tour type')
                            ->relationship('category', 'name')->searchable()->preload()
                            ->createOptionForm([Fields::name(), Fields::slug('/packages/type')]),
                        Select::make('destinations')->label('Places visited')
                            ->relationship('destinations', 'name')->multiple()->searchable()->preload()
                            ->helperText('The package is listed on each of these destination pages.'),
                        TextInput::make('sort_order')->numeric()->default(0)->helperText('Lower numbers appear first.'),
                    ]),
                    Section::make('Price per person')->schema([
                        Fields::price('price_inr', 'Price (INR)')
                            ->helperText('Leave empty for “Price on request”.'),
                        Fields::price('sale_price_inr', 'Offer price (INR)')->lt('price_inr')->helperText('Optional.'),
                        TextInput::make('price_npr')->label('Price (NPR)')->numeric()->minValue(0)->prefix('NPR')
                            ->helperText('Leave empty to work it out from the INR price (rate in Site Settings).'),
                        TextInput::make('sale_price_npr')->label('Offer price (NPR)')->numeric()->minValue(0)->prefix('NPR'),
                    ]),
                    Section::make('Trip facts')->schema([
                        TextInput::make('best_season')->placeholder('March to June, September to November')->maxLength(120),
                        TextInput::make('group_size')->placeholder('2 to 12')->maxLength(60),
                        Select::make('difficulty')->options(Package::DIFFICULTIES),
                        TextInput::make('max_altitude_m')->label('Highest point (metres)')->numeric()->minValue(0)->maxValue(9000),
                    ]),
                ])->columnSpan(['lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('category'))
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('image')->disk('public')->square()->label(''),
                TextColumn::make('name')->searchable()->sortable()->wrap()->weight('medium')
                    ->description(fn (Package $record) => trim(($record->category?->name ?? '').' · '.$record->durationLabel(), ' ·')),
                TextColumn::make('price_inr')->label('Price')->sortable()
                    ->formatStateUsing(fn ($state, Package $record) => inr($record->price('INR')))
                    ->placeholder('On request')
                    ->description(fn (Package $record) => $record->isOnRequest() ? null : npr($record->price('NPR'))),
                ToggleColumn::make('is_featured')->label('Homepage')->disabled(fn ($record) => auth()->user()->cannot('update', $record)),
                ToggleColumn::make('is_active')->label('Visible')->disabled(fn ($record) => auth()->user()->cannot('update', $record)),
                TextColumn::make('updated_at')->since()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tour_category_id')->label('Tour type')->relationship('category', 'name')->preload(),
                TernaryFilter::make('is_active')->label('Visible'),
                TernaryFilter::make('is_featured')->label('On homepage'),
            ])
            ->recordActions([
                Action::make('view')->icon(Heroicon::OutlinedArrowTopRightOnSquare)->label('View')
                    ->url(fn (Package $record) => $record->url())->openUrlInNewTab(),
                EditAction::make(),
                ReplicateAction::make()
                    ->excludeAttributes(['slug'])
                    ->mutateRecordDataUsing(fn (array $data) => [...$data, 'name' => $data['name'].' (copy)', 'is_active' => false]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('show')->visible(fn () => auth()->user()->can('updateAny', static::getModel()))->label('Make visible')->icon(Heroicon::OutlinedEye)
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => true])),
                    BulkAction::make('hide')->visible(fn () => auth()->user()->can('updateAny', static::getModel()))->label('Hide')->icon(Heroicon::OutlinedEyeSlash)
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => false])),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPackages::route('/'),
            'create' => CreatePackage::route('/create'),
            'edit' => EditPackage::route('/{record}/edit'),
        ];
    }
}
