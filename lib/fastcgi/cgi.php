<?php
namespace microfaster\service;

class cgi
{
    static public $FCGI_Header = array(
        'version' => 1,
        'type' => 1,
        'requestId' => 2,
        'contentLength' => 2,
        'paddingLength' => 1,
        'reserved' => 1,
    );
    
    static public $INIT_Request = array(
        'role' => 2,
        'flags' => 1,
        'reserved' => 5,
    );
    
    public $connection;
    
    public function accept($fastcgi_server)
    {
        $this->connection = @stream_socket_accept($fastcgi_server, 1);
        
        if(!$this->connection)
            return;
        
        $env = array();
        $stdin = '';
        
        while(true)
        {
            if(feof($this->connection))
                break;
            
            $head = $this->read('FCGI_Header');
            var_dump($head);
            switch($head['type'])
            {
                case 1:
                    var_dump(1);
                    $init = $this->read('INIT_Request');
                    //var_dump($init);
                    break;
                case 4:
                    var_dump(4);
                    if($head['contentLength'] > 0)
                    {
                        $type4 = $this->read_type4_request($head['contentLength'], $head['paddingLength']);
                        $env = array_merge($env, $type4);
                    }
                    break;
                case 5:
                    var_dump(5);
                    if($head['contentLength'] > 0)
                    {
                        $stdin .= fread($this->connection, $head['contentLength']);
                        fread($this->connection, $head['paddingLength']);
                    }
                    
                    $r = fsockopen("127.0.0.1", 2019, $e1, $e2);
                    if($r === false)
                        $message = "";
                    else
                    {
                        $message = fread($r, 1024);
                    }
                    
                    $message = "Content-type: text/html\r\n\r\n".$message;
                    //var_dump($message);
                    $this->request($message, $head['requestId']);
                    
                    
                    if($init['flags'] == 0)
                    {
                        fclose($this->connection);
                        return;
                    }
                    break;
            }
            
        }
        
    }
    
    public function request($message, $requestId)
    {
        $s = '';
        $s .= chr(1);
        $s .= chr(6);
        $s .= pack('n', $requestId);
        $s .= pack('n', strlen($message));
        $padding = 8-(strlen($message)%8);
        $s .= pack('C', $padding);
        $s .= chr(0);
        $s .= $message;
        for($i = 0; $i < $padding; $i++)
        {
            $s .= chr(0);
        }
        $s .= pack('C2n1C4', 1, 3, $requestId, 0, 8, 0, 0);
        $s .= pack('C8', 0, 0, 0, 0, 0, 0, 0, 0);
        //$s .= pack('C2n1C4', 1, 3, $requestId, 0, 0, 0, 0);
        var_dump("count:".strlen($s));
        fwrite($this->connection, $s);
        //sleep(1);
        //var_dump("end write:");
        //var_dump(feof($this->connection));
    }
    
    public function read($name)
    {
        //var_dump('start read');
        $res = array();
        foreach(self::$$name as $k => $length)
        {
            
            $res[$k] = fread($this->connection, $length);
            
            for($i = 0; $i < $length; $i++)
            {
                //var_dump(ord($res[$k][$i]));
            }
            
            switch($length)
            {
                case 1:
                    $res[$k] = current(unpack('C', $res[$k]));
                    break;
                case 2:
                    $res[$k] = current(unpack('n', $res[$k]));
                    break;
                case 4:
                    $res[$k] = current(unpack('N', $res[$k]));
                    break;
                case 8:
                    $res[$k] = current(unpack('Q', $res[$k]));
                    break;
            }
        }
        return $res;
    }
    
    public function read_type4_request($contentLength, $paddingLength)
    {
        //var_dump([$contentLength, $paddingLength]);
        $do = 0;
        $arr = array();
        while($contentLength > $do)
        {
            $name_len = ord(fread($this->connection, 1));
            $do += 1;
            if($name_len > 127)
            {
                $name_len = (($name_len & 0x7f) << 24) + (ord(fread($this->connection, 1)) << 16)
                    + (ord(fread($this->connection, 1)) << 8) + ord(fread($this->connection, 1));
                $do += 3;
            }
            
            $value_len = ord(fread($this->connection, 1));
            $do += 1;
            if($value_len > 127)
            {
                $value_len = (($value_len & 0x7f) << 24) + (ord(fread($this->connection, 1)) << 16)
                    + (ord(fread($this->connection, 1)) << 8) + ord(fread($this->connection, 1));
                $do += 3;
            }
            
            //var_dump([$name_len, $value_len]);
            
            $name = $name_len ? fread($this->connection, $name_len) : '';
            $do += $name_len;
            $value = $value_len ? fread($this->connection, $value_len) : '';
            $do += $value_len;
            $arr[$name] = $value;
            //var_dump([$name, $value,$do]);
        }
        fread($this->connection, $contentLength + $paddingLength - $do);
        return $arr;
    }
}