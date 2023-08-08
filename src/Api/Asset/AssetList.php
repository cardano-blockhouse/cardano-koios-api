<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Asset;

use Spatie\LaravelData\Data;

class AssetList extends Data
{
    public function __construct(
        public string      $policy_id,
        public string|null $asset_name,
        public string      $fingerprint,
    ) {}

}
