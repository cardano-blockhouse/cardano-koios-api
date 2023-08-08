<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Network;

use Spatie\LaravelData\Data;

class NetworkParamUpdates extends Data
{
    public function __construct(
        public string   $tx_hash,
        public int|null $block_height,
        public int      $block_time,
        public int      $epoch_no,
        public string   $data
    ) {}
}
