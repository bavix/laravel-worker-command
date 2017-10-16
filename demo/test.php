<?php

require dirname(__DIR__) . '/vendor/autoload.php';

// reload config bx:test
\Bavix\Extra\Gearman::reload([
  'bx:test' => ['test']
]);

/**
 * $client = new \GearmanClient();
 * ...
 * $client->doBackground('echo', 'hello world');
 */
\Bavix\Extra\Gearman::client()
  ->doBackground('echo', 'hello world');
