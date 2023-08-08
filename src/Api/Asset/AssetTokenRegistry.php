<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Asset;

use Spatie\LaravelData\Data;

class AssetTokenRegistry extends Data
{
    public function __construct(
        public string      $policy_id,
        public string|null $asset_name,
        public string      $asset_name_ascii,
        public string      $ticker,
        public string      $description,
        public string      $url,
        public int         $decimals,
        public string      $logo,
    ) {}

}
