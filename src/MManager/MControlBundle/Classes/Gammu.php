<?php

namespace MManager\MControlBundle\Classes;

class Gammu {
    protected $inboxPath;
    protected $outboxPath;
    protected $sent_path;
    protected $errors_path;
    
    public function __construct($config = "") {
        $configDir = dirname(__DIR__) . "../../../../config/gammu.ini";
        $configArr = parse_ini_file($configDir);
        if ($config != "") {
            $this->inboxPath = $config['inbox'];
            $this->outboxPath = $config['outbox'];
            $this->sentPath = $config['sent'];
            $this->errorsPath = $config['errors'];
        } else if ($configArr != "" && $configArr != null) {
            $this->inboxPath = $configArr['inbox'];
            $this->outboxPath = $configArr['outbox'];
            $this->sentPath = $configArr['sent'];
            $this->errorsPath = $configArr['errors'];
        }
    }
    
    public function getInboxPath() {
        return $this->inboxPath;
    }
    
    public function setInboxPath($inboxPath) {
        $this->inboxPath = $inboxPath;
    }
    
    public function getOutboxPath() {
        return $this->outboxPath;
    }
    
    public function setOutboxPath($outboxPath) {
        $this->outboxPath = $outboxPath;
    }
    
    public function getSentPath() {
        return $this->sentPath;
    }
    
    public function setSentPath($sentPath) {
        $this->sentPath = $sentPath;
    }
    
    public function sendSMS($phone, $smsText) {
        file_put_contents($this->getOutboxPath().'OUT'. $phone . '.txt', $smsText);
    }
    
    public function readIncomeSMSList() {
        if ($this->inboxPath) {
            return readdir($this->inboxPath);
        } else {
            return false;
        }
    }

    public function readSentSMSList() {
        if ($this->sentPath) {
            return readdir($this->sentPath);
        } else {
            return false;
        }
    }
}
