<?php

namespace App\Filament\Resources\Roles;

use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\EditRole;
use App\Filament\Resources\Roles\Pages\ListRoles;
use App\Support\Access;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Roles & Permissions';

    protected static ?int $navigationSort = 3;

    /** Form field holding one area's ticked abilities, e.g. "area_packages". */
    public static function field(string $area): string
    {
        return 'area_'.$area;
    }

    public static function form(Schema $schema): Schema
    {
        $areas = [];
        foreach (Access::AREAS as $area => [$label, $abilities]) {
            $areas[] = CheckboxList::make(static::field($area))
                ->label($label)
                ->options(collect($abilities)->mapWithKeys(fn ($ability) => [$ability => Access::ABILITY_LABELS[$ability]])->all())
                ->columns(4)
                ->gridDirection('row');
        }

        return $schema->columns(1)->components([
            Section::make('Role')->schema([
                TextInput::make('name')
                    ->label('Role name')
                    ->placeholder('e.g. Tour Guide')
                    ->required()
                    ->maxLength(60)
                    ->formatStateUsing(fn (?string $state) => $state ? Access::roleLabel($state) : null),
            ]),
            Section::make('Permissions')
                ->description('Tick what people with this role may do. “View” is needed to see a section at all.')
                ->schema($areas),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount(['permissions', 'users']))
            ->defaultSort('id')
            ->paginated(false)
            ->columns([
                TextColumn::make('name')->label('Role')->weight('medium')
                    ->formatStateUsing(fn (string $state) => Access::roleLabel($state))
                    ->description(fn (Role $record) => match ($record->name) {
                        Access::SUPER_ADMIN => 'Built-in · full access to everything',
                        Access::CUSTOMER => 'Built-in · website customers, no admin access',
                        default => null,
                    }),
                TextColumn::make('permissions_count')->label('Permissions')->badge()
                    ->formatStateUsing(fn ($state, Role $record) => $record->name === Access::SUPER_ADMIN ? 'All' : $state),
                TextColumn::make('users_count')->label('Users')->badge()->color('gray'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->modalDescription('People who only have this role will lose their admin access.'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }
}
