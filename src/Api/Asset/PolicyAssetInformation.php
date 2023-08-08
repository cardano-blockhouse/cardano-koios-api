<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Asset;

use Spatie\LaravelData\Data;

class PolicyAssetInformation extends Data
{
    public function __construct(
        public string|null                $asset_name,
        public string                     $asset_name_ascii,
        public string                     $fingerprint,
        public string                     $minting_tx_hash,
        public string                     $total_supply,
        public int                        $mint_cnt,
        public int                        $burn_cnt,
        public int                        $creation_time,
        public object|null                $minting_tx_metadata,
        public TokenRegistryMetadata|null $token_registry_metadata,
    ) {}

}
