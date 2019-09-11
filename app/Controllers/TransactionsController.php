<?php

    namespace CommissionCalc\Controllers;

use CommissionCalc\Models\Transaction;
use CommissionCalc\Models\Client;
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
        $transactions = $this->transactionRepository->getAll();
        $this->countCommissions($transactions);
    }

    private function countCommissions(array $transactions)
    {
        foreach ($transactions as $transaction) {

            if ($transaction->getTransactionType() === Transaction::CASH_IN) {

                $commission = $this->cashInCommission($transaction);
            } else {

                $commission = $this->cashOutCommission($transaction);
            }

            // Ceiling to the closest bond
            $significance = pow(10, $this->config['currencies'][$transaction->getCurrency()]['decimalPrecision']);
            $commission = ceil($commission * $significance) / $significance;

            // Print out
            $this->printCommission($commission, $this->config['currencies'][$transaction->getCurrency()]['decimalPrecision']);
        }
    }

    private function cashInCommission(Transaction $transaction)
    {
        $commission = $transaction->getTransactionAmount() * $this->config['cashInCommissionPercent'];
        $convertedLimit = $this->convertCurrency($transaction, $this->config['cashInCommissionLimitMax']);

        if ($commission > $convertedLimit) {
            return $convertedLimit;
        } else {
            return $commission;
        }
    }

    /**
     * Cash Out commission calculation for natural and legal users
     *
     * @param Transaction $transaction
     * @return float|int
     */
    private function cashOutCommission(Transaction $transaction)
    {
        $transactionClient = $transaction->getClient();

        if ($transactionClient->getClientType() === Client::CLIENT_TYPE_LEGAL) {

            $commission = $transaction->getTransactionAmount() * $this->config['cashOutCommissionPercentLegal'];
            $convertedLimit = $this->convertCurrency($transaction, $this->config['cashOutCommissionLegalLimitMin']);
            if ($commission < $convertedLimit) {
                return $convertedLimit;
            } else {
                return $commission;
            }
        }

        if ($transactionClient->getClientType() === Client::CLIENT_TYPE_NATURAL) {

            $date = new \DateTime($transaction->getDate());
            $week = $date->format('W');
            $clientTransactions = $this->transactionRepository->getClientTransactions($transactionClient->getClientId());
            $transactionsPerWeek = 0;
            $transactionsPerWeekAmount = 0;

            /** @var Transaction $userTransaction */
            foreach ($clientTransactions as $userTransaction) {

                $currentDate = new \DateTime($userTransaction->getDate());

                $interval = $currentDate->diff($date);
                $months_diff = (($interval->y) * 12) + ($interval->m);

                if ($week == $currentDate->format('W') && $months_diff === 0 && $userTransaction->getTransactionType() == Transaction::CASH_OUT) {

                    if ($userTransaction->getId() === $transaction->getId()) {
                        break;
                    }

                    $transactionsPerWeek ++;
                    $transactionsPerWeekAmount += $this->convertCurrency($userTransaction);
                }
            }

            if ($transactionsPerWeek >= $this->config['cashOutCommissionNormalFreeTransactions']) {
                $commission = $transaction->getTransactionAmount() * $this->config['cashOutCommissionPercentNormal'];
                return $commission;
            } else {
                if ($transactionsPerWeekAmount > $this->config['cashOutCommissionNormalDiscount']) {
                    $commission = $transaction->getTransactionAmount() * $this->config['cashOutCommissionPercentNormal'];
                    return $commission;
                } else {
                    $amount = max($this->convertCurrency($transaction) + $transactionsPerWeekAmount - $this->config['cashOutCommissionNormalDiscount'], 0);
                    $commission = $amount * $this->config['cashOutCommissionPercentNormal'];
                    return $this->convertCurrency($transaction, $commission);
                }
            }
        }

        throw new Exception("Unkown Client type");
    }

    /**
     * Converts transaction amount to EUR if $amount == -1
     * Converts $amount to transaction's currency if $amount >= 0
     *
     * @param Transaction $transaction
     * @param int $amount
     * @return float|int
     */
    private function convertCurrency(Transaction $transaction, $amount = -1)
    {
        if ($amount < 0) {
            $converted = $transaction->getTransactionAmount() / $this->config['currencies'][$transaction->getCurrency()]['conversionRate'];
        } else {
            $converted = $amount * $this->config['currencies'][$transaction->getCurrency()]['conversionRate'];
        }
        return $converted;
    }

    private function printCommission($commission, $decimals = 2)
    {
        echo number_format($commission, $decimals, '.', '') . PHP_EOL;
    }
}
