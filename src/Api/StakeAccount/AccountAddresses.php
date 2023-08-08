<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\StakeAccount;

use Spatie\LaravelData\Data;

class AccountAddresses extends Data
{
    public function __construct(
        public string $stake_address,
        public array  $addresses,
    ) {}

}
