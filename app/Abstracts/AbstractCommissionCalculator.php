<?php

namespace App\Abstracts;

use App\Contracts\CommissionCalculatorInterface;

abstract class AbstractCommissionCalculator implements CommissionCalculatorInterface
{
    protected $transactions = [], $output = [];
    public function setTransactions(array $transactions): self
    {
        $this->transactions = $transactions;
        return $this;
    }

    protected function getPercentage($amount, $rate)
    {
       return (float) ($amount * $rate) / 100;
    }
}
