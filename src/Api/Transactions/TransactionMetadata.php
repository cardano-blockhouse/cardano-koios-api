<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Transactions;

use Spatie\LaravelData\Data;

class TransactionMetadata extends Data
{
    public function __construct(
        public string $tx_hash,
        public object|null $metadata,
    ) {}
}
