<?php

namespace App\Services\Deposit;

use App\Abstracts\AbstractCommissionCalculator;

class DepositCommission extends AbstractCommissionCalculator
{

    public function calculate() : array
    {
        $this->calculateDefaultDepositCommissionRate();
        return $this->output;
    }

    private function calculateDefaultDepositCommissionRate() : void
    {
        $commissionRate = config('commission.rules.deposit.default');

        foreach ($this->transactions as $index => $transaction) {
            $this->output[$index] = $this->getPercentage($transaction['amount'], $commissionRate);
        }
    }
}
