<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Transactions;

use Spatie\LaravelData\Data;

class TransactionStatus extends Data
{
    public function __construct(
        public string   $tx_hash,
        public int|null $num_confirmations,
    ) {}
}
