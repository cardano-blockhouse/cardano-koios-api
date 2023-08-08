<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Epoch;

use Spatie\LaravelData\Data;

class EpochParams extends Data
{
    public function __construct(
        public int         $epoch_no,
        public int|null    $min_fee_a,
        public int|null    $min_fee_b,
        public int|null    $max_block_size,
        public int|null    $max_tx_size,
        public int|null    $max_bh_size,
        public string|null $key_deposit,
        public string|null $pool_deposit,
        public int|null    $max_epoch,
        public int|null    $optimal_pool_count,
        public int|null    $influence,
        public int|null    $monetary_expand_rate,
        public int|null    $treasury_growth_rate,
        public int|null    $decentralisation,
        public string|null $extra_entropy,
        public int|null    $protocol_major,
        public int|null    $protocol_minor,
        public string|null $min_utxo_value,
        public string|null $min_pool_cost,
        public string|null $nonce,
        public string      $block_hash,
        public string|null $cost_models,
        public int|null    $price_mem,
        public int|null    $price_step,
        public int|null    $max_tx_ex_mem,
        public int|null    $max_tx_ex_steps,
        public int|null    $max_block_ex_mem,
        public int|null    $max_block_ex_steps,
        public int|null    $max_val_size,
        public int|null    $collateral_percent,
        public int|null    $max_collateral_inputs,
        public string|null $coins_per_utxo_size,
    ) {}

}
