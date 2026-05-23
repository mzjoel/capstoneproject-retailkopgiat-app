<?php

namespace App\Filament\Widgets;

use App\Modules\Catalog\Models\Product;
use App\Modules\Catalog\Models\Category;
use Filament\Actions\BulkActionGroup;
use App\Modules\Transactions\Models\TransactionDetail; 
use App\Modules\Transactions\Models\Transaction; 
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;
use Illuminate\Support\Facades\DB;

class FavoriteProductsWidget extends TableWidget
{
    protected static ?int $sort = 3;
    protected static ?string $heading = 'Produk Favorit';
    protected int | string | array $columnSpan = 'full';
    public ?int $categoryId = null;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->buildQuery())
            ->defaultSort('total_sold', 'desc')
            ->columns([
                TextColumn::make('rank')
                    ->label('#')
                    ->rowIndex()
                    ->width(40),
 
                TextColumn::make('name')
                    ->label('Produk')
                    ->searchable()
                    ->weight('bold'),
 
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('info'),
 
                TextColumn::make('total_sold')
                    ->label('Terjual')
                    ->numeric()
                    ->sortable()
                    ->alignEnd(),
 
                TextColumn::make('total_revenue')
                    ->label('Pendapatan')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable()
                    ->alignEnd(),
 
                IconColumn::make('is_available')
                    ->label('Tersedia')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->headerActions([
                Action::make('filterCategory')
                    ->label(
                        $this->categoryId
                            ? 'Kategori: ' . Category::find($this->categoryId)?->name
                            : 'Semua Kategori'
                    )
                    ->icon('heroicon-m-funnel')
                    ->color('gray')
                    ->form([
                        \Filament\Forms\Components\Select::make('category_id')
                            ->label('Kategori')
                            ->options(
                                Category::orderBy('name')
                                    ->pluck('name', 'id')
                                    ->prepend('Semua Kategori', '')
                            )
                            ->default($this->categoryId ?? ''),
                    ])
                    ->action(function (array $data): void {
                        $this->categoryId = $data['category_id'] ?: null;
                    }),
            ])
            ->paginated([10, 25, 50])
            ->striped();
    }

    private function buildQuery(): Builder
    {
        return Product::query()
            ->select([
                'products.*',
                DB::raw('COALESCE(SUM(td.quantity), 0) AS total_sold'),
                DB::raw('COALESCE(SUM(td.price_transaction * td.quantity), 0) AS total_revenue'),
            ])
            ->leftJoin('transaction_details AS td', function ($join) {
                $join->on('td.product_id', '=', 'products.id')
                    ->whereExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('transactions')
                            ->whereColumn('transactions.id', 'td.transaction_id')
                            ->where('transactions.status', 'completed');
                    });
            })
            ->with('category')
            ->when($this->categoryId, fn (Builder $q) =>
                $q->where('products.category_id', $this->categoryId)
            )
            ->groupBy('products.id')
            ->orderByDesc('total_sold');
    }
}
