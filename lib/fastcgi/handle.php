<?php
namespace microfaster\service;

class handle
{
    public $connection;
    
    public function accept($handle_server)
    {
        $GLOBALS['top_domain_connection'] = //防止出错时提前释放
        $this->connection = @stream_socket_accept($handle_server, 1);
        
        if(!$this->connection)
            return;
        
        include MICRO_ROOT."/web/index.php";
        fclose($this->connection);
    }
}