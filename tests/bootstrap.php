<?php
date_default_timezone_set('asia/tokyo');

require_once __DIR__ . '/../vendor/autoload.php';

use Dmm\DmmClient;

// Delete the temp test user after all tests have fired
register_shutdown_function(function () {
    //code
});