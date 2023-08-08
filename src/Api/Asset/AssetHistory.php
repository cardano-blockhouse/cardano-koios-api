<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Asset;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class AssetHistory extends Data
{
    public function __construct(
        public string      $policy_id,
        public string|null $asset_name,
        public string      $fingerprint,
        #[DataCollectionOf(MintingTransactions::class)]
        public DataCollection $minting_txs,
    ) {}

}
