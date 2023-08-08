<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Asset;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class MintingTransactions extends Data
{
    public function __construct(
        public string  $tx_hash,
        public int    $block_time,
        public string $quantity,
        #[DataCollectionOf(Metadata::class)]
        public DataCollection $metadata,
    ) {}

}
