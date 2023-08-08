<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Block;

use Spatie\LaravelData\Data;

class BlockTransactions extends Data
{
    public function __construct(
        public string $block_hash,
        public array $tx_hashes,
    ) {}

}
