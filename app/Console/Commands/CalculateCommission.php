<?php

namespace App\Console\Commands;

use App\Components\Operation\SubOperation;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CalculateCommission extends Command
{
    protected $signature = 'calculate:commissions';

    protected $description = 'Calculate all commissions of accounts';

    public function handle()
    {
        Account::query()
            ->whereDoesntHave('transactions', function (Builder $query) {
                $query->where('type', Transaction::getKeyByType('commission'));
            })
            ->whereHas('transactions', function (Builder $query) {
                $query->where('type', Transaction::getKeyByType('using'))
                    ->whereRaw('
                        YEAR(created_at) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) 
                        and MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
                    ');
            })
            ->chunk(1000, function (Collection $accounts) {
                $accounts->each(function (Account $account) {
                    Transaction::make($account, $account->getCommissionSum(), new SubOperation('commission'));
                });
            });
    }
}
