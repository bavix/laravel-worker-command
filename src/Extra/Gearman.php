<?php

namespace Bavix\Extra;

use Bavix\Gearman\Client;
use Bavix\Gearman\Worker;

class Gearman
{

    /**
     * @var Worker
     */
    protected static $worker;

    /**
     * @var Client
     */
    protected static $client;

    /**
     * @param Client|Worker $obj
     */
    protected static function addServer($obj)
    {
        $servers = \config('gearman.servers');

        foreach ($servers as $server)
        {
            $obj->addServer(
                $server['host'],
                $server['port'] ?? 4730
            );
        }
    }

    /**
     * @return Client
     */
    public static function client(): Client
    {
        if (!static::$client)
        {
            static::$client = new Client();
            static::addServer(static::$client);
        }

        return static::$client;
    }

    /**
     * @return Worker
     */
    public static function worker(): Worker
    {
        if (!static::$worker)
        {
            static::$worker = new Worker();
            static::addServer(static::$worker);
        }

        return static::$worker;
    }

}
