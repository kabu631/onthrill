<?php

namespace App\Filament\Resources\GalleryImages;

use App\Filament\Resources\GalleryImages\Pages\ManageGalleryImages;
use App\Filament\Support\Fields;
use App\Models\GalleryImage;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class GalleryImageResource extends Resource
{
    protected static ?string $model = GalleryImage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|\UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $navigationLabel = 'Gallery';

    protected static ?string $modelLabel = 'photo';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Fields::image('image', 'gallery', 'Photo')->required(),
            TextInput::make('title')->label('Caption (optional)')->maxLength(150),
            TextInput::make('credit')->label('Photo credit')->maxLength(255)
                ->helperText('Needed for Creative Commons photos, e.g. “Name / Wikimedia Commons, CC BY-SA 4.0”. Leave empty for your own photos.'),
            TextInput::make('credit_url')->label('Credit link')->url()->maxLength(255),
            TextInput::make('sort_order')->numeric()->default(0),
            Toggle::make('is_active')->label('Show on website')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->contentGrid(['md' => 3, 'xl' => 4])
            ->columns([
                Stack::make([
                    ImageColumn::make('image')->disk('public')->imageHeight(180)->extraImgAttributes(['class' => 'w-full rounded-lg object-cover']),
                    TextColumn::make('title')->placeholder('No caption')->limit(40),
                    ToggleColumn::make('is_active')->label('Visible')->disabled(fn ($record) => auth()->user()->cannot('update', $record)),
                ])->space(2),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageGalleryImages::route('/')];
    }
}
