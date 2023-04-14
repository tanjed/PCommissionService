<?php

namespace App\Services\Commission;

use App\Abstracts\AbstractCommissionCalculator;

class DepositCommission extends AbstractCommissionCalculator
{

    public function calculate() : array
    {
        $this->calculateDefaultDepositCommissionRate();
        return $this->output;
    }
}
