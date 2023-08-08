<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Transactions;

use Spatie\LaravelData\Data;

class Input extends Data
{
    public function __construct(
        public Redeemer $redeemer,
        public Datum    $datum,
    ) {}
}
