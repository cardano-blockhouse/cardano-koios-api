<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Pool;

use Spatie\LaravelData\Data;

class PoolMetadata extends Data
{
    public function __construct(
        public string        $pool_id_bech32,
        public string|null   $meta_url,
        public string|null   $meta_hash,
        public MetaJson|null $meta_json,
    ) {}

}
