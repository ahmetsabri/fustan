<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Schemas\ProductForm;
use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Helpers\TranslationHelper;
use App\Models\Product;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    public static function getNavigationLabel(): string
    {
        return TranslationHelper::label('المنتجات', 'Products');
    }

    public static function getModelLabel(): string
    {
        return TranslationHelper::label('منتج', 'Product');
    }

    public static function getPluralModelLabel(): string
    {
        return TranslationHelper::label('المنتجات', 'Products');
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user?->isAdmin() || $user?->isCustomerService();
    }

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user?->isAdmin() || $user?->isCustomerService();
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();
        return $user?->isAdmin() || $user?->isCustomerService();
    }
}
