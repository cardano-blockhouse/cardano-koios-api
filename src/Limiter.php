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

    private $max_requests_per_10_seconds = 100;
    private const SLEEP_TIME = 60;

    private function __construct() {
        $this->requestCounter = 0;
        $this->lastRequestTime = Carbon::now();
    }

    public function setMaxRequests($maxRequests) {
        $this->max_requests_per_10_seconds = $maxRequests;
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
        if (Carbon::now()->diffInSeconds($this->lastRequestTime) > 10 ) {
            $this->requestCounter = 0;
            return 0;
        } else {
            if ($this->requestCounter < $this->max_requests_per_10_seconds) {
                return 0;
            } else {
                $this->requestCounter = 0;
                return self::SLEEP_TIME;
            }
        }
    }
}
