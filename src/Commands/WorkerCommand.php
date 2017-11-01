<?php

namespace Bavix\Commands;

use Bavix\Exceptions\Invalid;
use Bavix\Exceptions\NotFound\Path;
use Bavix\Exceptions\PermissionDenied;
use Bavix\Extra\Gearman;
use Bavix\Config\Config;
use Bavix\Gearman\Worker;
use Bavix\Helpers\Arr;
use Bavix\Helpers\Closure;
use Bavix\Helpers\JSON;
use Illuminate\Console\Command;

abstract class WorkerCommand extends Command
{

    /**
     * @var string
     */
    protected $name = 'worker:default';

    /**
     * @var bool
     */
    protected $fnUpdate = true;

    /**
     * @var Worker
     */
    protected $worker;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var array
     */
    protected $map = [];

    /**
     * WorkerDefault constructor.
     *
     * @throws PermissionDenied
     * @throws Invalid
     */
    public function __construct()
    {
        parent::__construct();
        $this->config = new Config(config_path());
        $this->worker = Gearman::worker();
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function fnReload($name = null): string
    {
        return ($name ?: $this->getName()) . '-' . __FUNCTION__;
    }

    /**
     * @param \GearmanJob $job
     *
     * @throws PermissionDenied
     * @throws Invalid
     * @throws Path
     */
    public function fnUpdate(\GearmanJob $job)
    {
        $this->config->cleanup();

        /**
         * @var array $workload
         */
        $workload = JSON::decode($job->workload());

        foreach ($workload as $config)
        {
            $this->info('update config: ' . $config);

            \config([
                $config => $this->config
                    ->get($config)
                    ->asArray()
            ]);
        }
    }

    /**
     * Execute the console command.
     *
     * @return null
     */
    public function handle()
    {
        if ($this->fnUpdate)
        {
            $this->worker->addFunction(
                $this->fnReload(),
                [$this, 'fnUpdate']
            );
        }

        foreach ($this->map as $name => $callable)
        {
            $this->worker->addFunction($name, $callable);
        }

        while ($this->worker->work())
        {
            continue;
        }

        return null;
    }

}
