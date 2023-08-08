<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Pool;

use Spatie\LaravelData\Data;

class MetaJson extends Data
{
    public function __construct(
        public string $name,
        public string $ticker,
        public string $homepage,
        public string $description,
    ) {}

}
