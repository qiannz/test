<?php

/**
 * AMQPQueue
 *
 */

if(!class_exists('AMQPException', false)){
    require_once ROOT_PATH . 'lib/Third/phpAmqplib/amqp.inc';
}

class Core_Queue_AMQP {
    public $connConf;
    public $vhost = '/';
    public $exchange = 'xiangle.direct';
    public $binding_type = 'direct';
    private static $AMQPObject = NULL;
    
    protected static $_instance;
    public static function getInstance($option = array()){
        if(self::$_instance === NULL){
            self::$_instance = new self($option);
        }
        return self::$_instance;
    }
    
    /**
     * 链接的单例
     */
    public function getQMQPInstace(){
        if(self::$AMQPObject === NULL){
            self::$AMQPObject = new AMQPConnection($this->connConf['host'], $this->connConf['port'], $this->connConf['user'], $this->connConf['password'], $this->vhost);
        }
        return self::$AMQPObject;
    
    }
    
    public function __construct($option = array()){
        $connConf = $GLOBALS['GLOBAL_CONF']['Queue']['Conn'];
        if(isset($option['host'])) $connConf['host'] = $option['host'];
        if(isset($option['port'])) $connConf['host'] = $option['port'];
        if(isset($option['user'])) $connConf['host'] = $option['user'];
        if(isset($option['password'])) $connConf['password'] = $option['password'];
        $this->connConf = $connConf;
        
        if(isset($option['vhost'])) $this->vhost = $option['vhost'];
        if(isset($option['exchange'])) $this->exchange = $option['exchange'];
        if(isset($option['binding_type'])) $this->binding_type = $option['binding_type'];
    }
    
    public function push($queue, $value){
        $conn = new AMQPConnection($this->connConf['host'], $this->connConf['port'], $this->connConf['user'], $this->connConf['password'], $this->vhost);
        $ch = $conn->channel();
        $ch->queue_declare($queue, false, true, false, false);
        $ch->exchange_declare($this->exchange, $this->binding_type, false, true, false);
        $ch->queue_bind($queue, $this->exchange);
        $msg = new AMQPMessage($value, array('content_type'=>'text/plain'));
        $ch->basic_publish($msg, $this->exchange);
        $ch->close();
        $conn->close();
        return $ch;
    }
    
    /**
     * pop
     * @param string $queue    queue name
     * @param object $callObj  回调的object
     * @param string $callFunc 回调的function name
     * @param Boolean $isAck   是否开启ack
     */
    public function pop($queue, $callObj = '', $callFunc = '', $isAck = false){
        $conn = $this->getQMQPInstace();
        $channel = null;
        try{
            $channel = $conn->channel();
        }catch(Exception $e){
            echo 'Caught exception: ', $e->getMessage();
            echo "\nTrace: \n" . $e->getTraceAsString();
        }
        if($channel == null){
            echo 'Failed to connect the server !!';
        }else{
            $channel->queue_declare($queue, false, true, false, false);
            echo "get message \n";
            while(true){
                usleep(50000);
                $result = $channel->basic_get($queue);
                if(null != $result){
                    echo "result==" . $result->body . " \n";
                    if(!empty($callFunc)){
                        $res = !empty($callObj) ? $callObj->$callFunc($result->body) : $callFunc($result->body);
                        if($isAck){
                            if($res){
                                $channel->basic_ack($result->delivery_info["delivery_tag"], false);
                            }else{
                                $channel->basic_reject($result->delivery_info["delivery_tag"], true);
                            }
                        }
                    }
                }
            }
            
            $channel->close();
        }
        $conn->close();
    }
}

//Core_Queue_AMQP::getInstance()->push('msgs', 'hello world');