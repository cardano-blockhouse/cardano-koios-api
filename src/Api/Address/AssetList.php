<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Address;

use Spatie\LaravelData\Data;

class AssetList extends Data
{
    public function __construct(
        public string      $policy_id,
        public string|null $asset_name,
        public string      $fingerprint,
        public int         $decimals,
        public string      $quantity
    ) {}

}
