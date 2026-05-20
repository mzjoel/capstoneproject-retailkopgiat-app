<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Modules\Catalog\Models\Category;
use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;


class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->live(onBlur:true)
                    ->unique(Category::class, 'name', ignoreRecord: true)
                    ->maxLength(255)
                    ->autofocus()
            ]);
    }
}
