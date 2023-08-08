<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Transactions;

use Spatie\LaravelData\Data;

class Withdrawls extends Data
{
    public function __construct(
        public string $amount,
        public string $stake_addr,
    ) {}
}
