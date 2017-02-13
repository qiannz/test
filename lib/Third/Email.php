<?php

/**
 * 邮件类
 *
 */

class Third_Email {
    /* Public Variables */
    public $smtp_port;
    public $time_out;
    public $host_name;
    public $log_file;
    public $relay_host;
    public $debug;
    public $auth;
    public $user;
    public $pass;
    public $mail_from;
    public $mail_name;
    /* Private Variables */
    private $sock;
    
    /* Constractor */
    public function __construct(){
        require_once ROOT_PATH . 'etc/email.config.php';
        $mailcfg = $GLOBALS['mailcfg'];
        
        $this->debug = True;
        $this->smtp_port = $mailcfg['port'];
        $this->relay_host = $mailcfg['server'];
        $this->mail_from = $mailcfg['from'];
        $this->mail_name = $mailcfg['name'];
        
        //is used in fsockopen()
        $this->time_out = 30;
        
        $this->auth = $mailcfg['auth']; //auth
        $this->user = $mailcfg['auth_username'];
        $this->pass = $mailcfg['auth_password'];
        
        //is used in HELO command
        $this->host_name = "localhost";
        $this->log_file = "";
        $this->sock = FALSE;
    }
    
    /* Main Function */
    public function sendmail($to, $subject = "", $body = "", $mailtype = 'HTML', $cc = "", $bcc = "", $additional_headers = ""){
        $mail_from = $this->get_address($this->strip_comment($this->mail_from));
        $body = ereg_replace("(^|(\r\n))(\.)", "\1.\3", $body);
        $header = "MIME-Version:1.0\r\n";
        if($mailtype == "HTML"){
            $header .= "Content-Type:text/html; charset=utf-8\r\n";
        }
        if($cc != ""){
            $header .= "Cc: " . $cc . "\r\n";
        }
        
        $header .= "From: " . $this->mail_name . "<" . $this->mail_from . ">\r\n";
        $subject = "=?utf-8?B?" . base64_encode($subject) . "?=";
        $header .= "Subject: " . $subject . "\r\n";
        $header .= $additional_headers;
        $header .= "Date: " . date("r") . "\r\n";
        $header .= "X-Mailer:Nuomi.Us (mail server/" . phpversion() . ")\r\n";
        list($msec, $sec) = explode(" ", microtime());
        $header .= "Message-ID: <" . date("YmdHis", $sec) . "." . ($msec * 1000000) . "." . $mail_from . ">\r\n";
        $TO = explode(",", $this->strip_comment($to));
        if($cc != ""){
            $TO = array_merge($TO, explode(",", $this->strip_comment($cc)));
        }
        if($bcc != ""){
            $TO = array_merge($TO, explode(",", $this->strip_comment($bcc)));
        }
        $sent = TRUE;
        foreach($TO as $rcpt_to){
            $headerto = "To: " . $rcpt_to . "\r\n";
            $headerall = $header . $headerto;
            $rcpt_to = $this->get_address($rcpt_to);
            if(!$this->smtp_sockopen($rcpt_to)){
                $this->log_write("Error: Cannot send email to " . $rcpt_to . "\n");
                $sent = FALSE;
                continue;
            }
            if($this->smtp_send($this->host_name, $mail_from, $rcpt_to, $headerall, $body)){
                $this->log_write("E-mail has been sent to <" . $rcpt_to . ">\n");
            }else{
                $this->log_write("Error: Cannot send email to <" . $rcpt_to . ">\n");
                $sent = FALSE;
            }
            fclose($this->sock);
            $this->log_write("Disconnected from remote host\n");
        }
        return $sent;
    }
    
    /* Private Functions */
    public function smtp_send($helo, $from, $to, $header, $body = ""){
        if(!$this->smtp_putcmd("HELO", $helo)){
            return $this->smtp_error("sending HELO command");
        }
        
        #auth
        if($this->auth){
            if(!$this->smtp_putcmd("AUTH LOGIN", base64_encode($this->user))){
                return $this->smtp_error("sending HELO command");
            }
            if(!$this->smtp_putcmd("", base64_encode($this->pass))){
                return $this->smtp_error("sending HELO command");
            }
        }
        
        #
        if(!$this->smtp_putcmd("MAIL", "FROM:<" . $from . ">")){
            return $this->smtp_error("sending MAIL FROM command");
        }
        if(!$this->smtp_putcmd("RCPT", "TO:<" . $to . ">")){
            return $this->smtp_error("sending RCPT TO command");
        }
        if(!$this->smtp_putcmd("DATA")){
            return $this->smtp_error("sending DATA command");
        }
        if(!$this->smtp_message($header, $body)){
            return $this->smtp_error("sending message");
        }
        if(!$this->smtp_eom()){
            return $this->smtp_error("sending <CR><LF>.<CR><LF> [EOM]");
        }
        if(!$this->smtp_putcmd("QUIT")){
            return $this->smtp_error("sending QUIT command");
        }
        return TRUE;
    }
    
