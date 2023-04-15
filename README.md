# PCommissionService

`PCommissionService` is a simple Laravel command that automate the commission calculations from a given CSV. 

# Requirements
- PHP 8.1 and above

# Installation
Follow the steps to setup PCommissionService :
- `git clone https://github.com/tanjed/PCommissionService.git`
- `cd PCommissionService`
- `composer install`
- `cp .env.testing .env`

# Usage
Run `php artisan calculate:commission {csv_full_path}`

> **_NOTE :_**  It is important to provide the full CSV path to prevent any kinds of error. <br>
> **Example** : `php artisan calculate:commission storage/app/public/sample.csv`


# Test
Run `php artisan test`
> **_NOTE :_**  `CommissionServiceTest` has dedicated data I/O and config inside it. Any change in the config file won't have any effect.


# Functionalities
A Laravel command named `CalculateCommissionCommand` is the booting point of the whole process.
It takes the path of the CSV as an argument and start the calculation process.

There are 2 major components of the process.
- `CSVParser`
- `CommissionCalculator`

### CSVParser
It takes the CSV file path then parse and filter data and return a clean dataset to work with.
~~~php
$transactions = (new CSVParser())
    ->setFilePath('storage/app/public/sample.csv')
    ->parse();
~~~

### CommissionCalculator
It takes the sanitized CSV data as an array and calculates the commission fees for different operation type.
`CommissionCalculator` has split into 2 additional class for operational ease. These are :
- `DepositCommission`
- `WithdrawCommission`

After segregating the transactions by operation type corresponding modules are being called for further processing.
Both class extends `AbstractCommissionCalculator` which implements `CommissionCalculatorInterface` (a common interface for commission calculation).

~~~php
$commissionCalculator = new CommissionCalculator();
$commissions = $commissionCalculator->setTransactions($transactions)->calculate();
$commissionCalculator->showOutput(); //Print Output if necessary
~~~

### DepositCommission
This class is responsible for processing `Deposit` type transactions. It takes the deposit transactions
as input and calculate the commission fee based on the default config settings.
~~~php
  $depositCommissions = (new DepositCommission())
            ->setTransactions($deposits)
            ->calculate();
~~~

### WithdrawCommission 
This class is responsible for processing `Withdraw` type transactions. It takes the deposit transactions
as input and calculate the commission fee based on the default config settings.
It also handles the additional logics for weekly private transactions commission by segregating the transaction list by
corresponding user type.
To process private transactions `WithdrawCommission` needs to convert currencies to EUR.
`CurrencyConverter` and `ExchangeRate` classes are used to convert to desired currency.
~~~php
  $withdrawCommissions = (new WithdrawCommission())
            ->setTransactions($withdraws)
            ->calculate();
~~~

### ExchangeRate
This class is responsible for providing the latest list of exchange rate from EUR to other currencies.
~~~php
$exchangeRates = (new ExchangeRate)->getExchangeRates();
~~~

### CurrencyConverter
This class converts one currency amount into different currency from the list given from `ExchangeRate`.
~~~php
$convertedCurrency = (new CurrencyConverter())->convertCurrency(10, 'EUR', 'JPY');
~~~
>**_NOTE :_** The Japanese Yen does not use a decimal point. The yen is the lowest value possible in Japanese currency.
