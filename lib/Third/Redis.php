<?php

/**
 * Redisent, a Redis interface for the modest
 * @author Justin Poliey <jdp34@njit.edu>
 * @copyright 2009 Justin Poliey <jdp34@njit.edu>
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @package Redisent
 */

define('CRLF', sprintf('%s%s', chr(13), chr(10)));

class Third_Redis {
    /**
     * Socket connection to the Redis server
     * @var resource
     * @access private
     */
    private $__sock;
    
    public function __construct(){
    }
    
    /**
     * Creates a Redisent connection to the Redis server on host {@link $host} and port {@link $port}.
     * @param string $host The hostname of the Redis server
     * @param integer $port The port number of the Redis server
     */
    public function connect($host, $port = 6379){
        $errno = $errstr = '';
        $this->__sock = fsockopen($host, $port, $errno, $errstr);
        if(!$this->__sock){
            ob_clean();
            echo '<div id="feed-none" class="search-none"><p class="fb14">网络出错</p></div>';
            ob_flush();
            //Model_Message_Sms::send('13585802482', "hi,Redis({$host})连不上了!", 3);
            exit();
        
     //throw new Exception("{$errno} - {$errstr}");
        }
    }
    
    public function __destruct(){
        $this->quit();
    }
    
    public function close(){
        $this->quit();
    }
    
    public function quit(){
        if($this->__sock && is_resource($this->__sock)){
            fclose($this->__sock);
        }
        $this->__sock = NULL;
    }
    
    public function __call($name, $args){
        /* Build the Redis unified protocol command */
        array_unshift($args, strtoupper($name));
        
        foreach($args as &$value){
            $value = sprintf('$%d%s%s', strlen($value), CRLF, $value);
        }
        
        $command = sprintf('*%d%s%s%s', count($args), CRLF, implode($args, CRLF), CRLF);
        
        /* Open a Redis connection and execute the command */
        for($written = 0; $written < strlen($command); $written += $fwrite){
            $fwrite = fwrite($this->__sock, substr($command, $written));
            if($fwrite === FALSE){
                throw new Exception('Failed to write entire command to stream');
            }
        }
        
        /* Parse the response based on the reply identifier */
        $reply = trim(fgets($this->__sock, 512));
        switch(substr($reply, 0, 1)){
            /* Error reply */
            case '-':
                //                throw new RedisException(substr(trim($reply), 4));
                break;
            /* Inline reply */
            case '+':
                $response = substr(trim($reply), 1);
                break;
            /* Bulk reply */
            case '$':
                $response = null;
                if($reply == '$-1'){
                    break;
                }
                $read = 0;
                $size = substr($reply, 1);
                if($size > 0){
                    do{
                        $block_size = ($size - $read) > 1024 ? 1024 : ($size - $read);
                        $response .= fread($this->__sock, $block_size);
                        $read += $block_size;
                    }while($read < $size);
                }
                fread($this->__sock, 2); /* discard crlf */
                break;
            /* Multi-bulk reply */
            case '*':
                $count = substr($reply, 1);
                if($count == '-1'){
                    return null;
                }
                $response = array();
                for($i = 0; $i < $count; $i++){
                    $bulk_head = trim(fgets($this->__sock, 512));
                    $size = substr($bulk_head, 1);
                    if($size == '-1'){
                        $response[] = null;
                    }else{
                        $read = 0;
                        $block = "";
                        do{
                            $block_size = ($size - $read) > 1024 ? 1024 : ($size - $read);
                            $block .= fread($this->__sock, $block_size);
                            $read += $block_size;
                        }while($read < $size);
                        fread($this->__sock, 2); /* discard crlf */
                        $response[] = $block;
                    }
                }
                break;
            /* Integer reply */
            case ':':
                $response = intval(substr(trim($reply), 1));
                break;
            default:
                throw new RedisException("invalid server response: {$reply}");
                break;
        }
        /* Party on */
        return $response;
    }
}

if(!class_exists('RedisException', false)){
    class RedisException extends Exception {
        // do nothing
    }
}