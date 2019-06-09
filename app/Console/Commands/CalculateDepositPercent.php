<?php

namespace App\Console\Commands;

use App\Components\Operation\SumOperation;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CalculateDepositPercent extends Command
{
    protected $signature = 'calculate:deposits';

    protected $description = 'Calculate all deposits';

    public function handle()
    {
        $today = date('Y-m-d');
        $lastDayOfMonth = ($today === date('Y-m-t', strtotime($today)));

        Account::query()
            ->whereDay('created_at', $lastDayOfMonth ? 31 : date('d'))
            ->whereDoesntHave('transactions', function (Builder $query) {
                $query->where('type', Transaction::getKeyByType('deposit'));
            })
            ->chunk(1000, function (Collection $accounts) {
                $accounts->each(function (Account $account) {
                    Transaction::make($account, $account->getDepositPercentSum(), new SumOperation('deposit'));
                });
            });
    }
}
