<?php

    namespace CommissionCalc\Services;

use CommissionCalc\Models\Transaction;
use CommissionCalc\Models\Client;
use CommissionCalc\Repositories\TransactionsRepository;

class Commission
{

    protected $transactionRepository;

    protected $config;

    protected $conversion;

    public function __construct(TransactionsRepository $repository, array $config)
    {
        $this->transactionRepository = $repository;

        $this->config = $config;

        // Load Conversion service
        $this->conversion = new Conversion($config);
    }

    public function calculate(Transaction $transaction)
    {
        if ($transaction->getTransactionType() === Transaction::CASH_IN) {

            return $this->cashInCommission($transaction);
        }

        return $this->cashOutCommission($transaction);
    }

    public function cashInCommission(Transaction $transaction)
    {
        $commission = $transaction->getTransactionAmount() * $this->config['cashInCommissionPercent'];
        $convertedLimit = $this->conversion->convert($transaction, $this->config['cashInCommissionLimitMax']);

        if ($commission > $convertedLimit) {
            return $convertedLimit;
        }

        return $commission;
    }

    /**
     * Cash Out commission calculation for natural and legal users
     *
     * @param Transaction $transaction
     * @return float|int
     */
    public function cashOutCommission(Transaction $transaction)
    {
        $transactionClient = $transaction->getClient();

        if ($transactionClient->getClientType() === Client::CLIENT_TYPE_LEGAL) {

            $commission = $transaction->getTransactionAmount() * $this->config['cashOutCommissionPercentLegal'];
            $convertedLimit = $this->conversion->convert($transaction, $this->config['cashOutCommissionLegalLimitMin']);

            if ($commission < $convertedLimit) {
                return $convertedLimit;
            }

            return $commission;
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
                    $transactionsPerWeekAmount += $this->conversion->convert($userTransaction);
                }
            }

            if ($transactionsPerWeek >= $this->config['cashOutCommissionNormalFreeTransactions']) {
                $commission = $transaction->getTransactionAmount() * $this->config['cashOutCommissionPercentNormal'];
                return $commission;
            }

            if ($transactionsPerWeekAmount > $this->config['cashOutCommissionNormalDiscount']) {
                $commission = $transaction->getTransactionAmount() * $this->config['cashOutCommissionPercentNormal'];
                return $commission;
            }

            $amount = max($this->conversion->convert($transaction) + $transactionsPerWeekAmount - $this->config['cashOutCommissionNormalDiscount'], 0);
            $commission = $amount * $this->config['cashOutCommissionPercentNormal'];
            return $this->conversion->convert($transaction, $commission);
        }

        throw new Exception("Unkown Client type");
    }
}