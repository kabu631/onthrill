<?php

namespace App\Filament\Resources\Testimonials;

use App\Filament\Resources\Testimonials\Pages\ManageTestimonials;
use App\Filament\Support\Fields;
use App\Models\Testimonial;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
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

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static string|\UnitEnum|null $navigationGroup = 'Website';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)->schema([
                TextInput::make('name')->required()->maxLength(120),
                TextInput::make('location')->label('City or role')->placeholder('e.g. Delhi, or Business owner')->maxLength(120),
            ]),
            Textarea::make('content')->label('Review')->required()->rows(4)->maxLength(1000),
            Grid::make(3)->schema([
                Select::make('rating')->options([5 => '★★★★★', 4 => '★★★★', 3 => '★★★', 2 => '★★', 1 => '★'])->default(5)->required()->native(false),
                TextInput::make('sort_order')->numeric()->default(0),
                Toggle::make('is_active')->label('Show on website')->default(true)->inline(false),
            ]),
            Fields::image('photo', 'testimonials', 'Photo (optional)')->avatar(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('photo')->disk('public')->circular()->label(''),
                TextColumn::make('name')->searchable()->weight('medium')->description(fn (Testimonial $record) => $record->location),
                TextColumn::make('content')->label('Review')->limit(90)->wrap(),
                TextColumn::make('rating')->formatStateUsing(fn ($state) => str_repeat('★', (int) $state))->color('warning'),
                ToggleColumn::make('is_active')->label('Visible')->disabled(fn ($record) => auth()->user()->cannot('update', $record)),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageTestimonials::route('/')];
    }
}
