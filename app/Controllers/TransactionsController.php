<?php

    namespace CommissionCalc\Controllers;

use CommissionCalc\Services\Commission;
use CommissionCalc\Repositories\TransactionsRepository;

class TransactionsController
{

    /**
     *
     * @var TransactionsRepository
     */
    protected $transactionRepository;

    /**
     *
     * @var array
     */
    protected $config;

    protected $commission;

    protected $conversion;

    /**
     * TransactionsController constructor.
     *
     * @param TransactionsRepository $repository
     * @param array $config
     */
    public function __construct(TransactionsRepository $repository, array $config)
    {
        $this->transactionRepository = $repository;
        $this->config = $config;
        $this->commission = new Commission($repository, $config);
    }

    /**
     *
     * Entry point
     *
     * @param string $filename
     */
    public function processRequest($filename)
    {
        $this->transactionRepository->loadFromFile($filename);

        foreach ($this->transactionRepository->getAll() as $transaction) {

            // Calculate commission
            $commission = $this->commission->calculate($transaction);

            // Ceiling to the closest bond
            $significance = pow(10, $this->config['currencies'][$transaction->getCurrency()]['decimalPrecision']);
            $commission = ceil($commission * $significance) / $significance;

            // Print out
            $this->printCommission($commission, $this->config['currencies'][$transaction->getCurrency()]['decimalPrecision']);
        }
    }

    private function printCommission($commission, $decimals = 2)
    {
        echo number_format($commission, $decimals, '.', '') . PHP_EOL;
    }
}
