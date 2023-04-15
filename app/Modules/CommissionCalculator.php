<?php

namespace App\Modules;

use App\Contracts\CommissionCalculatorInterface;
use App\Services\Commission\DepositCommission;
use App\Services\Commission\WithdrawCommission;

class CommissionCalculator implements CommissionCalculatorInterface
{
    const USER_TYPE_PRIVATE = 'private';
    const USER_TYPE_BUSINESS = 'business';
    const OPERATION_TYPE_WITHDRAW = 'withdraw';
    const OPERATION_TYPE_DEPOSIT = 'deposit';
    const ENTRY_UNPROCESSABLE_TEXT = 'Unprocessable';
    private $depositCommission,
        $withdrawCommission,
        $transactions = [],
        $deposits = [],
        $withdraws = [],
        $output = [],
        $nonDecimalCurrencies = [];

    public function __construct(DepositCommission $depositCommission, WithdrawCommission $withdrawCommission)
    {
        $this->depositCommission = $depositCommission;
        $this->withdrawCommission = $withdrawCommission;
        $this->nonDecimalCurrencies = config('commission.non_decimal_currencies');
    }

    public function setTransactions(array $transactions) : self
    {
        $this->transactions = $transactions;
        return $this;
    }

    public function calculate() : array
    {
        $totalTransactions = count($this->transactions);
        $this->segregateOperationTypes();

        $depositCommissions = $this->depositCommission
            ->setTransactions($this->deposits)
            ->calculate();

        $withdrawCommissions = $this->withdrawCommission
            ->setTransactions($this->withdraws)
            ->calculate();

        for ($i = 0; $i < $totalTransactions; $i++) {

            if (isset($withdrawCommissions[$i]))
                $commission = $this->getRoundedDecimal($withdrawCommissions[$i]);
            elseif (isset($depositCommissions[$i]))
                $commission = $this->getRoundedDecimal($depositCommissions[$i]);
            else
                $commission = self::ENTRY_UNPROCESSABLE_TEXT;

            $this->output[$i] = $commission;
        }

        return $this->output;
    }

    public function showOutput() : void
    {
        foreach ($this->output as $commission) {
            echo $commission;
            echo PHP_EOL;
        }
    }

    private function getRoundedDecimal($commission, $decimalPlaces = 2) : string
    {
        if (isset($this->nonDecimalCurrencies[$commission['currency']])) return ceil($commission['amount']);

        return number_format(ceil($commission['amount'] * 100) / 100, $decimalPlaces);
    }

    private function segregateOperationTypes() : void
    {
        foreach ($this->transactions as $index => $transaction) {
            switch (strtolower($transaction['operation_type'])) {
                case self::OPERATION_TYPE_DEPOSIT :
                    $this->deposits[$index] = $transaction;
                    break;

                case self::OPERATION_TYPE_WITHDRAW :
                    $this->withdraws[$index] = $transaction;
                    break;

                default :
                    break;
            }
        }
    }
}
