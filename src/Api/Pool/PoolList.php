<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Pool;

use Spatie\LaravelData\Data;

class PoolList extends Data
{
    public function __construct(
        public string|null $pool_id_bech32,
        public string|null $ticker,
    ) {}

}
