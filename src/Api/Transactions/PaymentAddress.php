<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Transactions;

use Spatie\LaravelData\Data;

class PaymentAddress extends Data
{
    public function __construct(
        public string $bech32,
        public string $cred,
    ) {}

}
