<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Address;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class AddressInformation extends Data
{
    public function __construct(
        public string      $address,
        public string      $balance,
        public string|null $stake_address,
        public bool        $script_address,
        #[DataCollectionOf(UtxoSet::class)]
        public DataCollection $utxo_set,
    ) {}

}
