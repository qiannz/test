<?php

class TNonblockingServer extends TServer {
    
    public function serve(){
        $this->transport_->setCallback(array($this, 'handleRequest'));
        $this->transport_->listen();
    }
    
    public function stop(){
        $this->transport_->close();
    }
    
    public function handleRequest(TTransport $transport){
        $protocol = new TBinaryProtocol($transport, true, true);
        $this->processor_->process($protocol, $protocol);
    }

}

