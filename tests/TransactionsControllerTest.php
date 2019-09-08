<?php

use CommissionCalc\Models\Transaction;
use CommissionCalc\Models\Client;
use CommissionCalc\Repositories\TransactionsRepository;
use CommissionCalc\Controllers\TransactionsController;
use ReflectionClass as ReflectionClass;

class TransactionsControllerTest extends PHPUnit\Framework\TestCase
{
    private $config;

    public function setUp()
    {
        $docRoot = dirname(dirname(__FILE__));
        $this->config = include($docRoot.'/'.'config_test.php');
    }

    protected static function getMethod($class, $name)
    {
        $class  = new ReflectionClass($class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    public function testConvertCurrencyToEUR()
    {
        $method      = self::getMethod('CommissionCalc\Controllers\TransactionsController', 'convertCurrency');
        $obj         = new TransactionsController(new TransactionsRepository(), $this->config);
        $transaction = new Transaction();
        $transaction->setDate("2018-08-11");
        $transaction->setClient(new Client('1', 'natural'));
        $transaction->setTransactionType("cash_in");
        $transaction->setTransactionAmount("12953");
        $transaction->setCurrency("JPY");

        $result = $method->invokeArgs($obj, [$transaction]);

        self::assertEquals(100, $result);
    }

    public function testConvertCurrencyFromEUR()
    {
        $method      = self::getMethod('CommissionCalc\Controllers\TransactionsController', 'convertCurrency');
        $obj         = new TransactionsController(new TransactionsRepository(), $this->config);
        $transaction = new Transaction();
        $transaction->setDate("2018-08-11");
        $transaction->setClient(new Client('1', 'natural'));
        $transaction->setTransactionType("cash_in");
        $transaction->setTransactionAmount("1000");
        $transaction->setCurrency("JPY");

        $result = $method->invokeArgs($obj, [$transaction, 1000]);

        self::assertEquals(129530, $result);
    }


    public function testCashInCommissionEUR()
    {
        $method      = self::getMethod('CommissionCalc\Controllers\TransactionsController', 'cashInCommission');
        $obj         = new TransactionsController(new TransactionsRepository(), $this->config);
        $transaction = new Transaction();
        $transaction->setDate("2018-08-11");
        $transaction->setClient(new Client('1', 'natural'));
        $transaction->setTransactionType("cash_in");
        $transaction->setTransactionAmount("200.00");
        $transaction->setCurrency("EUR");

        $result = $method->invokeArgs($obj, [$transaction]);

        self::assertEquals(0.06, $result);
    }

    public function testCashInCommissionUSD()
    {
        $method      = self::getMethod('CommissionCalc\Controllers\TransactionsController', 'cashInCommission');
        $obj         = new TransactionsController(new TransactionsRepository(), $this->config);
        $transaction = new Transaction();
        $transaction->setDate("2018-08-11");
        $transaction->setClient(new Client('1', 'natural'));
        $transaction->setTransactionType("cash_in");
        $transaction->setTransactionAmount("100.00");
        $transaction->setCurrency("USD");

        $result = $method->invokeArgs($obj, [$transaction]);

        self::assertEquals(0.03, $result);
    }

    public function testCashInCommissionJPY()
    {
        $method      = self::getMethod('CommissionCalc\Controllers\TransactionsController', 'cashInCommission');
        $obj         = new TransactionsController(new TransactionsRepository(), $this->config);
        $transaction = new Transaction();
        $transaction->setDate("2018-08-11");
        $transaction->setClient(new Client('1', 'natural'));
        $transaction->setTransactionType("cash_in");
        $transaction->setTransactionAmount("10000");
        $transaction->setCurrency("JPY");

        $result = $method->invokeArgs($obj, [$transaction]);

        self::assertEquals(3, $result);
    }

    
    public function testCashOutCommissionOneTransactionNatural()
    {
        $method      = self::getMethod('CommissionCalc\Controllers\TransactionsController', 'cashOutCommission');
        $obj         = new TransactionsController(new TransactionsRepository(), $this->config);
        $transaction = new Transaction();
        $transaction->setDate("2018-08-11");
        $transaction->setClient(new Client('1', 'natural'));
        $transaction->setTransactionType("cash_out");
        $transaction->setTransactionAmount("1100");
        $transaction->setCurrency("EUR");

        $result = $method->invokeArgs($obj, [$transaction]);

        self::assertEquals(0.3, $result);
    }

    public function testCashOutCommissionOneTransactionLegalMin()
    {
        $method      = self::getMethod('CommissionCalc\Controllers\TransactionsController', 'cashOutCommission');
        $obj         = new TransactionsController(new TransactionsRepository(), $this->config);
        $transaction = new Transaction();
        $transaction->setDate("2018-08-11");
        $transaction->setClient(new Client('1', 'legal'));
        $transaction->setTransactionType("cash_out");
        $transaction->setTransactionAmount("500");
        $transaction->setCurrency("EUR");

        $result = $method->invokeArgs($obj, [$transaction]);

        self::assertEquals(1.5, $result);
    }

    public function testCashOutCommissionOneTransactionLegalMax()
    {
        $method      = self::getMethod('CommissionCalc\Controllers\TransactionsController', 'cashOutCommission');
        $obj         = new TransactionsController(new TransactionsRepository(), $this->config);
        $transaction = new Transaction();
        $transaction->setDate("2018-08-11");
        $transaction->setClient(new Client('1', 'legal'));
        $transaction->setTransactionType("cash_out");
        $transaction->setTransactionAmount("5000");
        $transaction->setCurrency("EUR");

        $result = $method->invokeArgs($obj, [$transaction]);

        self::assertEquals(15, $result);
    }
}
