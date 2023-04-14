<?php

namespace App\Modules;

use App\Contracts\CommissionCalculatorInterface;
use App\Services\Deposit\DepositCommission;

class CommissionCalculator implements CommissionCalculatorInterface
{
    private
        $depositCommission,
        $transactions = [],
        $deposits = [],
        $withdraws = [];

    public function __construct(DepositCommission $depositCommission)
    {
        $this->depositCommission = $depositCommission;
    }

    public function setTransactions(array $transactions) : self
    {
        $this->transactions = $transactions;
        return $this;
    }

    public function calculate()
    {
        $this->segregateOperationTypes();

        $depositCommissions = $this->depositCommission
            ->setTransactions($this->deposits)
            ->calculate();

    }

    private function segregateOperationTypes() : void
    {
        foreach ($this->transactions as $index => $transaction) {
            switch (strtolower($transaction['operation_type'])) {
                case 'deposit' :
                    $this->deposits[$index] = $transaction;
                    break;

                case 'withdraw' :
                    $this->withdraws[$index] = $transaction;
                    break;

                default :
                    break;
            }
        }
    }
}
