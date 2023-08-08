<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Pool;

use Spatie\LaravelData\Data;

class PoolStakeSnapshot extends Data
{
    public function __construct(
        public string      $snapshot,
        public int         $epoch_no,
        public string|null $nonce,
        public string      $pool_stake,
        public string      $active_stake,
    ) {}

}
