<?php

namespace Component;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class MonologService
{
    private $monolog;

    public function __construct()
    {
        $this->monolog = new Logger('monolog');
        $this->monolog->pushHandler(new StreamHandler('../resources/loggs/monolog.txt', Logger::WARNING));
    }

    public function getDependency():Logger
    {
        return $this->monolog;
    }

}