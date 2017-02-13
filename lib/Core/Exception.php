<?php

/**
 * 异常处理
 *
 */

class Core_Exception extends Exception {
    /**
     * 记录异常处理日志
     *
     * @param exception $e
     * @param string $msg
     * @return boolean
     */
    public static function exceptionProcess($e, $msg = ''){
        $data = 'sql:' . $msg . "\n";
        $data .= $e->getMessage() . "\n";
        foreach($e->getTrace() as $key=>$trace){
            if(!isset($trace['file']) && !isset($trace['line'])){
                continue;
            }
            $data .= ($key + 1) . ' File:' . $trace['file'] . ' Line:' . $trace['line'] . "\n";
        }
        if(isDebug()){
            echo (nl2br($data));
        }else{
            $logFile = LOG_PATH . 'sql_error_' . date('Y-m-d') . '.log';
            $logMsg = '[' . date('Y-m-d H:i:s') . '] - ' . $data . "\n";
            file_put_contents($logFile, $logMsg, FILE_APPEND);
        }
    }
}