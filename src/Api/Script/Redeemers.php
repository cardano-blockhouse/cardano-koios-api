<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Script;

use Spatie\LaravelData\Data;

class Redeemers extends Data
{
    public function __construct(
        public string      $tx_hash,
        public int         $tx_index,
        public int         $unit_mem,
        public int         $unit_steps,
        public string      $fee,
        public string      $purpose,
        public string|null $datum_hash,
        public object      $datum_value
    ) {}

}
