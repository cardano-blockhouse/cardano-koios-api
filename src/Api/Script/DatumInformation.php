<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Script;

use Spatie\LaravelData\Data;

class DatumInformation extends Data
{
    public function __construct(
        public string|null $hash,
        public object      $value,
        public string      $bytes,
    ) {}

}
