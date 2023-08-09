<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Script;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class ScriptRedeemer extends Data
{
    public function __construct(
        public string $script_hash,
        #[DataCollectionOf(Redeemers::class)]
        public DataCollection $redeemers,
    ) {}

}
