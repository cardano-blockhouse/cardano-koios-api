<?php

namespace CardanoBlockhouse\CardanoKoiosApi;

use Carbon\Carbon;

class Limiter
{

    public static function create():Limiter {
        if (Limiter::$INSTANCE == null) {
            Limiter::$INSTANCE = new Limiter();
        }
        return Limiter::$INSTANCE;
    }

    private static $INSTANCE = null;

    private $requestCounter;
    private $lastRequestTime;

    private const MAX_REQUESTS_PER_10_SECONDS = 100;
    private const SLEEP_TIME = 60;

    private function __construct() {
        $this->requestCounter = 0;
        $this->lastRequestTime = Carbon::now();
    }

    public function nextRequest() {

        $sleepTime = $this->getSleepTime();

        if ($sleepTime > 0) {
            sleep($sleepTime);
        }
        $this->lastRequestTime = Carbon::now();
        $this->requestCounter++;
    }

    private function getSleepTime() {
        if (Carbon::now()->diffInSeconds($this->lastRequestTime) > 60 ) {
            $this->requestCounter = 0;
            return 0;
        } else {
            if ($this->requestCounter < self::MAX_REQUESTS_PER_10_SECONDS) {
                return 0;
            } else {
                $this->requestCounter = 0;
                return self::SLEEP_TIME;
            }
        }
    }
}
