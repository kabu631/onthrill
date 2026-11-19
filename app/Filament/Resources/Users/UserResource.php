<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use App\Support\Access;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Users';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Account')->columns(2)->schema([
                TextInput::make('name')->required()->maxLength(120),
                TextInput::make('email')->email()->required()->maxLength(160)->unique(ignoreRecord: true),
                TextInput::make('phone')->tel()->maxLength(20),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->minLength(8)
                    ->required(fn (string $operation) => $operation === 'create')
                    ->dehydrated(fn (?string $state) => filled($state))
                    ->dehydrateStateUsing(fn (string $state) => Hash::make($state))
                    ->helperText(fn (string $operation) => $operation === 'edit' ? 'Leave empty to keep the current password.' : null),
            ]),

            Section::make('Access')
                ->description('Roles decide what this person can see and do in the admin panel. Customers have no admin access.')
                // Handing out roles is itself a permission, so staff cannot escalate each other.
                ->visible(fn () => auth()->user()->allows('roles.update'))
                ->schema([
                    Select::make('roles')
                        ->label('Roles')
                        ->relationship('roles', 'name', fn (Builder $query) => $query
                            // Only a Super Admin can create another Super Admin.
                            ->when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->where('name', '!=', Access::SUPER_ADMIN)))
                        ->getOptionLabelFromRecordUsing(fn ($record) => Access::roleLabel($record->name))
                        ->multiple()
                        ->preload()
                        ->placeholder('Customer (no admin access)')
                        // Nobody changes their own roles: it prevents locking yourself out.
                        ->disabled(fn (?User $record) => $record?->is(auth()->user()))
                        ->helperText(fn (?User $record) => $record?->is(auth()->user())
                            ? 'You cannot change your own roles.'
                            : 'Manage what each role can do under Settings > Roles & Permissions.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with('roles')->withCount('bookings'))
            ->columns([
                TextColumn::make('name')->searchable(['name', 'email', 'phone'])->weight('medium')->description(fn (User $record) => $record->email),
                TextColumn::make('phone')->placeholder('-'),
                TextColumn::make('roles.name')->label('Roles')->badge()
                    ->formatStateUsing(fn (string $state) => Access::roleLabel($state))
                    ->color(fn (string $state) => match ($state) {
                        Access::SUPER_ADMIN => 'danger',
                        Access::CUSTOMER => 'gray',
                        default => 'primary',
                    }),
                TextColumn::make('bookings_count')->label('Bookings')->badge()->color('gray'),
                TextColumn::make('created_at')->label('Joined')->date('d M Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('roles')->label('Role')->relationship('roles', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => Access::roleLabel($record->name))->preload(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
