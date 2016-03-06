<?php

require_once "vendor/autoload.php";
$conf = require 'config.php';

$app = new \SlimRest\App($conf);

// Register Entity Resource
// init resources
new \SlimRest\Resource\Auth();
// end init resources

$app->run();
