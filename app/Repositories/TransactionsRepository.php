<?php

    namespace CommissionCalc\Repositories;

use CommissionCalc\Models\Transaction;
use CommissionCalc\Models\Client;

class TransactionsRepository
{
    protected $transactions = [];

    public function __construct()
    {
    }

    public function getAll()
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction)
    {
        $this->transactions[] = $transaction;
    }

    public function getClientTransactions($clientId)
    {
        $clientTransactions = [];
        foreach ($this->transactions as $transaction) {
            $client = $transaction->getClient();
            if ($client->getClientId() === $clientId) {
                $clientTransactions[] = $transaction;
            }
        }
        return $clientTransactions;
    }

    /**
     * Here we read the file rows and we add each line as a transaction object in the transactions array
     * @param type $filename
     */
    public function loadFromFile($filename)
    {
        if (file_exists($filename)) {
            $contents = file_get_contents($filename);
            $contents = str_replace("\r\n", "\n", $contents);
            $rows = explode("\n", $contents);

            foreach ($rows as $row) {
                if ($row != "") {
                    $row = explode(',', $row);
                    $transaction = new Transaction();
                    $transaction->setDate($row[0]);
                    $transaction->setClient(new Client($row[1], $row[2]));
                    $transaction->setTransactionType($row[3]);
                    $transaction->setTransactionAmount($row[4]);
                    $transaction->setCurrency($row[5]);
                    $this->addTransaction($transaction);
                }
            }
        }
    }
}
