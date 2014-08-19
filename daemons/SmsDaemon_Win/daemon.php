<?php
//ATM2-485,TEST:IN2,11:13:57
// Без этой директивы PHP не будет перехватывать сигналы
declare(ticks=1); 

class Daemon {
    // Максимальное количество дочерних процессов
    public $maxProcesses = 1;
    // Когда установится в TRUE, демон завершит работу
    protected $stop_server = FALSE;
    // Здесь будем хранить запущенные дочерние процессы
    protected $currentJobs = array();
    
    private $gammuPath;
    
    public $db;

    public function __construct($gammuPath = "") {
        $this->gammuPath = $gammuPath;
        echo "Constructed daemon controller".PHP_EOL;
    }

    public function setDbConnection() {
	$this->db = new PDO('mysql:host=localhost;dbname=mmanager','root','');
	$this->db->exec('SET NAMES utf8');	
    }

    public function closeDbConnection() {
	$this->db = null;
    }

    public function run() {
        echo "Running daemon controller".PHP_EOL;
        while (!$this->stop_server) {
            $this->launchJob();
        } 
	echo  date('Y-m-d-h-m-s') . ': Service closed.';
    }

    private function loopCycle() {
	//Блок 1 Чтение входящих сообщений из папки Inbox, занесение в БД, перенос в архив.
	$newMessages = $this->checkInbox($this->gammuPath . '/inbox');
	if ($newMessages) {
	    $this->insertMessagesToDb($newMessages);
	}
	//Блок 2 Рассылка по расписанию
	$this->checkSchedule();

    }
    
    private function getModemIdByPhone($modemPhone) 
    {
        if (!$this->db) {
            $this->setDbConnection();
        }
        $sql = "SELECT * FROM `mmanager`.`modems` WHERE `modem_phone`=:modem_phone";
        $result = $this->db->prepare($sql);
        $result->bindParam(':modem_phone', $modemPhone);
        $result->execute();
        $modem_id = $result->fetch();
        
        $this->closeDbConnection();
        return $modem_id ? $modem_id['modem_id'] : false;
    }

    private function insertMessagesToDb ($newMessages){
	$this->setDbConnection();
        $modem_id = "";
        
        $sql1 = "INSERT INTO `mmanager`.`smsmessages` (`sms_text`, `sms_from`, `sms_sentdate`) VALUES (:smsText, :smsFrom, :smsSentDate)";
        $sql2 = "INSERT INTO `mmanager`.`smsmessages` (`modem_id`, `sms_text`, `sms_from`, `sms_sentdate`) VALUES (:modem_id, :smsText, :smsFrom, :smsSentDate)";
        foreach ($newMessages as $newMessage) {
            
            if (!empty($newMessage['sms_from'])) {
                $modem_id = $this->getModemIdByPhone($newMessage['sms_from']);
                $this->setDbConnection();
            }

            if (empty($modem_id)) {
                $result = $this->db->prepare($sql1);
                echo ('Request is: $sql1');
            } else {
                $result = $this->db->prepare($sql2);
                $newMessage['modem_id'] = $modem_id;
                echo ('Request is: $sql2');
            }
            
	    $sentdate = date($newMessage['sms_date']. ' '. $newMessage['sms_time']);
        if (!empty($newMessage['modem_id'])) {
            $result->bindParam(':modem_id', $newMessage['modem_id']);
        }
	    $result->bindParam(':smsText', $newMessage['sms_text']);
	    $result->bindParam(':smsFrom', $newMessage['sms_from']);
	    $result->bindParam(':smsSentDate', $sentdate);
           
            print_r ($newMessage);
	    $result->execute();
            
            //ATM2-485,TEST:IN2,11:13:57

            //$res = $this->parseModemAns($newMessage['sms_text']);
            //if (!empty($res)) {
                //file_put_contents('c:\\log\\log.txt', print_r($res),FILE_APPEND);	
            //}
            $this->closeDbConnection();
	}
    }

    private function parseModemAns($answerString) {
	$arr = explode(",",$answerString);
        $result = "";
	if (is_array($arr) && $arr[0] != null) {
	    $result['device_type'] = $arr[0];
	}
	if (is_array($arr) && $arr[1] != null) {
	    $t = explode(':', $arr[1]);
	    if (is_array($t) && $t[1]) {
		$result['device_answer'] = $t[1];
	    }			
	}
	if (is_array($arr) && $arr[2] != null) {
	    $result['response_time'] = $arr[2];

        }
	return $result ? $result : false;
    }

    private function checkSchedule() {
	$this->setDbConnection();
	$sql = "SELECT `timeblock_date`, `timeblock_starttime`, `timeblock_endtime`, `modem_phone`, `schedule_name` 
		FROM timeblocks t 
		LEFT JOIN schedules s ON t.schedule_id = s.schedule_id
		LEFT JOIN modems m ON s.schedule_id = m.schedule_id
		WHERE timeblock_date = (NOW()-CURTIME());";
        if ($result = $this->db->query($sql))
        {
            $result->execute();                
            $data = $result->fetchAll();
	    print_r($data);
	    foreach ($data as $v) {
                if ($v['modem_phone']) {
		    $now = getdate(date('Y-m-d-h-m-s'));
		    $timeblock_date = date($v['timeblock_date'],'Y-m-d');
		    $timeblock_starttime = date($v['timeblock_starttime'],'h:m');
		    $timeblock_endtime = date($v['timeblock_endtime'], 'h:m');
		    
		    print_r($now);
		    print_r($timeblock_date);
		    print_r($timeblock_starttime);
		    print_r($timeblock_endtime);

		    if (date('Y-m-d')==$v['timeblock_date'] && date('h-m')==$v['timeblock_starttime']) {
			echo 'Sending SwitchOn SMS';
		    }
		    if (date('Y-m-d')==$v['timeblock_date'] && date('h-m')==$v['timeblock_endtime']) {
			echo 'Sending SwitchOff SMS';
		    }
		}
	    }
        }
	$this->closeDbConnection();
    }
    
    protected function launchJob() { 
        call_user_method('loopCycle', $this);
        sleep(10);
        return TRUE; 
    }

    public function checkInbox($inboxPath) {
	$files = array();
        if ($handle = opendir($inboxPath)) {
	    while(false !== ($file = readdir($handle))) {
		if ($file != '.' && $file != '..') {
		    array_push($files, $this->parseInboxFile($file, $inboxPath."/".$file));
		    copy($inboxPath."/$file",$inboxPath . "/../archive/$file");
	    	    unlink($inboxPath."/$file");
		}
	    }
        }
        closedir($handle);
	if (!empty($files)) {
    	    return $files;
	} else {
	    return false;
	}
    }

    public function parseInboxFile($filename, $filepath) {
        $matches = explode('_',$filename);
	if (substr($matches[0],0,2)=="IN") {
	    $matches['sms_date'] = substr($matches[0],2,strlen($matches[0]));
	    $matches['sms_date'] = substr($matches['sms_date'],0,4) . '-' . substr($matches['sms_date'],4,2) . '-' . substr($matches['sms_date'],6,2);
	    unset($matches[0]);
	}
	if ($matches[1]) {
	    $matches['sms_time'] = $matches[1];
	    $matches['sms_time'] = substr($matches['sms_date'],0,2) . ':' . substr($matches['sms_date'],2,2);
	    unset($matches[1]);
	}
	if ($matches[3]) {
	    $matches['sms_from'] = $matches[3];
	    unset($matches[3]);
	}
	unset($matches[2]);
	unset($matches[4]);
    
	$matches['sms_text'] = file_get_contents($filepath);
    
	return $matches;	
    }
}