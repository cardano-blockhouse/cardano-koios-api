<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\StakeAccount;

use Spatie\LaravelData\Data;

class Updates extends Data
{
    public function __construct(
        public string $action_type,
        public string $tx_hash,
        public int    $epoch_no,
        public int    $epoch_slot,
        public int    $absolute_slot,
        public int    $block_time,
    ) {}

}
