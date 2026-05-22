<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Set; 
use Filament\Schemas\Components\Utilities\Get;
use App\Modules\Catalog\Models\Product;
use App\Modules\Analytics\Models\CustomerProfile;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Informasi Pesanan')
                            ->schema([
                                Select::make('customer_profile_id')
                                    ->label('Pelanggan')
                                    ->options(function(){
                                        return CustomerProfile::query()
                                            ->whereNotNull('name') 
                                            ->where('name', '!=', '') 
                                            ->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        if ($state) {
                                            $set('order_id', 'GIAT-' . time() . '-' . $state);
                                        } else {
                                            $set('order_id', 'GIAT-GUEST');
                                        }
                                    })
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        if ($state) {
                                            $set('order_id', 'GIAT-' . time() . '-' . $state);
                                        } else {
                                            $set('order_id', 'GIAT-GUEST');
                                        }
                                    }),
                                TextInput::make('order_id')
                                    ->label('ID Pesanan')
                                    ->default('GIAT-GUEST')
                                    ->readOnly()
                                    ->dehydrated()
                                    ->required(),
                                TextInput::make('status')
                                    ->label('status')
                                    ->default('pending')
                                    ->readOnly() 
                                    ->dehydrated(),
                                Select::make('payment_method')
                                    ->label('Metode Pembayaran')
                                    ->options([
                                        'cash'=>'CASH',
                                        'midtrans'=>'QRIS',
                                    ])
                                    ->default('cash')
                                    ->required()
                                    ->native(false)
                                    ->selectablePlaceholder(false),
                                TextInput::make('grand_total')
                                    ->label('Total Pembayaran')
                                    ->numeric()
                                    ->live()
                                    ->prefix('Rp')
                                    ->columnSpan(2)
                                    ->readOnly() 
                                    ->dehydrated(),
                            ])->columns(2),

                        Section::make('Daftar Belanja')
                            ->schema([
                                Repeater::make('details')
                                    ->relationship()
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        
                                        self::updateTotalAmount($get, $set); 
                                    })
                                    ->deleteAction(
                                        fn ($action) => $action->after(fn (Get $get, Set $set) =>
                                            self::updateTotalAmount($get, $set)
                                        )
                                    )
                                    ->schema([
                                        Select::make('product_id')
                                            ->label('Produk')
                                            ->options(function(){
                                                return Product::query()
                                                    ->where('is_available', true)
                                                    ->get()
                                                    ->mapWithKeys(fn ($product) => [
                                                        $product->id => "{$product->name} (Status: Tersedia)"
                                                    ]);
                                            })
                                            ->required()
                                            ->searchable()
                                            ->native(false)
                                            ->live()
                                            ->afterStateUpdated(function($state, Set $set, Get $get){
                                                if($state){
                                                    $product = Product::find($state);
                                                    if($product){
                                                        $set('price_transaction', $product->price);
                                                        $quantity = $get('quantity') ?? 1;
                                                        $set('grand_total', $product->price * $quantity);
                                                    }
                                                }
                                        
                                                self::updateTotalAmount($get, $set);
                                            })
                                            ->columnSpan(3),

                                        TextInput::make('quantity')
                                            ->numeric()
                                            ->default(1)
                                            ->minValue(1)
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $price = $get('price_transaction') ?? 0;
                                                $set('grand_total', $price * ($state ?? 1));
                                                
                                                self::updateTotalAmount($get, $set);
                                            })
                                            ->columnSpan(1),

                                        TextInput::make('price_transaction')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->disabled()
                                            ->dehydrated()
                                            ->columnSpan(2),

                                        TextInput::make('grand_total')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->disabled()
                                            ->dehydrated()
                                            ->columnSpan(2),
                                    ])
                                    ->columns(8)
                                    ->addActionLabel('+ Add Item')
                                    ->reorderable(false)
                                    ->collapsible()
                                    ->cloneable()
                            ]),
                    ])->columnSpan(['lg'=>2])
            ]);
    }

    public static function updateTotalAmount(Get $get, Set $set): void
    {
        $items = $get('../../details') ?? $get('details') ?? [];
        
        $total = collect($items)->sum('grand_total');
        
        if ($get('../../details') !== null) {
            $set('../../grand_total', $total);
        } else {
            $set('grand_total', $total);
        }
    }
}