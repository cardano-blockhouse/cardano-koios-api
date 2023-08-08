<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\StakeAccount;

use Spatie\LaravelData\Data;

class AccountTransactions extends Data
{
    public function __construct(
        public string   $tx_hash,
        public int      $tx_index,
        public string   $address,
        public string   $value,
        public int|null $block_height,
        public int      $block_time,
    ) {}

}
