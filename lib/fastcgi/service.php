<?php
namespace microfaster;

use microfaster\service\cgi;
use microfaster\service\handle;

class fastcgi
{
    public function __construct()
    {
        
    }
    
    public function run()
    {
        $fastcgi_server = stream_socket_server("tcp://0.0.0.0:2018", $errno, $errstr);
        stream_set_blocking($fastcgi_server, 1);
        
        $handle_server = stream_socket_server("tcp://127.0.0.1:2019", $errno, $errstr);
        stream_set_blocking($handle_server, 1);
        
        $pid = pcntl_fork();
        if($pid === 0)
        {
            $cgi = new cgi;
            while(true)
            {
                $cgi->accept($fastcgi_server);
            }
            return;
        }
        $pid = pcntl_fork();
        if($pid === 0)
        {
            $handle = new handle;
            fclose(STDOUT);
            while(true)
            {
                $handle->accept($handle_server);
            }
            return;
        }
        var_dump('end');
    }
    
    public function __destruct()
    {
        echo "__destruct";
    }
}