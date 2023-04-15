<?php

namespace App\Modules;

class ExchangeRate
{
    public function getExchangeRates() : array
    {
        $list = json_decode(file_get_contents(config('commission.currency_exchange_base_url')), true);
        return $list['rates'] ?? [];
    }
}
