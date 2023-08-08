<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Epoch;

use Spatie\LaravelData\Data;

class EpochBlockProtocols extends Data
{
    public function __construct(
        public int $proto_major,
        public int $proto_minor,
        public int $blocks,
    ) {}

}
