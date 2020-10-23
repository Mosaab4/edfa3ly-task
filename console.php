<?php

// require autoloader
require __DIR__.'/vendor/autoload.php';

use App\Command\CreateCartCommand;
use App\Command\ShowProductCommand;
use Symfony\Component\Console\Application;

// create instance of the console application
$application = new Application();

// register available commands
$application->add(new CreateCartCommand());
$application->add(new ShowProductCommand());


// run the application
$application->run();