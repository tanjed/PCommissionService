<?php

namespace App\Services\Parser;

use DateTime;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class CSVParser
{
    const INVALID_FILE_ERROR_TEXT = 'Invalid file input';
    const PROCESSING_ERROR_TEXT = 'Unable to process';
    private $filePath, $parsedData, $validEntryColumnCount, $validUserTypes, $validOperationTypes, $dateFormat;

    public function __construct()
    {
        $this->filePath = null;
        $this->parsedData = [];
        $this->validCSVColumns = config('commission.csv_columns');
        $this->validOperationTypes = config('commission.operation_types');
        $this->validUserTypes = config('commission.user_types');
        $this->validCSVColumnCount = count($this->validCSVColumns);
        $this->dateFormat = config('commission.date_format');
    }

    public function setFilePath(string $filePath) : CSVParser
    {
        if (!is_file($filePath)) throw new \Exception(self::INVALID_FILE_ERROR_TEXT);

        $this->filePath = $filePath;
        return $this;
    }

    public function parse()
    {
        if (empty($this->filePath)) throw new \Exception(self::INVALID_FILE_ERROR_TEXT);

        try {
            if (($handle = fopen($this->filePath, 'r')) !== false) {
                while (($data = fgetcsv($handle)) !== false) {
                    if ($this->isValidEntry($data)) $this->parsedData[] = $this->mapEntry($data);
                }
                fclose($handle);
            }
        }
        catch (\Exception $exception) {
            Log::error($exception);
            throw new \Exception(self::PROCESSING_ERROR_TEXT);
        }

       return $this->parsedData;
    }

    private function isValidEntry($data) : bool
    {
        if (!is_array($data) || count($data) != $this->validCSVColumnCount) return false;
        if (!DateTime::createFromFormat($this->dateFormat, $data[$this->validCSVColumns['date']])) return false;
        if (!is_numeric($data[$this->validCSVColumns['user_id']])) return false;
        if (!in_array(strtolower($data[$this->validCSVColumns['operation_type']]), $this->validOperationTypes)) return false;
        if (!in_array(strtolower($data[$this->validCSVColumns['user_type']]), $this->validUserTypes)) return false;
        return true;
    }

    private function mapEntry(array $data): array
    {
        $mappedData = [];

        foreach ($this->validCSVColumns as $column => $index) {
            $mappedData[$column] = $data[$index];
        }

        return $mappedData;
    }
}
