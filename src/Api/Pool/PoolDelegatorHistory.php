<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Pool;

use Spatie\LaravelData\Data;

class PoolDelegatorHistory extends Data
{
    public function __construct(
        public string $stake_address,
        public string $amount,
        public int    $epoch_no,
    ) {}

}
