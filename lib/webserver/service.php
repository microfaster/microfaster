<?php
namespace microfaster\webserver;

class service
{
    public $worker = 3;
    public $server;

    public function __construct()
    {
        
    }
    
    public function run()
    {
        $this->server = stream_socket_server("tcp://0.0.0.0:2018", $errno, $errstr);
        stream_set_blocking($this->server, 1);

        while($this->worker --){
            $this->fork();
        }
        
        while(1){
            $cid = pcntl_wait($status);
            if($cid > 0){
                $this->fork();
            }

            var_dump($cid);
            sleep(1);
        }
        var_dump('end');
    }

    public function fork()
    {
        $pid = pcntl_fork();
        if($pid > 0){
            var_dump($pid);
            //主进程
            return $pid;
        }elseif($pid === 0){
            //子进程
            var_dump('child');
            $this->accept();
            exit(1);
        }else
            exit("fork error!");
    }

    public function accept()
    {
        while(true){
            static $i;
            if(!$i){
                fclose(STDOUT);
                $i = true;
            }
            $fp = @stream_socket_accept($this->server, 60);
            if(!$fp)
                continue;

            $data = '';
            while (!feof($fp)) {
                $data .= fread($fp, 1);
                if(strpos($data, "\r\n\r\n") !== false)
                    break;
            }
            $GLOBALS['HEADER'] = $data;
            $GLOBALS['FP'] = $fp;
            echo "HTTP/1.1 200 OK\r\n";
            echo "Server: Microfaster\r\n";
            echo "Content-Type: text/html;charset=utf-8\r\n\r\n";
            //echo "this is ",posix_getpid();
            //var_dump($data);
            include(MICRO_ROOT."/web/index.php");
            fclose($fp);
        }
    }
    
    public function __destruct()
    {
        
    }
}