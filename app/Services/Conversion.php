<?php

    namespace CommissionCalc\Services;

use CommissionCalc\Models\Transaction;

class Conversion
{

    protected $config;
    
    public function __construct(array $config)
    {
        // Load config
        $this->config = $config;
    }

    /**
     * Converts transaction amount to EUR if $amount == -1
     * Converts $amount to transaction's currency if $amount >= 0
     *
     * @param Transaction $transaction
     * @param int $amount
     * @return float|int
     */
    public function convert(Transaction $transaction, $amount = -1)
    {
        if ($amount < 0) {
            $converted = $transaction->getTransactionAmount() / $this->config['currencies'][$transaction->getCurrency()]['conversionRate'];
        } else {
            $converted = $amount * $this->config['currencies'][$transaction->getCurrency()]['conversionRate'];
        }
        return $converted;
    }
}