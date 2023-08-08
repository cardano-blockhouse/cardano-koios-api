<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Address;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class AddressAssets extends Data
{
    public function __construct(
        public string         $address,
        #[DataCollectionOf(AssetList::class)]
        public DataCollection $asset_list
    ) {}

}
