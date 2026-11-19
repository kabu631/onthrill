<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Filament\Support\Fields;
use App\Filament\Support\LinksRecordsById;
use App\Models\Page;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;

class PageResource extends Resource
{
    use LinksRecordsById;

    protected static ?string $model = Page::class;

    // Admin URLs use the numeric id; the public site uses the slug.
    protected static ?string $recordRouteKeyName = 'id';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|\UnitEnum|null $navigationGroup = 'Website';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    /** Addresses already used by the website itself, so a page can never hide them. */
    public const RESERVED_SLUGS = [
        'admin', 'destinations', 'packages', 'booking', 'currency', 'contact-us', 'enquiry', 'gallery', 'search',
        'sitemap', 'sitemap.xml', 'robots.txt', 'llms.txt', 'login', 'register', 'logout', 'forgot-password', 'reset-password', 'my-account', 'storage',
        'livewire', 'filament', 'up', 'build', 'css', 'js', 'fonts',
    ];

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            Section::make('Page')->columnSpan(['lg' => 2])->schema([
                Fields::name('title', 'Title'),
                Fields::slug('')
                    ->rules(['nullable', 'alpha_dash', Rule::notIn(self::RESERVED_SLUGS)])
                    ->validationMessages(['not_in' => 'This address is already used by another part of the website. Please choose a different one.'])
                    ->helperText('The page address, e.g. about-us. Leave empty to generate it from the title.'),
                RichEditor::make('content')
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('pages')
                    ->extraInputAttributes(['style' => 'min-height: 24rem']),
            ]),
            Section::make('Publishing & menus')->columnSpan(['lg' => 1])->schema([
                Toggle::make('is_active')->label('Published')->default(true),
                Toggle::make('show_in_header')->label('Show in header menu')
                    ->helperText('Appears between “Gallery” and “Contact”.'),
                Toggle::make('show_in_footer')->label('Show in footer links')->default(true),
                TextInput::make('menu_label')->label('Menu label')->maxLength(60)
                    ->placeholder('Same as title')->helperText('Optional shorter name for menus, e.g. “About”.'),
                TextInput::make('sort_order')->label('Menu order')->numeric()->default(0)->helperText('Lower numbers appear first.'),
            ]),
            Fields::seo()->columnSpan(['lg' => 2]),
        ]);
    }

    public static function table(Table $table): Table
    {
        $locked = fn ($record) => auth()->user()->cannot('update', $record);

        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('title')->searchable()->weight('medium')->description(fn (Page $record) => '/'.$record->slug),
                ToggleColumn::make('is_active')->label('Published')->disabled($locked),
                ToggleColumn::make('show_in_header')->label('Header menu')->disabled($locked),
                ToggleColumn::make('show_in_footer')->label('Footer')->disabled($locked),
                TextColumn::make('updated_at')->since()->sortable(),
            ])
            ->recordActions([
                Action::make('view')->icon(Heroicon::OutlinedArrowTopRightOnSquare)->label('View')
                    ->url(fn (Page $record) => route('pages.show', $record))->openUrlInNewTab(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}
