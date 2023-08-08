<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Asset;

use Spatie\LaravelData\Data;

class AssetTransactions extends Data
{
    public function __construct(
        public string   $tx_hash,
        public int      $epoch_no,
        public int|null $block_height,
        public int      $block_time,
    ) {}

}
