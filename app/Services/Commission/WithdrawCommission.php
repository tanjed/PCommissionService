<?php

namespace App\Services\Commission;

use App\Abstracts\AbstractCommissionCalculator;
use App\Modules\CommissionCalculator;
use App\Services\Converter\CurrencyConverter;
use Carbon\Carbon;

class WithdrawCommission extends AbstractCommissionCalculator
{

    public function calculate()
    {
        $this->segregateUserTypes();
        $this->calculateBusinessTransactionsCommission();
        $this->calculatePrivateTransactionsCommission();
        return $this->output;
    }

    private function calculateBusinessTransactionsCommission() : void
    {
        $commissionRate = $this->getCommissionRate(CommissionCalculator::OPERATION_TYPE_WITHDRAW, CommissionCalculator::USER_TYPE_BUSINESS);

        foreach ($this->businessTransactions as $index => $transaction) {
            $commission = $this->getPercentage($transaction['amount'], $commissionRate);
            $this->output[$index] = $this->mapCommissionOutput($commission, $transaction);
        }
    }

    private function calculatePrivateTransactionsCommission() : void
    {
        $baseCurrency = config('commission.base_currency');
        $currencyConverter = new CurrencyConverter();
        $maximumFreeWithdraw = config('commission.rules.withdraw.weekly_discount.maximum_free_withdraw');
        $maximumFreeAmount = config('commission.rules.withdraw.weekly_discount.maximum_free_amount');
        $commissionRate = $this->getCommissionRate(CommissionCalculator::OPERATION_TYPE_WITHDRAW, CommissionCalculator::USER_TYPE_PRIVATE);

        $userWiseWeeklyTransactions = $this->segregateUserWiseWeeklyTransactions($this->privateTransactions);
        foreach ($userWiseWeeklyTransactions as $weeklyTransactions) {
            foreach ($weeklyTransactions as $transactions) {
                $transactionsThisWeek = 0;
                $weeklyTransactionAmountInBaseCurrency = 0;

               foreach ($transactions as $transaction) {
                   $commission = 0.00;
                   $transactionsThisWeek++;
                   $weeklyTransactionAmountInBaseCurrency += $currencyConverter->convertCurrency($transaction['amount'], $transaction['currency'], $baseCurrency);
                   if ($weeklyTransactionAmountInBaseCurrency > $maximumFreeAmount) {
                       $commission = $this->getPercentage(($weeklyTransactionAmountInBaseCurrency - $maximumFreeAmount), $commissionRate);
                       $weeklyTransactionAmountInBaseCurrency = $maximumFreeAmount;
                   } elseif ($transactionsThisWeek > $maximumFreeWithdraw) {
                       $commission = $this->getPercentage($weeklyTransactionAmountInBaseCurrency, $commissionRate);
                   }
                   $commission = $currencyConverter->convertCurrency($commission, $baseCurrency, $transaction['currency']);
                   $this->output[$transaction['index']] = $this->mapCommissionOutput($commission, $transaction);
               }
            }
        }
    }

    private function segregateUserTypes() : void
    {
        foreach ($this->transactions as $index => $transaction) {
            switch (strtolower($transaction['user_type'])) {
                case CommissionCalculator::USER_TYPE_PRIVATE :
                    $this->privateTransactions[$index] = $transaction;
                    break;

                case CommissionCalculator::USER_TYPE_BUSINESS :
                    $this->businessTransactions[$index] = $transaction;
                    break;

                default :
                    break;
            }
        }
    }

    private function segregateUserWiseWeeklyTransactions($transactions)
    {
        $segregatedTransactions = [];

        foreach ($transactions as $index => $transaction) {
            $transaction['index'] = $index;
            $date = Carbon::createFromFormat('Y-m-d', $transaction['date']);
            $startOfWeek = $date->startOfWeek(Carbon::MONDAY)
                ->endOfWeek(Carbon::SUNDAY)
                ->startOfWeek()
                ->toDateString();

            $segregatedTransactions[$transaction['user_id']][$startOfWeek][] = $transaction;
        }

        return $segregatedTransactions;
    }
}
