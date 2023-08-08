<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Epoch;

use Spatie\LaravelData\Data;

class EpochInfo extends Data
{
    public function __construct(
        public int         $epoch_no,
        public string      $out_sum,
        public string      $fees,
        public int         $tx_count,
        public int         $blk_count,
        public int         $start_time,
        public int         $end_time,
        public int         $first_block_time,
        public int         $last_block_time,
        public string|null $active_stake,
        public string|null $total_rewards,
        public string|null $avg_blk_reward,
    ) {}

}
