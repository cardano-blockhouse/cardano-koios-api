<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Transactions;

use CardanoBlockhouse\CardanoKoiosApi\Api\Address\AssetList;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class Inputs extends Data
{
    public function __construct(
        public PaymentAddress       $payment_addr,
        public string|null          $stake_addr,
        public string               $tx_hash,
        public int                  $tx_index,
        public string               $value,
        public string|null          $datum_hash,
        public InlineDatum|null     $inline_datum,
        public ReferenceScript|null $reference_script,
        #[DataCollectionOf(AssetList::class)]
        public DataCollection       $asset_list,
    ) {}
}
