<?php

namespace App\Abstracts;

use App\Contracts\CommissionCalculatorInterface;
use Illuminate\Support\Facades\Log;

abstract class AbstractCommissionCalculator implements CommissionCalculatorInterface
{
    protected $transactions = [], $privateTransactions = [], $businessTransactions = [], $output = [];
    public function setTransactions(array $transactions): self
    {
        $this->transactions = $transactions;
        return $this;
    }

    protected function getPercentage($amount, $rate)
    {
       return ($amount * $rate) / 100;
    }

    protected function getCommissionRate($operationType, $userType)
    {
        $commissionRules = config('commission.rules');
        if (isset($commissionRules[$operationType][$userType])) return $commissionRules[$operationType][$userType];
        return $commissionRules[$operationType]['default'];
    }

    protected function calculateDefaultDepositCommissionRate() : void
    {
        $commissionRate = config('commission.rules.deposit.default');

        foreach ($this->transactions as $index => $transaction) {
            $commission = $this->getPercentage($transaction['amount'], $commissionRate);
            $this->output[$index] = $this->mapCommissionOutput($commission, $transaction);
        }
    }

    protected function mapCommissionOutput($commission, $transaction)
    {
        return [
            'amount' => $commission,
            'currency' => $transaction['currency']
        ];
    }
}
