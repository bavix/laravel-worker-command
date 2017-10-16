<?php

namespace Bavix\Commands;

use Bavix\Extra\Gearman;
use Bavix\Helpers\JSON;

class ReloadCommand extends WorkerCommand
{

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
        $this->map['pushReload'] = [$this, 'pushReload'];

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

        Gearman::client()->doBackground(
            $this->fnReload($wordload['name']),
            JSON::encode($wordload['args'])
        );
    }

}
