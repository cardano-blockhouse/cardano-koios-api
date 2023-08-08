<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\StakeAccount;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class AccountRewards extends Data
{
    public function __construct(
        public string         $stake_address,
        #[DataCollectionOf(Rewards::class)]
        public DataCollection $rewards,
    ) {}

}
