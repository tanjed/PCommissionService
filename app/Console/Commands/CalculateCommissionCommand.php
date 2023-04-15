<?php

namespace App\Console\Commands;

use App\Modules\CommissionCalculator;
use App\Services\Parser\CSVParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CalculateCommissionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:commission {csv_path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will parse calculate commissions from given csv file for different user_type(private, business) and operation_type(withdraw, deposit)';

    private $csvPath, $csvParser, $commissionCalculator;

    public function __construct(
        CSVParser $csvParser,
        CommissionCalculator $commissionCalculator
    )
    {
        parent::__construct();
        $this->csvParser = $csvParser;
        $this->commissionCalculator = $commissionCalculator;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $transactions = $this->csvParser
                ->setFilePath($this->argument('csv_path'))
                ->parse();

            $this->commissionCalculator
                ->setTransactions($transactions)
                ->calculate();

            $this->commissionCalculator->showOutput();
        }
        catch (\Exception $exception) {
            Log::error($exception);
            throw $exception;
        }
    }
}
