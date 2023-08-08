<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\StakeAccount;

use Spatie\LaravelData\Data;

class AccountInformation extends Data
{
    public function __construct(
        public string $stake_address,
        public string $status,
        public string|null $delegated_pool,
        public string $total_balance,
        public string $utxo,
        public string $rewards,
        public string $withdrawals,
        public string $rewards_available,
        public string $reserves,
        public string $treasury,
    ) {}

}
