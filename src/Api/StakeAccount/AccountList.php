<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\StakeAccount;

use Spatie\LaravelData\Data;

class AccountList extends Data
{
    public function __construct(
        public string $id,
    ) {}

}
