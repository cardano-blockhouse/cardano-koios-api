<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\StakeAccount;

use Spatie\LaravelData\Data;

class History extends Data
{
    public function __construct(
        public string $pool_id,
        public int    $epoch_no,
        public string $active_stake,
    ) {}

}
