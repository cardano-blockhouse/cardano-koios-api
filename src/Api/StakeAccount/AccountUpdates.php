<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\StakeAccount;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class AccountUpdates extends Data
{
    public function __construct(
        public string         $stake_address,
        #[DataCollectionOf(Updates::class)]
        public DataCollection $updates,
    ) {}

}
