<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\StakeAccount;

use CardanoBlockhouse\CardanoKoiosApi\Api\Address\AssetList;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class AccountAssets extends Data
{
    public function __construct(
        public string         $stake_address,
        #[DataCollectionOf(AssetList::class)]
        public DataCollection $asset_list
    ) {}

}
