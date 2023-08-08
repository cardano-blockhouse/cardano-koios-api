<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Block;

use Spatie\LaravelData\Data;

class BlockInformation extends Data
{
    public function __construct(
        public string $hash,
        public int $epoch_no,
        public int $abs_slot,
        public int $epoch_slot,
        public int|null $block_height,
        public int $block_size,
        public int $block_time,
        public int $tx_count,
        public string $vrf_key,
        public string $op_cert,
        public int $op_cert_counter,
        public string|null $pool,
        public int|null $proto_major,
        public int|null $proto_minor,
        public string|null $total_output,
        public string|null $total_fees,
        public int $num_confirmations,
        public string $parent_hash,
        public string $child_hash,
    ) {}

}

