<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Script;

use Spatie\LaravelData\Data;

class NativeScriptList extends Data
{
    public function __construct(
        public string $script_hash,
        public string $creation_tx_hash,
        public string $type,
    ) {}

}
