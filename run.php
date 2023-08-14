<?php

declare(strict_types=1);

use HPT\Dispatcher;
use HPT\HPTGrabber;
use HPT\HPTOutput;

require_once __DIR__ . '/vendor/autoload.php';

$grabber = new HPTGrabber();
$output = new HPTOutput();

$dispatcher = new Dispatcher($grabber,$output);
$json = $dispatcher->run();
echo $json;
