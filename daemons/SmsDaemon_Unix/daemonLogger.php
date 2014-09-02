<?php

class DaemonLogger {
    private $logFile = "/log/log.txt";
    
    public function __constuct($logFilePath)
    {
        $this->logFile = $logFilePath;
    }
    
    public function log($logInfo, $logMessage = "")
    {
        file_put_contents($this->logFile,date() . " : " . $logMessage. " " . $logInfo, FILE_APPEND);
    }
}

