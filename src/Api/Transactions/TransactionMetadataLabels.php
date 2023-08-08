<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Transactions;

use Spatie\LaravelData\Data;

class TransactionMetadataLabels extends Data
{
    public function __construct(
        public string $key,
    ) {}
}
