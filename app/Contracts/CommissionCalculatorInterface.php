<?php

namespace App\Contracts;

interface CommissionCalculatorInterface
{
    public function setTransactions(array $transactions);
    public function calculate();
}
