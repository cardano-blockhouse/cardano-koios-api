<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Transactions;

use Spatie\LaravelData\Data;

class Certificates extends Data
{
    public function __construct(
        public int|null    $index,
        public string      $type,
        public object|null $info
    ) {}
}
