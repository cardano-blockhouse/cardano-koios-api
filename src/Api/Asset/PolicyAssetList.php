<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Asset;

use Spatie\LaravelData\Data;

class PolicyAssetList extends Data
{
    public function __construct(
        public string|null $asset_name,
        public string      $fingerprint,
        public string      $total_supply,
        public int         $decimals,
    ) {}

}
