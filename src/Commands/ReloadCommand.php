<?php

namespace Bavix\Commands;

use Bavix\Extra\Gearman;
use Bavix\Helpers\JSON;

class ReloadCommand extends WorkerCommand
{

    const PROP_FN_PUSH_RELOAD = 'pushReload';

    /**
     * @var string
     */
    protected $description = 'Service of automatic updating of configurations';

    /**
     * @var bool
     */
    protected $fnUpdate = false;

    /**
     * @var string
     */
    protected $name = 'bx:reload';

    /**
     * ReloadCommand constructor.
     */
    public function __construct()
    {
        $this->map[self::PROP_FN_PUSH_RELOAD] = [
            $this,
            self::PROP_FN_PUSH_RELOAD
        ];

        parent::__construct();
    }

    /**
     * @param \GearmanJob $job
     *
     * @return void
     */
    public function pushReload(\GearmanJob $job)
    {
        $wordload = JSON::decode($job->workload());

        foreach ($wordload as $name => $args)
        {
            Gearman::client()->doBackground(
                $this->fnReload($name),
                JSON::encode($args)
            );

            $this->info('name: ' . $name . ' args: ' . $args);
        }
    }

}
