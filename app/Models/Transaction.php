<?php

namespace App\Models;

use App\Components\Operation\AbstractOperation;
use App\Components\Operation\OperationStrategy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Transaction extends Model
{
    private static $availableTypes = [
        0 => 'deposit',
        1 => 'commission',
        2 => 'using'
    ];

    protected $guarded = [];

    public $timestamps = false;

    public static function getTypeByKey($key)
    {
        if (!isset(static::$availableTypes[$key])) {
            throw new \RuntimeException("Transaction type by key [$key] not found.");
        }

        return static::$availableTypes[$key];
    }

    public static function getKeyByType($type)
    {
        $found = array_search($type, static::$availableTypes);

        if ($found === false) {
            throw new \RuntimeException("Transaction key by type [$type] not found.");
        }

        return $found;
    }

    public static function make(Account $account, float $sum, AbstractOperation $operation)
    {
        DB::transaction(function () use ($account, $sum, $operation) {
            $account->update([
                'balance' => $operation->calculate($account->balance, $sum)
            ]);

            $account->transactions()->create([
                'sum'        => $sum,
                'type'       => static::getKeyByType($operation->type()),
                'created_at' => date('Y-m-d')
            ]);
        });
    }
}
