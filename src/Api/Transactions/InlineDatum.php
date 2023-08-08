<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Transactions;

use Spatie\LaravelData\Data;

class InlineDatum extends Data
{
    public function __construct(
        public string  $bytes,
        public object  $value,
    ) {}
}
