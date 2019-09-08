<?php

    namespace CommissionCalc\Models;

class Transaction
{
    const CASH_IN = "cash_in";
    const CASH_OUT = "cash_out";

    private $id;
    private $date;
    private $transactionType;
    private $transactionAmount;
    private $currency;
    private $client;

    /**
     * Transaction constructor.
     * @param $id
     */
    public function __construct()
    {
        $this->id = uniqid();
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }


    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    public function getClient()
    {
        return $this->client;
    }


    /**
     * @return mixed
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * @param mixed $transactionType
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;
    }

    /**
     * @return mixed
     */
    public function getTransactionAmount()
    {
        return $this->transactionAmount;
    }

    /**
     * @param mixed $transactionAmount
     */
    public function setTransactionAmount($transactionAmount)
    {
        $this->transactionAmount = $transactionAmount;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}
