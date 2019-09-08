<?php

if ($argc != 2) 
{
    die('Input file required');
}

require_once __DIR__ . '/vendor/autoload.php';

use CommissionCalc\Repositories\TransactionsRepository;
use CommissionCalc\Controllers\TransactionsController;

$config = include('config.php');

$transactionController = new TransactionsController(new TransactionsRepository(), $config);
$transactionController->processRequest($argv[1]);