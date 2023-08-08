<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Pool;

use Spatie\LaravelData\Data;

class Relay extends Data
{
    public function __construct(
        public string|null $dns,
        public string|null $srv,
        public string|null $ipv4,
        public string|null $ipv6,
        public string|null $port,
    ) {}

}
