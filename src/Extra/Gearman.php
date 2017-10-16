<?php

namespace Bavix\Extra;

use Bavix\Commands\ReloadCommand;
use Bavix\Gearman\Client;
use Bavix\Gearman\Worker;
use Bavix\Helpers\JSON;

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
     * @var ReloadCommand
     */
    protected static $reload;

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
     * @return ReloadCommand
     */
    protected static function command(): ReloadCommand
    {
        if (!static::$reload)
        {
            static::$reload = new ReloadCommand();
        }

        return static::$reload;
    }

    /**
     * @param string $name
     * @param array  $args
     */
    public static function reload($name, array $args)
    {
        static::client()
            ->doBackground(static::command()->getName(), JSON::encode([
                'name' => $name,
                'args' => $args
            ]));
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
