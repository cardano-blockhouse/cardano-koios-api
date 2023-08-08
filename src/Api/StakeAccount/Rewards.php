<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\StakeAccount;

use Spatie\LaravelData\Data;

class Rewards extends Data
{
    public function __construct(
        public int         $earned_epoch,
        public int         $spendable_epoch,
        public string      $amount,
        public string      $type,
        public string|null $pool_id,
    ) {}

}
