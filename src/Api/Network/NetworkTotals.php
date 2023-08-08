<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Network;

use Spatie\LaravelData\Data;

class NetworkTotals extends Data
{
    public function __construct(
        public int    $epoch_no,
        public string $circulation,
        public string $treasury,
        public string $reward,
        public string $supply,
        public string $reserves,
    ) {}

}
