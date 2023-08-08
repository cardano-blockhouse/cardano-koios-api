<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Asset;

use Spatie\LaravelData\Data;

class TokenRegistryMetadata extends Data
{
    public function __construct(
        public string $name,
        public string $description,
        public string $ticker,
        public string $url,
        public string $logo,
        public int    $decimals,
    ) {}

}
