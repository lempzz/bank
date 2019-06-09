<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getDepositPercentSum()
    {
        return round($this->balance * ($this->percent / 100), 2);
    }

    public function getCommissionSum()
    {
        $commission = 0;

        if (between($this->balance, 0, 1000)) {
            $percentSum = $this->balance * 0.05;
            $commission = $percentSum < 50 ? 50 : $percentSum;
        } elseif (between($this->balance, 1000, 10000)) {
            $commission = $this->balance * 0.06;
        } elseif ($this->balance > 10000) {
            $percentSum = $this->balance * 0.07;
            $commission = $percentSum > 5000 ? 5000 : $percentSum;
        }

        return $this->isPreviouslyMonthCreated() ? $this->calculateCommissionForRecentlyCreated($commission) : $commission;
    }

    private function isPreviouslyMonthCreated()
    {
        $created = strtotime($this->created_at);
        $previouslyMonth = strtotime('-1 month');

        return date('Y-m', $previouslyMonth) === date('Y-m', $created);
    }

    private function calculateCommissionForRecentlyCreated(float $commission)
    {
        return $commission / date('j', strtotime($this->created_at));
    }
}
