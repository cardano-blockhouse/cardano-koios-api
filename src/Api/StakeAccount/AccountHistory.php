<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\StakeAccount;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class AccountHistory extends Data
{
    public function __construct(
        public string         $stake_address,
        #[DataCollectionOf(History::class)]
        public DataCollection $history,
    ) {}

}
