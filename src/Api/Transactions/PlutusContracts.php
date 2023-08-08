<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Transactions;

use Spatie\LaravelData\Data;

class PlutusContracts extends Data
{
    public function __construct(
        public string|null  $address,
        public string       $script_hash,
        public string       $bytecode,
        public int          $size,
        public bool         $valid_contract,
        public Input        $input,
    ) {}
}
