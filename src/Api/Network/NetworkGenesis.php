<?php

namespace CardanoBlockhouse\CardanoKoiosApi\Api\Network;

use Spatie\LaravelData\Data;

class NetworkGenesis extends Data
{
    public function __construct(
        public string $networkmagic,
        public string $networkid,
        public string $epochlength,
        public string $slotlength,
        public string $maxlovelacesupply,
        public int    $systemstart,
        public string $activeslotcoeff,
        public string $slotsperkesperiod,
        public string $maxkesrevolutions,
        public string $securityparam,
        public string $updatequorum,
        public string $alonzogenesis,
    ) {}

}
