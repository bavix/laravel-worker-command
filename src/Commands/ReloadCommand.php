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
        $this->info(__FUNCTION__);
        $workload = $job->workload();

        $directoryIterator = new \RecursiveDirectoryIterator(
            app_path('Console/Commands'),
            \RecursiveDirectoryIterator::SKIP_DOTS
        );

        $iterator = new \RecursiveIteratorIterator($directoryIterator);

        /**
         * @var $file \SplFileInfo
         */
        foreach ($iterator as $file)
        {
            $namespace = \App\Console\Commands::class;
            $name = str_replace('.php', '', $file->getFilename());
            $class = $namespace . '\\' . $name;

            /**
             * @var WorkerCommand $object
             */
            $object = new $class;

            Gearman::client()->doBackground(
                $this->fnReload($object->getName()),
                $workload
            );

            $this->info('class: ' . $name . ', args: ' . $workload);
        }
    }

}
