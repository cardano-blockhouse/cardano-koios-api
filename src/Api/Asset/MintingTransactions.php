<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Asset;

use Spatie\LaravelData\Data;

class MintingTransactions extends Data
{
    public function __construct(
        public string $tx_hash,
        public int    $block_time,
        public string $quantity,
        public array  $metadata,
    ) {}

}
