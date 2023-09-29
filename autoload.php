<?php

ini_set('display_errors', 'on');
error_reporting(E_ALL);

require_once(__DIR__ . '/vendor/autoload.php');

spl_autoload_register(function ($class) {
    require_once(__DIR__ . '/classes/' . $class . '.php');
});
