<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Address;

use Spatie\LaravelData\Data;

class CredentialTransactions extends Data
{
    public function __construct(
        public string   $tx_hash,
        public int      $epoch_no,
        public int|null $block_height,
        public int      $block_time,
    ) {}

}
