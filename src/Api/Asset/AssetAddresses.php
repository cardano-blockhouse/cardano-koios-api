<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Asset;

use Spatie\LaravelData\Data;

class AssetAddresses extends Data
{
    public function __construct(
        public string $payment_address,
        public string $quantity,
    ) {}

}
