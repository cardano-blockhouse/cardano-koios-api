<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Asset;

use Spatie\LaravelData\Data;

class Metadata extends Data
{
    public function __construct(
        public object $metadata,
    ) {}

}
