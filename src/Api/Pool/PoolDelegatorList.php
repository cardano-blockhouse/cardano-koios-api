<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Pool;

use Spatie\LaravelData\Data;

class PoolDelegatorList extends Data
{
    public function __construct(
        public string $stake_address,
        public string $amount,
        public int    $active_epoch_no,
        public string $latest_delegation_tx_hash,
    ) {}

}
