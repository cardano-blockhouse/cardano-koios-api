<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Transactions;

use Spatie\LaravelData\Data;

class Unit extends Data
{
    public function __construct(
        public string $steps,
        public string $mem,
    ) {}
}
