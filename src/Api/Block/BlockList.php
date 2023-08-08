<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Block;

use Spatie\LaravelData\Data;

class BlockList extends Data
{
    public function __construct(
        public string      $hash,
        public int         $epoch_no,
        public int         $abs_slot,
        public int         $epoch_slot,
        public int|null    $block_height,
        public int         $block_size,
        public int         $block_time,
        public int         $tx_count,
        public string      $vrf_key,
        public string|null $pool,
        public int         $op_cert_counter,
        public int|null    $proto_major,
        public int|null    $proto_minor,
    ) {}

}
