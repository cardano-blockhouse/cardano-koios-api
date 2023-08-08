<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Network;

use Spatie\LaravelData\Data;

class NetworkTip extends Data
{
    public function __construct(
        public string   $hash,
        public int      $epoch_no,
        public int      $abs_slot,
        public int      $epoch_slot,
        public int|null $block_no,
        public int      $block_time
    ) {}

}
