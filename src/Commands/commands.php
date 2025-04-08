<?php

use FrameJam\Commands\MigrateCommand;
use FrameJam\Commands\RollbackCommand;
use Symfony\Component\Console\Application;

$application = new Application('FrameJam', '1.0.0');

$application->add(new MigrateCommand());
$application->add(new RollbackCommand());

return $application; 