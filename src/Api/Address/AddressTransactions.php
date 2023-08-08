<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Address;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class AddressTransactions extends Data
{
    public function __construct(
        public string   $tx_hash,
        public int      $epoch_no,
        public int|null $block_height,
        public int      $block_time,
    ) {}

}
