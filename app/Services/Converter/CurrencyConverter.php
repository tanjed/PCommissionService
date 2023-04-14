<?php

namespace App\Services\Converter;

class CurrencyConverter
{
    private $rates = [], $defaultCurrency;

    public function __construct()
    {
        $this->defaultCurrency = config('commission.base_currency');
        $list = $this->getCurrencyList();
        if (isset($list['rates'])) $this->rates = $list['rates'];
        $this->rates['USD'] = 1.1497;
        $this->rates['JPY'] = 129.53;
    }

    private function getCurrencyList()
    {
       return json_decode(file_get_contents(config('commission.currency_exchange_base_url')), true);
    }

    private function getExchangeRate($toCurrency)
    {
        $toCurrency = strtoupper($toCurrency);
        if (isset($this->rates[$toCurrency])) return $this->rates[$toCurrency];
        return null;
    }

    public function convertCurrency($amount, $fromCurrency, $toCurrency)
    {
        if ($fromCurrency == $toCurrency)
           return $amount;
        elseif ($toCurrency == $this->defaultCurrency)
           return $amount / $this->getExchangeRate($fromCurrency);
        elseif ($fromCurrency == $this->defaultCurrency)
            return $amount * $this->getExchangeRate($toCurrency);
        else
            return ($amount / $this->getExchangeRate($fromCurrency)) + ($amount * $this->getExchangeRate($toCurrency));

    }
}
