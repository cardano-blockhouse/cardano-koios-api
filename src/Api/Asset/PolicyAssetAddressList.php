<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Asset;

use Spatie\LaravelData\Data;

class PolicyAssetAddressList extends Data
{
    public function __construct(
        public string|null $asset_name,
        public string      $payment_address,
        public string      $quantity,
    ) {}

}
