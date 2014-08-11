<?php

class SMSDaemon {
    
    private $gammuDir;
    private $inboxChecked;
    private $dbname;
    private $sqlserver;
    private $user;
    private $password;
    private $config;
    
    public function getConfigFile($config_file) 
    {
        $config = "";
        if (file_exists($config_file)) {
            $config = parse_ini_file($config_file);
            $this->gammuDir = $config['gammuDir'];
            $this->inboxChecked = $config['inboxChecked'];
            $this->dbname = $config['dbname'];
            $this->sqlserver = $config['sqlserver'];
            $this->user = $config['user'];
            $this->password = $config['password'];
            
        }
        return $config;
    }
    
    public function __construct($config) 
    {
        $this->config = $this->getConfigFile($config);
        $data = $this->config;
        if (is_array($data)) {
            $this->gammuDir = $data['gammuDir'];
            $this->inboxChecked = $data['inboxChecked'] ? $data['inboxChecked'] : "";
            $this->dbname = $data['dbname'];
            $this->sqlserver = $data['sqlserver'];
            $this->user = $data['user'];
            $this->password = $data['password'];
        }
    }
    
    public function readInbox() 
    {
        $messages = [];
        if ($handle = opendir($this->gammuDir . 'inbox')) {
            while (false !== ($file = readdir($handle))) { 
                if ($file != "." && $file != "..") {
                    array_push($messages,$file);
                }
            }
            closedir($handle); 
        }
        return $messages;
    }
    
    public function parseSMSFilename($smsFilename) {
        if (substr($smsFilename,0,2) == "IN") {
            $year = substr($smsFilename, 2,4);
            $month = substr($smsFilename, 6,2);
            $day = substr($smsFilename, 8,2);
            $hour = substr($smsFilename, 11,2);
            $minute = substr($smsFilename, 13,2);
            $second = substr($smsFilename, 15,2);
            $dateStr = "$year-$month-$day $hour:$minute";
            $date =  new DateTime($dateStr);
            $phone = substr($smsFilename, 21, 12);    
        }
        $message = file_get_contents("c:/gammu/inbox/$smsFilename");

        return [
            'message' => $message,
            'date'    => $date,
            'from'   => $phone
        ];
    }
}

$smsDaemon = new SMSDaemon('smsdaemon.ini');
$message_files = $smsDaemon->readInbox();

$smsDaemon->parseSMSFilename($message_files[3]);