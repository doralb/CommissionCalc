<?php

use CommissionCalc\Services\Commission;
use CommissionCalc\Models\Transaction;
use CommissionCalc\Models\Client;
use CommissionCalc\Repositories\TransactionsRepository;

class CommissionTest extends PHPUnit\Framework\TestCase
{

    private $config;

    public function setUp()
    {
        $docRoot = dirname(dirname(__FILE__));
        $this->config = include ($docRoot . '/' . 'config_test.php');
    }

    public function testCashInCommissionEUR()
    {
        $obj = new Commission(new TransactionsRepository(), $this->config);
        $transaction = new Transaction();
        $transaction->setDate("2018-08-11");
        $transaction->setClient(new Client('1', 'natural'));
        $transaction->setTransactionType("cash_in");
        $transaction->setTransactionAmount("200.00");
        $transaction->setCurrency("EUR");

        $result = $obj->cashInCommission($transaction);

        self::assertEquals(0.06, $result);
    }

    public function testCashInCommissionUSD()
    {
        $obj = new Commission(new TransactionsRepository(), $this->config);
        $transaction = new Transaction();
        $transaction->setDate("2018-08-11");
        $transaction->setClient(new Client('1', 'natural'));
        $transaction->setTransactionType("cash_in");
        $transaction->setTransactionAmount("100.00");
        $transaction->setCurrency("USD");

        $result = $obj->cashInCommission($transaction);

        self::assertEquals(0.03, $result);
    }

    public function testCashInCommissionJPY()
    {
        $obj = new Commission(new TransactionsRepository(), $this->config);
        $transaction = new Transaction();
        $transaction->setDate("2018-08-11");
        $transaction->setClient(new Client('1', 'natural'));
        $transaction->setTransactionType("cash_in");
        $transaction->setTransactionAmount("10000");
        $transaction->setCurrency("JPY");

        $result = $obj->cashInCommission($transaction);

        self::assertEquals(3, $result);
    }

    public function testCashOutCommissionOneTransactionNatural()
    {
        $obj = new Commission(new TransactionsRepository(), $this->config);
        $transaction = new Transaction();
        $transaction->setDate("2018-08-11");
        $transaction->setClient(new Client('1', 'natural'));
        $transaction->setTransactionType("cash_out");
        $transaction->setTransactionAmount("1100");
        $transaction->setCurrency("EUR");

        $result = $obj->cashOutCommission($transaction);

        self::assertEquals(0.3, $result);
    }

    public function testCashOutCommissionOneTransactionLegalMin()
    {
        $obj = new Commission(new TransactionsRepository(), $this->config);
        $transaction = new Transaction();
        $transaction->setDate("2018-08-11");
        $transaction->setClient(new Client('1', 'legal'));
        $transaction->setTransactionType("cash_out");
        $transaction->setTransactionAmount("500");
        $transaction->setCurrency("EUR");

        $result = $obj->cashOutCommission($transaction);

        self::assertEquals(1.5, $result);
    }

    public function testCashOutCommissionOneTransactionLegalMax()
    {
        $obj = new Commission(new TransactionsRepository(), $this->config);
        $transaction = new Transaction();
        $transaction->setDate("2018-08-11");
        $transaction->setClient(new Client('1', 'legal'));
        $transaction->setTransactionType("cash_out");
        $transaction->setTransactionAmount("5000");
        $transaction->setCurrency("EUR");

        $result = $obj->cashOutCommission($transaction);

        self::assertEquals(15, $result);
    }
}