<?php

namespace App\Services\Converter;

use App\Modules\ExchangeRate;

class CurrencyConverter
{
    private $rates = [], $defaultCurrency;

    public function __construct(ExchangeRate $exchangeRateDetector)
    {
        $this->defaultCurrency = config('commission.base_currency');
        $this->rates = $exchangeRateDetector->getExchangeRates();
    }

    private function getExchangeRate(string $toCurrency)
    {
        $toCurrency = strtoupper($toCurrency);
        return $this->rates[$toCurrency] ?? null;
    }

    public function convertCurrency(float $amount, string $fromCurrency, string $toCurrency) : float
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
