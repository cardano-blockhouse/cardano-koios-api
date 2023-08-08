<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Transactions;

use Spatie\LaravelData\Data;

class ReferenceScript extends Data
{
    public function __construct(
        public string      $hash,
        public int         $size,
        public string      $type,
        public string      $bytes,
        public object|null $value
    ) {}

}
