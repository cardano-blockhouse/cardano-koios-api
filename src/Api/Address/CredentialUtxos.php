<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Address;

use Spatie\LaravelData\Data;

class CredentialUtxos extends Data
{
    public function __construct(
        public string $tx_hash,
        public int    $tx_index,
        public string $value,
    ) {}

}
