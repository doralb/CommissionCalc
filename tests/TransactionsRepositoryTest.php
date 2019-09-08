<?php

use CommissionCalc\Models\Transaction;
use CommissionCalc\Models\Client;
use CommissionCalc\Repositories\TransactionsRepository;

class TransactionsRepositoryTest extends PHPUnit\Framework\TestCase
{
    public function testgetAll()
    {
        $transaction = new Transaction();
        $transaction->setDate("2018-08-11");
        $transaction->setClient(new Client("1", "natural"));
        $transaction->setTransactionType("cash_in");
        $transaction->setTransactionAmount("1000.00");
        $transaction->setCurrency("EUR");
        $repo = new TransactionsRepository();
        $repo->addTransaction($transaction);
        self::assertEquals([$transaction], $repo->getAll());
    }
}
