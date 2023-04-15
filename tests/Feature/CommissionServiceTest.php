<?php

namespace Tests\Feature;

use App\Modules\CommissionCalculator;
use App\Modules\ExchangeRate;
use App\Services\Commission\DepositCommission;
use App\Services\Commission\WithdrawCommission;
use App\Services\Converter\CurrencyConverter;
use App\Services\Parser\CSVParser;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class CommissionServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_commission_calculation_success_flow()
    {
        $csvParserMock = $this->createMock(CSVParser::class);
        $exchangeRateDetectorMock = $this->createMock(ExchangeRate::class);

        foreach ($this->getDataSource() as $dataSource) {
            Config::set('commission.rules.deposit.default', $dataSource['config']['rules']['deposit']['default']);
            Config::set('commission.rules.withdraw.default', $dataSource['config']['rules']['withdraw']['default']);
            Config::set('commission.rules.withdraw.business', $dataSource['config']['rules']['withdraw']['business']);
            Config::set('commission.rules.withdraw.weekly_discount.maximum_free_withdraw', $dataSource['config']['rules']['withdraw']['weekly_discount']['maximum_free_withdraw']);
            Config::set('commission.rules.withdraw.weekly_discount.maximum_free_amount', $dataSource['config']['rules']['withdraw']['weekly_discount']['maximum_free_amount']);

            $csvParserMock->method('parse')->willReturn($dataSource['input']);
            $exchangeRateDetectorMock->method('getExchangeRates')->willReturn($dataSource['conversion_rate']);

            $depositCommissionService = new DepositCommission();
            $currencyConverter = new CurrencyConverter($exchangeRateDetectorMock);
            $withdrawCommissionService = new WithdrawCommission($currencyConverter);
            $commissionCalculator = new CommissionCalculator($depositCommissionService, $withdrawCommissionService);

            $this->assertEquals($dataSource['output'], $commissionCalculator->setTransactions($csvParserMock->parse())->calculate());
        }
    }

    private function getDataSource()
    {
        return [
            [
                'input' => [
                    ['date' => '2014-12-31', 'user_id' => '4', 'user_type' => 'private', 'operation_type' => 'withdraw', 'amount' => '1200.00', 'currency' => 'EUR'],
                    ['date' => '2015-01-01', 'user_id' => '4', 'user_type' => 'private', 'operation_type' => 'withdraw', 'amount' => '1000.00', 'currency' => 'EUR'],
                    ['date' => '2016-01-05', 'user_id' => '4', 'user_type' => 'private', 'operation_type' => 'withdraw', 'amount' => '1000.00', 'currency' => 'EUR'],
                    ['date' => '2016-01-05', 'user_id' => '1', 'user_type' => 'private', 'operation_type' => 'deposit', 'amount' => '200.00', 'currency' => 'EUR'],
                    ['date' => '2016-01-06', 'user_id' => '2', 'user_type' => 'business', 'operation_type' => 'withdraw', 'amount' => '300.00', 'currency' => 'EUR'],
                    ['date' => '2016-01-06', 'user_id' => '1', 'user_type' => 'private', 'operation_type' => 'withdraw', 'amount' => '30000', 'currency' => 'JPY'],
                    ['date' => '2016-01-07', 'user_id' => '1', 'user_type' => 'private', 'operation_type' => 'withdraw', 'amount' => '1000.00', 'currency' => 'EUR'],
                    ['date' => '2016-01-07', 'user_id' => '1', 'user_type' => 'private', 'operation_type' => 'withdraw', 'amount' => '100.00', 'currency' => 'USD'],
                    ['date' => '2016-01-10', 'user_id' => '1', 'user_type' => 'private', 'operation_type' => 'withdraw', 'amount' => '100.00', 'currency' => 'EUR'],
                    ['date' => '2016-01-10', 'user_id' => '2', 'user_type' => 'business', 'operation_type' => 'deposit', 'amount' => '10000.00', 'currency' => 'EUR'],
                    ['date' => '2016-01-10', 'user_id' => '3', 'user_type' => 'private', 'operation_type' => 'withdraw', 'amount' => '1000.00', 'currency' => 'EUR'],
                    ['date' => '2016-02-15', 'user_id' => '1', 'user_type' => 'private', 'operation_type' => 'withdraw', 'amount' => '300.00', 'currency' => 'EUR'],
                    ['date' => '2016-02-19', 'user_id' => '5', 'user_type' => 'private', 'operation_type' => 'withdraw', 'amount' => '3000000', 'currency' => 'JPY'],
                ],
                'output' => ["0.60", "3.00", "0.00", "0.06", "1.50", "0", "0.69", "0.30", "0.30", "3.00", "0.00", "0.00", "8608"],
                'conversion_rate' => [
                    'USD' =>  1.129031,
                    'JPY' => 130.869977
                ],
                'config' => [
                    'rules' => [
                        'deposit' => [
                            'default' => 0.03,
                        ],
                        'withdraw' => [
                            'default' => 0.3,
                            'business' => 0.5,
                            'weekly_discount' => [
                                'maximum_free_withdraw' => 3,
                                'maximum_free_amount' => 1000
                            ],
                        ]
                    ],
                ]
            ]
        ];
    }

    public function testParserSuccessCases(): void
    {
        $csvParser = new CSVParser();
        $csvData = $csvParser->setFilePath(storage_path('app/public/sample.csv'))->parse();

        $this->assertIsArray($csvData);
    }

    public function testParserInvalidFilePath()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid file input');

        $csvParser = new CSVParser();
        $csvParser->setFilePath(public_path('app/public/sample.csv'))->parse();
        $csvParser->parse();
    }

    public function testParserReturnSelf()
    {
        $csvParser = new CSVParser();
        $csvData = $csvParser->setFilePath(storage_path('app/public/sample.csv'));
        $this->assertEquals($csvParser, $csvData);
    }
}
