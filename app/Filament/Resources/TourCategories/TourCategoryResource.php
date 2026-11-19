<?php

namespace App\Filament\Resources\TourCategories;

use App\Filament\Resources\TourCategories\Pages\ManageTourCategories;
use App\Filament\Support\Fields;
use App\Filament\Support\LinksRecordsById;
use App\Models\TourCategory;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class TourCategoryResource extends Resource
{
    use LinksRecordsById;

    protected static ?string $model = TourCategory::class;

    // Admin URLs use the numeric id; the public site uses the slug.
    protected static ?string $recordRouteKeyName = 'id';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolderOpen;

    protected static string|\UnitEnum|null $navigationGroup = 'Tours';

    protected static ?string $navigationLabel = 'Tour Types & Menu';

    protected static ?string $modelLabel = 'tour type';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)->schema([
                Fields::name(),
                Fields::slug('/packages/type'),
            ]),
            Textarea::make('description')->rows(3)->maxLength(1000)->helperText('Shown at the top of the tour type page.'),
            Fields::image('image', 'tour-types'),
            Grid::make(3)->schema([
                TextInput::make('sort_order')->numeric()->default(0)->helperText('Menu order: lower first.'),
                Toggle::make('is_active')->label('Visible on website')->default(true)->inline(false),
                Toggle::make('show_in_menu')->label('Show in the Tour Packages menu')->default(true)->inline(false),
            ]),
            Fields::seo(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount('packages'))
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('image')->disk('public')->circular()->label(''),
                TextColumn::make('name')->searchable()->weight('medium')->description(fn (TourCategory $record) => '/packages/type/'.$record->slug),
                TextColumn::make('packages_count')->label('Packages')->badge(),
                ToggleColumn::make('show_in_menu')->label('In menu')->disabled(fn ($record) => auth()->user()->cannot('update', $record)),
                ToggleColumn::make('is_active')->label('Visible')->disabled(fn ($record) => auth()->user()->cannot('update', $record)),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->modalDescription('Packages of this type will be kept but left without a type.'),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageTourCategories::route('/')];
    }
}
