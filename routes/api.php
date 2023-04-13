<?php

use Illuminate\Support\Facades\Route;


Route::get('test', function (){
    dd((new \App\Services\Parser\CSVParser())->setFilePath(storage_path('app/public/sample.csv'))->parse());
});
