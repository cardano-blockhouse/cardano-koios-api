<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Asset;

use Spatie\LaravelData\Data;

class AssetSummary extends Data
{
    public function __construct(
        public string      $policy_id,
        public string|null $asset_name,
        public string      $fingerprint,
        public int         $total_transactions,
        public int         $staked_wallets,
        public int         $unstaked_addresses,
    ) {}

}
