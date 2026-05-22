<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkAction;
use App\Modules\Transactions\Models\Transaction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_id')
                ->label('No Transaksi')
                ->searchable()
                ->sortable()
                ->weight('bold')
                ->copyable()
                ->icon('heroicon-o-document-text'),
                TextColumn::make('customerProfile.name')
                    ->label('Nama')
                    ->default('Walk-in Customer')
                    ->searchable()
                    ->icon('heroicon-o-user'),
                TextColumn::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'cash' => 'success',
                        'qris' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('grand_total')
                    ->label('Total Pembayaran')
                    ->money('IDR')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'pending' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match($state) {
                        'pending' => 'heroicon-o-clock',
                        'completed' => 'heroicon-o-check-circle',
                        'cancelled' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    }),
                    TextColumn::make('created_at')
                        ->label('Tanggal Transaksi')
                        ->dateTime('d M Y H:i')
                        ->sortable()
                        ->icon('heroicon-o-calendar'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Transaksi')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->native(false),

                    Filter::make('created_at')
                    ->form([
                        DatePicker::make('from')
                            ->label('From Date'),
                        DatePicker::make('until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $q, $date): Builder => $q->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $q, $date): Builder => $q->whereDate('created_at', '<=', $date)
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators['from'] = 'From: ' . \Carbon\Carbon::parse($data['from'])->format('d M Y');
                        }
                        if ($data['until'] ?? null) {
                            $indicators['until'] = 'Until: ' . \Carbon\Carbon::parse($data['until'])->format('d M Y');
                        }
                        return $indicators;
                    }),
                    Filter::make('today')
                        ->label('Hari Ini')
                        ->query(fn (Builder $query): Builder => $query->whereDate('created_at', today()))
                        ->toggle(),
            ])
            ->recordActions([
                ActionGroup::make([
                ViewAction::make(),
            
            EditAction::make()
                ->visible(fn (Transaction $record): bool => $record->status === 'pending'),

            Action::make('complete')
                ->label('Mark Complete')
                ->icon('heroicon-o-check')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Complete Transaction')
                ->modalDescription('Are you sure you want to mark this Transaction as completed?')
                ->visible(fn (Transaction $record): bool => $record->status === 'pending')
                ->action(function (Transaction $record) {
                    $record->update(['status' => 'completed']);

                    Notification::make()
                        ->title('Transaction Completed')
                        ->body("Transaction {$record->invoice_number} has been completed.")
                        ->success()
                        ->send();
                }),

            Action::make('cancel')
                ->label('Cancel Transaction')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Cancel Transaction')
                ->modalDescription('Are you sure? Stock will be restored.')
                ->visible(fn (Transaction $record): bool => $record->status === 'pending')
                ->action(function (Transaction $record) {
                    $record->markAsCancelled();

                    Notification::make()
                        ->title('Transaction Cancelled')
                        ->body("Transaction {$record->invoice_number} has been cancelled. Stock restored.")
                        ->warning()
                            ->send();
                }),

            Action::make('print')
                ->label('Print Invoice')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn (Transaction $record): string => route('invoice.print', $record))
                ->openUrlInNewTab()
                ->visible(false), 

            DeleteAction::make()
                ->visible(fn (Transaction $record): bool => $record->status === 'cancelled'),
        ]),
            ])
            ->toolbarActions([
               BulkActionGroup::make([
                    BulkAction::make('complete_all')
                        ->label('Complete Selected')
                         ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each(
                            fn ($record) => $record->status === 'pending' && $record->update(['status' => 'completed'])
                    ))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])->striped()->poll('30s');
    }
}
