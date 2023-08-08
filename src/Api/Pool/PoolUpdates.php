<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Pool;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class PoolUpdates extends Data
{
    public function __construct(
        public string         $tx_hash,
        public int            $block_time,
        public string         $pool_id_bech32,
        public string         $pool_id_hex,
        public int            $active_epoch_no,
        public string         $vrf_key_hash,
        public float          $margin,
        public string         $fixed_cost,
        public string         $pledge,
        public string         $reward_addr,
        public array          $owners,
        #[DataCollectionOf(Relay::class)]
        public DataCollection $relays,
        public string|null    $meta_url,
        public string|null    $meta_hash,
        public MetaJson|null  $meta_json,
        public string         $pool_status,
        public int|null       $retiring_epoch,
    ) {}

}
