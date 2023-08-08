<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Pool;

use Spatie\LaravelData\Data;

class PoolBlocks extends Data
{
    public function __construct(
        public int      $epoch_no,
        public int      $epoch_slot,
        public int      $abs_slot,
        public int|null $block_height,
        public string   $block_hash,
        public int      $block_time,
    ) {}

}
