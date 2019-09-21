<?php

use CommissionCalc\Models\Client;
use CommissionCalc\Models\Transaction;
use CommissionCalc\Services\Conversion;

class ConversionTest extends PHPUnit\Framework\TestCase
{

    private $config;

    public function setUp()
    {
        $docRoot = dirname(dirname(__FILE__));
        $this->config = include ($docRoot . '/' . 'config_test.php');
    }

    public function testConvertCurrencyToEUR()
    {
        $obj = new Conversion($this->config);
        $transaction = new Transaction();
        $transaction->setDate("2018-08-11");
        $transaction->setClient(new Client('1', 'natural'));
        $transaction->setTransactionType("cash_in");
        $transaction->setTransactionAmount("12953");
        $transaction->setCurrency("JPY");

        $result = $obj->convert($transaction);

        self::assertEquals(100, $result);
    }

    public function testConvertCurrencyFromEUR()
    {
        $obj = new Conversion($this->config);
        $transaction = new Transaction();
        $transaction->setDate("2018-08-11");
        $transaction->setClient(new Client('1', 'natural'));
        $transaction->setTransactionType("cash_in");
        $transaction->setTransactionAmount("1000");
        $transaction->setCurrency("JPY");

        $result = $obj->convert($transaction, 1000);

        self::assertEquals(129530, $result);
    }
}