<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Transactions;

use Spatie\LaravelData\Data;

class NativeScripts extends Data
{
    public function __construct(
        public string  $script_hash,
        public object  $script_json,
    ) {}
}
