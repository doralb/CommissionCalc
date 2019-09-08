<?php
return [
    'cashOutCommissionPercentNormal' => 0.003,
    'cashOutCommissionPercentLegal' => 0.003,
    'cashOutCommissionLegalLimitMin' => 0.5,
    'cashOutCommissionNormalFreeTransactions' => 3,
    'cashOutCommissionNormalDiscount' => 1000,
    'cashInCommissionLimitMax' => 5,
    'cashInCommissionPercent' => 0.0003,
    'commissionPrecision' => 2,
    'currencies' => [
        'EUR' => [
            'conversionRate' => 1,
            'decimalPrecision' => 2
        ],
        'USD' => [
            'conversionRate' => 1.1497,
            'decimalPrecision' => 2
        ],
        'JPY' => [
            'conversionRate' => 129.53,
            'decimalPrecision' => 0
        ]
    ]
];