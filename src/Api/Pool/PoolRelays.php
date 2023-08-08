<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Pool;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class PoolRelays extends Data
{
    public function __construct(
        public string         $pool_id_bech32,
        #[DataCollectionOf(Relay::class)]
        public DataCollection $relays,
    ) {}

}
