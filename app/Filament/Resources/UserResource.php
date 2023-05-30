<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\RelationManagers\RolesRelationManager;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 1;

    protected static function getNavigationGroup(): ?string
    {
        return __('Administrative');
    }

    protected static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255),

                    TextInput::make('password')
                        ->password()
                        ->maxLength(255)
                        ->dehydrateStateUsing(
                            static fn (null|string $state): null|string => filled($state) ? Hash::make($state) : null
                        )
                        ->dehydrated(static fn (null|string $state): bool => filled($state))
                        ->required(static fn (Page $livewire): bool => $livewire instanceof CreateUser)
                        ->label(
                            static fn (
                                Page $livewire
                            ): string => ($livewire instanceof EditUser) ? 'New Password' : 'Password'
                        ),

                    Select::make('roles')
                        ->multiple()
                        ->relationship('roles', 'name')
                        ->searchable()
                        ->preload(),

                    Select::make('permissions')
                        ->multiple()
                        ->relationship('permissions', 'name')
                        ->searchable()
                        ->preload(),

                    Forms\Components\Toggle::make('access_admin'),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),

                Auth::user()->can('update user') ?
                    Tables\Columns\ToggleColumn::make('access_admin')
                        ->sortable()
                        ->searchable() :
                    IconColumn::make('access_admin')
                        ->boolean()
                        ->trueIcon('heroicon-o-check')
                        ->falseIcon('heroicon-o-x')
                        ->sortable()
                        ->searchable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d-M-Y')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                TrashedFilter::make(),
                TernaryFilter::make('access_admin'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RolesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
