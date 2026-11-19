<?php

namespace App\Filament\Support;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

/** Form fields shared by several admin resources. */
class Fields
{
    /** Name input that fills the slug while the slug is still untouched. */
    public static function name(string $field = 'name', string $label = 'Name'): TextInput
    {
        return TextInput::make($field)
            ->label($label)
            ->required()
            ->maxLength(255)
            ->live(onBlur: true)
            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                if (blank($get('slug')) || $get('slug') === Str::slug((string) $old)) {
                    $set('slug', Str::slug((string) $state));
                }
            });
    }

    public static function slug(string $prefix): TextInput
    {
        return TextInput::make('slug')
            ->label('URL slug')
            ->prefix(url($prefix).'/')
            ->helperText('Leave empty to generate it from the name. Changing it changes the page address.')
            ->maxLength(255)
            ->rules(['nullable', 'alpha_dash'])
            ->unique(ignoreRecord: true);
    }

    public static function image(string $field, string $directory, string $label = 'Image'): FileUpload
    {
        return FileUpload::make($field)
            ->label($label)
            ->image()
            ->disk('public')
            ->directory($directory)
            ->visibility('public')
            ->maxSize(8192)
            // Big phone/camera photos are scaled down in the browser before uploading:
            // faster uploads, and the website never has to handle 4000 px images.
            ->imageResizeMode('contain')
            ->imageResizeTargetWidth('1600')
            ->imageResizeTargetHeight('1600')
            ->imageResizeUpscale(false)
            ->imageEditor()
            ->helperText('JPG, PNG or WebP. Large photos are resized automatically.');
    }

    /** Image stored by a settings page (no model behind it). */
    public static function settingImage(string $field, string $label): FileUpload
    {
        return FileUpload::make($field)
            ->label($label)
            ->image()
            ->disk('public')
            ->directory('site')
            ->visibility('public')
            ->maxSize(8192)
            ->imageResizeMode('contain')
            ->imageResizeTargetWidth('2000')  // wide banners need a little more width
            ->imageResizeTargetHeight('2000')
            ->imageResizeUpscale(false);
    }

    public static function gallery(string $directory): FileUpload
    {
        return static::image('gallery', $directory, 'Additional images')
            ->multiple()
            ->reorderable()
            ->maxFiles(8)
            ->panelLayout('grid');
    }

    public static function seo(): Section
    {
        return Section::make('Search engine (SEO)')
            ->description('Optional. Used for Google results and link previews.')
            ->collapsed()
            ->schema([
                TextInput::make('meta_title')->maxLength(255)->helperText('Defaults to the name.'),
                Textarea::make('meta_description')->rows(2)->maxLength(500)->helperText('About 150 characters works best.'),
            ]);
    }

    public static function price(string $field, string $label): TextInput
    {
        return TextInput::make($field)
            ->label($label)
            ->numeric()
            ->minValue(0)
            ->maxValue(9999999)
            ->prefix('₹');
    }
}
