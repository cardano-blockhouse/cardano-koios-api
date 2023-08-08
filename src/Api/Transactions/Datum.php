<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Transactions;

use Spatie\LaravelData\Data;

class Datum extends Data
{
    public function __construct(
        public string  $hash,
        public object  $value,
    ) {}
}
