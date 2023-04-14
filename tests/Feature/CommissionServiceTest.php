<?php

namespace Tests\Feature;

use App\Services\Parser\CSVParser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommissionServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testParserSuccessCases(): void
    {
        $csvParser = new CSVParser();
        $csvData = $csvParser->setFilePath(storage_path('app/public/sample.csv'))->parse();

        $this->assertIsArray($csvData);
        $this->assertEquals([
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
            ['date' => '2016-02-19', 'user_id' => '5', 'user_type' => 'private', 'operation_type' => 'withdraw', 'amount' => '3000000', 'currency' => 'JPY']
        ], $csvData);
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
