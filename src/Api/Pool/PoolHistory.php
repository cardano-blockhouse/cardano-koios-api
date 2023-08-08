<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Pool;

use Spatie\LaravelData\Data;

class PoolHistory extends Data
{
    public function __construct(
        public int         $epoch_no,
        public string      $active_stake,
        public float       $active_stake_pct,
        public float|null  $saturation_pct,
        public int|null    $block_cnt,
        public int         $delegator_cnt,
        public float       $margin,
        public string      $fixed_cost,
        public string      $pool_fees,
        public string      $deleg_rewards,
        public string|null $member_rewards,
        public float       $epoch_ros,
    ) {}

}
