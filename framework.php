<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Shanghai');

set_error_handler(function ($severity, $message, $file, $line) {
    throw new \ErrorException($message, 0, $severity, $file, $line);
});

register_shutdown_function(function () {
    if ($err = error_get_last()) {
        http_response_code(500);
        echo json_encode($err);
    }
});

require "vendor/autoload.php";
