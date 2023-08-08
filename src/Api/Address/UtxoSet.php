<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Address;

use CardanoBlockhouse\CardanoKoiosApi\Api\Transactions\InlineDatum;
use CardanoBlockhouse\CardanoKoiosApi\Api\Transactions\ReferenceScript;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class UtxoSet extends Data
{
    public function __construct(
        public string               $tx_hash,
        public int                  $tx_index,
        public int|null             $block_height,
        public int                  $block_time,
        public string               $value,
        public string|null          $datum_hash,
        public InlineDatum|null     $inline_datum,
        public ReferenceScript|null $reference_script,
        #[DataCollectionOf(AssetList::class)]
        public DataCollection       $asset_list
    ) {}

}
