<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Transactions;

use CardanoBlockhouse\CardanoKoiosApi\Api\Address\AssetList;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class TransactionInfos extends Data
{
    public function __construct(
        public string          $tx_hash,
        public string          $block_hash,
        public int|null        $block_height,
        public int             $epoch_no,
        public int             $epoch_slot,
        public int             $absolute_slot,
        public int             $tx_timestamp,
        public int             $tx_block_index,
        public int             $tx_size,
        public string          $total_output,
        public string          $fee,
        public string          $deposit,
        public string|null     $invalid_before,
        public string|null     $invalid_after,
        #[DataCollectionOf(Collateral::class)]
        public DataCollection  $collateral_inputs,
        public Collateral|null $collateral_output,
        #[DataCollectionOf(ReferenceInputs::class)]
        public DataCollection  $reference_inputs,
        #[DataCollectionOf(Inputs::class)]
        public DataCollection  $inputs,
        #[DataCollectionOf(Outputs::class)]
        public DataCollection  $outputs,
        #[DataCollectionOf(Withdrawls::class)]
        public DataCollection  $withdrawals,
        #[DataCollectionOf(AssetList::class)]
        public DataCollection  $assets_minted,
        public object|null     $metadata,
        #[DataCollectionOf(Certificates::class)]
        public DataCollection  $certificates,
        #[DataCollectionOf(NativeScripts::class)]
        public DataCollection  $native_scripts,
        #[DataCollectionOf(PlutusContracts::class)]
        public DataCollection  $plutus_contracts,
    ) {}

}