    public function smtp_sockopen($address){
        if($this->relay_host == ""){
            return $this->smtp_sockopen_mx($address);
        }else{
            return $this->smtp_sockopen_relay();
        }
    }
    
    public function smtp_sockopen_relay(){
        $this->log_write("Trying to " . $this->relay_host . ":" . $this->smtp_port . "\n");
        $this->sock = @fsockopen($this->relay_host, $this->smtp_port, $errno, $errstr, $this->time_out);
        if(!($this->sock && $this->smtp_ok())){
            $this->log_write("Error: Cannot connenct to relay host " . $this->relay_host . "\n");
            $this->log_write("Error: " . $errstr . " (" . $errno . ")\n");
            return FALSE;
        }
        $this->log_write("Connected to relay host " . $this->relay_host . "\n");
        return TRUE;
        ;
    }
    
    public function smtp_sockopen_mx($address){
        $domain = ereg_replace("^.+@([^@]+)$", "\1", $address);
        if(!@getmxrr($domain, $MXHOSTS)){
            $this->log_write("Error: Cannot resolve MX \"" . $domain . "\"\n");
            return FALSE;
        }
        foreach($MXHOSTS as $host){
            $this->log_write("Trying to " . $host . ":" . $this->smtp_port . "\n");
            $this->sock = @fsockopen($host, $this->smtp_port, $errno, $errstr, $this->time_out);
            if(!($this->sock && $this->smtp_ok())){
                $this->log_write("Warning: Cannot connect to mx host " . $host . "\n");
                $this->log_write("Error: " . $errstr . " (" . $errno . ")\n");
                continue;
            }
            $this->log_write("Connected to mx host " . $host . "\n");
            return TRUE;
        }
        $this->log_write("Error: Cannot connect to any mx hosts (" . implode(", ", $MXHOSTS) . ")\n");
        return FALSE;
    }
    
    public function smtp_message($header, $body){
        fputs($this->sock, $header . "\r\n" . $body);
        $this->smtp_debug("> " . str_replace("\r\n", "\n" . "> ", $header . "\n> " . $body . "\n> "));
        return TRUE;
    }
    
    public function smtp_eom(){
        fputs($this->sock, "\r\n.\r\n");
        $this->smtp_debug(". [EOM]\n");
        return $this->smtp_ok();
    }
    
    public function smtp_ok(){
        $response = str_replace("\r\n", "", fgets($this->sock, 512));
        $this->smtp_debug($response . "\n");
        if(!ereg("^[23]", $response)){
            fputs($this->sock, "QUIT\r\n");
            fgets($this->sock, 512);
            $this->log_write("Error: Remote host returned \"" . $response . "\"\n");
            return FALSE;
        }
        return TRUE;
    }
    
    public function smtp_putcmd($cmd, $arg = ""){
        if($arg != ""){
            if($cmd == ""){
                $cmd = $arg;
            }else{
                $cmd = $cmd . " " . $arg;
            }
        }
        fputs($this->sock, $cmd . "\r\n");
        $this->smtp_debug("> " . $cmd . "\n");
        return $this->smtp_ok();
    }
    
    public function smtp_error($string){
        $this->log_write("Error: Error occurred while " . $string . ".\n");
        return FALSE;
    }
    
    public function log_write($message){
        $this->smtp_debug($message);
        if($this->log_file == ""){
            //echo $message;
            return TRUE;
        }
        $message = date("M d H:i:s ") . get_current_user() . "[" . getmypid() . "]: " . $message;
        if(!@file_exists($this->log_file) || !($fp = @fopen($this->log_file, "a"))){
            $this->smtp_debug("Warning: Cannot open log file \"" . $this->log_file . "\"\n");
            return FALSE;
        }
        flock($fp, LOCK_EX);
        fputs($fp, $message);
        fclose($fp);
        return TRUE;
    }
    
    public function strip_comment($address){
        $comment = "\([^()]*\)";
        while(ereg($comment, $address)){
            $address = ereg_replace($comment, "", $address);
        }
        return $address;
    }
    
    public function get_address($address){
        $address = ereg_replace("([ \t\r\n])+", "", $address);
        $address = ereg_replace("^.*<(.+)>.*$", "\1", $address);
        return $address;
    }
    
    public function smtp_debug($message){
        if($this->debug){
            //echo $message;
        }
    }

}

?>