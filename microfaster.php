<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
set_time_limit(0);
define("DEBUG", true);

define("MICRO_ROOT", __DIR__);
require __DIR__.'/./vendor/autoload.php';

function getErrorType($type)
{
    switch ($type) {
        case E_ERROR: // 1 //
            return 'E_ERROR';
        case E_WARNING: // 2 //
            return 'E_WARNING';
        case E_PARSE: // 4 //
            return 'E_PARSE';
        case E_NOTICE: // 8 //
            return 'E_NOTICE';
        case E_CORE_ERROR: // 16 //
            return 'E_CORE_ERROR';
        case E_CORE_WARNING: // 32 //
            return 'E_CORE_WARNING';
        case E_COMPILE_ERROR: // 64 //
            return 'E_COMPILE_ERROR';
        case E_COMPILE_WARNING: // 128 //
            return 'E_COMPILE_WARNING';
        case E_USER_ERROR: // 256 //
            return 'E_USER_ERROR';
        case E_USER_WARNING: // 512 //
            return 'E_USER_WARNING';
        case E_USER_NOTICE: // 1024 //
            return 'E_USER_NOTICE';
        case E_STRICT: // 2048 //
            return 'E_STRICT';
        case E_RECOVERABLE_ERROR: // 4096 //
            return 'E_RECOVERABLE_ERROR';
        case E_DEPRECATED: // 8192 //
            return 'E_DEPRECATED';
        case E_USER_DEPRECATED: // 16384 //
            return 'E_USER_DEPRECATED';
    }
    return "";
}
/*
function echoErr($arr)
{
    echo getErrorType($arr['type']),":",$arr['message']," \nIN:",$arr['file']," ON LINE:",$arr['line'],"\n";
}

set_error_handler(function($type, $message ,$file, $line){
    if(error_reporting() == 0)
        return;
    $err = compact("type", "message", "file", "line");
    echoErr($err);
    if(DEBUG)
        exit(0);
});

register_shutdown_function(function(){
    $err = error_get_last();
    if($err)
        echoErr($err);
});
*/
(new microfaster\webserver\service)->run();