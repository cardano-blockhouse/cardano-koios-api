<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Transactions;

use Spatie\LaravelData\Data;

class Redeemer extends Data
{
    public function __construct(
        public string $purpose,
        public string $fee,
        public Unit   $unit,
        public Datum  $datum
    ) {}
}
