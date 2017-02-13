<?php
require_once(ROOT_PATH."lib/Third/phpmailer/class.phpmailer.php");   

class Custom_Send {
    /**
     * @param $sendto_email 发送到
     * @param $subject 邮件主题
     * @param $user_name 用户名 
     */    
    public static function sendMail ($sendto_email, $subject, $user_name, $randPwd) 
    {

        $mail = new PHPMailer();    
        $mail->IsSMTP();                  // send via SMTP    
        $mail->Host = "smtp.services.mplife.com";   // SMTP servers    
        $mail->SMTPAuth = true;           // turn on SMTP authentication    
        $mail->Username = "mpning@services.mplife.com";     // SMTP username  注意：普通邮件认证不需要加 @域名    
        $mail->Password = "nam!@#QAZ123"; // SMTP password    
        $mail->From = "mpning@services.mplife.com";      // 发件人邮箱    
        $mail->FromName =  "名品导购";  // 发件人    
      
        $mail->CharSet = "UTF-8";   // 这里指定字符集！    
        $mail->Encoding = "base64";    
        $mail->AddAddress($sendto_email,$user_name);  // 收件人邮箱和姓名    
        //$mail->AddReplyTo("yourmail@yourdomain.com","yourdomain.com");    
        //$mail->WordWrap = 50; // set word wrap 换行字数    
        //$mail->AddAttachment("/var/tmp/file.tar.gz"); // attachment 附件    
        //$mail->AddAttachment("/tmp/image.jpg", "new.jpg");    
        $mail->IsHTML(true);  // send as HTML    
        // 邮件主题    
        $mail->Subject = $subject;    
        // 邮件内容    
        $mail->Body = "亲爱的".$user_name."
                                                              以下是你在逛南京的账号信息
                                                              登录帐号：".$user_name."
                                                              密码：".$randPwd."
                                                              为了密码安全起见建议在登陆逛南京后及时修改你的密码以免账号被他人盗用 -——来自逛南京";   
                                                                       
        $mail->AltBody ="text/html";    
        
        if(!$mail->Send())    
        {    
            //echo "邮件发送有误 <p>";    
            //echo "邮件错误信息: " . $mail->ErrorInfo;
            return '0';    
        }    
        else {    
            // echo "$user_name 邮件发送成功!<br />";   
            return '1'; 
        }     
    }
   /**
    * 发送短信
    * @param unknown_type $phone
    * @param unknown_type $message
    * @param unknown_type $time
    * return SendSmsResult 
    */
    public static function sendMobileMessage($phone, $message, $time = null){
    	// 短信通道
    	$client = new SoapClient('http://webservice.mplife.com/smswebservice/SmsHelper.asmx?wsdl');
    	$auth = array('UserName'=>$GLOBALS['GLOBAL_CONF']['SMS_User'], 'Password'=>$GLOBALS['GLOBAL_CONF']['SMS_Pwd']);
    	$authvalues = new SoapVar($auth, SOAP_ENC_OBJECT,'Auths','http://api.mplife.com/');
    	$header =  new SoapHeader('http://api.mplife.com/', "Auths", $authvalues, false);
    	$client->__setSoapHeaders(array($header));
    	$param = array(
    			'phones'      => $phone,
    			'content'     => $message,
    			'sendtime'    => $time,
    	);
    	$call_val = $client->SendSms($param);
    	$arr = objectToArray($call_val);
    	return $arr;
    }
    
    /**
     * 发送短信
     * @param unknown_type $phone
     * @param unknown_type $message
     * @param unknown_type $time
     * return SendSmsResult
     */
    public static function sendMessage($phone, $message, $time = null){
    	// 短信通道
    	$client = new SoapClient('http://webservice.mplife.com/smswebservice/SmsHelper.asmx?wsdl');
    	$auth = array('UserName' => 'crowdfunding', 'Password' => 'F6049729E87B4C42C42C35E7C4938371D099E3F4D598AD04');
    	$authvalues = new SoapVar($auth, SOAP_ENC_OBJECT,'Auths','http://api.mplife.com/');
    	$header =  new SoapHeader('http://api.mplife.com/', "Auths", $authvalues, false);
    	$client->__setSoapHeaders(array($header));
    	$param = array(
    			'phones'      => $phone,
    			'content'     => $message,
    			'sendtime'    => $time,
    	);
    	$call_val = $client->SendSms($param);
    	$arr = objectToArray($call_val);
    	return $arr;
    }
	
    /**
     * 发送短信（最新）
     * @param unknown_type $phone 手机号码
     * @param unknown_type $message 消息内容
     * @param unknown_type $time 时间戳
     * @param unknown_type $saleNo 订单号
     * @return Ambigous <void, array>
     */
    public static function sendMessageNew($phone, $message, $time=null, $saleNo='' ){
    	// 短信通道
    	$client = new SoapClient('http://webservice.mplife.com/smswebservice/SmsHelper.asmx?wsdl');
    	$auth = array('UserName' => 'crowdfunding', 'Password' => 'F6049729E87B4C42C42C35E7C4938371D099E3F4D598AD04');
    	$authvalues = new SoapVar($auth, SOAP_ENC_OBJECT,'Auths','http://api.mplife.com/');
    	$header =  new SoapHeader('http://api.mplife.com/', "Auths", $authvalues, false);
    	$client->__setSoapHeaders(array($header));
    	$jsonData = array('Ip'=>CLIENT_IP,
    					  'Made'=>'NEW',
    					  'SendTime'=>$time,
    					  'SaleNo'=>$saleNo
    					);
    	$param = array(
    			'phones'      => $phone,
    			'content'     => $message,
    			'jsonData'    => json_encode($jsonData)
    	);
    	$call_val = $client->SendSms($param);
    	$arr = objectToArray($call_val);
    	return $arr;
    }
    
    /**
     * 找回密码（邮件找回）
     * @param unknown_type $to_email
     * @param unknown_type $user_name
     * @param unknown_type $content
     * @return boolean
     */
    public static function sendFindPasswordMail ($to_email, $user_name, $content)
    {    
    	$mail = new PHPMailer();
    	$mail->IsSMTP();
    	$mail->Host = $GLOBALS['GLOBAL_CONF']['SMTP_Host']; // SMTP servers
    	$mail->SMTPAuth = true;           // turn on SMTP authentication
    	$mail->Username = $GLOBALS['GLOBAL_CONF']['SMTP_User'];     // SMTP username  注意：普通邮件认证不需要加 @域名
    	$mail->Password = $GLOBALS['GLOBAL_CONF']['SMTP_Pwd']; // SMTP password
    	$mail->From = $GLOBALS['GLOBAL_CONF']['SMTP_Send'];      // 发件人邮箱
    	$mail->FromName = "名品导购";  // 发件人    
    	$mail->CharSet = "UTF-8";   // 这里指定字符集！
    	$mail->Encoding = "base64";
    	$mail->AddAddress($to_email,$user_name);  // 收件人邮箱和姓名
    	//$mail->AddReplyTo("yourmail@yourdomain.com","yourdomain.com");//抄送
    	$mail->WordWrap = 50; // set word wrap 换行字数
    	//$mail->AddAttachment("/var/tmp/file.tar.gz"); // attachment 附件
    	$mail->IsHTML(true);  // send as HTML
    	// 邮件主题
    	$mail->Subject = '找回密码';
    	// 邮件内容
    	$mail->Body = $content;                                   
        $mail->AltBody ="text/html";  //邮件正文不支持HTML的备用显示  
        if(!$mail->Send()){
			return false;
        }else {
            return true;
    	}
    }
}