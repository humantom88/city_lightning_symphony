<?php
// Без этой директивы PHP не будет перехватывать сигналы
declare(ticks=1); 
require_once 'daemonLogger.php';

class Daemon {
    // Максимальное количество дочерних процессов
    public $maxProcesses = 1;
    // Когда установится в TRUE, демон завершит работу
    protected $stop_server = FALSE;
    // Здесь будем хранить запущенные дочерние процессы
    protected $currentJobs = array();
     
    protected $logger;
    
    private $commandSwitchOn = "5492 out3 pulse2";
    
    private $commandSwitchOff = "5492 out3 pulse2";
    
    private $gammuPath;
    
    private $dbhost;
    
    private $dbuser;
    
    private $dbpassword;
    
    private $dbname;
    
    public $db;

    public function __construct($gammuPath = "", $dbhost = "localhost", $dbname = "mmanager", $dbuser = "root", $dbpassword = "", $logFilePath) {
        $this->gammuPath = $gammuPath;
        $this->dbhost = $dbhost;
        $this->dbname = $dbname;
        $this->dbuser = $dbuser;
        $this->dbpassword = $dbpassword;
        $this->logger = new DaemonLogger($logFilePath);
        echo "Сonstructed daemon controller".PHP_EOL;
        // Ждем сигналы SIGTERM и SIGCHLD
        pcntl_signal(SIGTERM, array($this, "childSignalHandler"));
        pcntl_signal(SIGCHLD, array($this, "childSignalHandler"));
    }

    public function setDbConnection() {
	$this->db = new PDO("mysql:host=$this->dbhost;dbname=$this->dbname",$this->dbuser,$this->dbpassword);
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
	$newMessages = $this->checkInbox($this->gammuPath . 'inbox');
	if ($newMessages) {
            foreach ($newMessages as $k => $newMessage) {
                if (empty($newMessage['sms_from'])) {
                    if (!$this->db) {
                        $this->setDbConnection();
                        $sql = "SELECT `modem_id` FROM `$this->dbname`.`modems` WHERE `modem_phone`=:modem_from";
                        $result = $this->db->prepare($sql);
                        $result->bindParam(':modem_from', $newMessages['sms_from']);
                        $result->execute();
                        $mid = $result->fetch();

                        if ($mid) {
                            $newMessages[$k] = $mid['modem_id'];
                        }
                    }
                }
            }
	    $this->insertMessagesToDb($newMessages);
            $this->updateModemStatus($newMessages);
	}
	//Блок 2 Рассылка по расписанию
	$this->checkSchedule();
    }
    
    private function updateModemStatus ($newMessages = "")
    {
        //ATM2-485,TEST:IN2,11:13:57
        if (!empty($newMessages)) {
            foreach ($newMessages as $k => $newMessage) {
                if($newMessage['sms_text']) {
                    $res = $this->parseModemAns($newMessage['sms_text']);
                    if (!empty($res)) {
                        if (empty($this->db)) {
                            $this->setDbConnection();
                        }
                        $sql = "UPDATE `$this->dbname`.`modems` SET `modem_status`=:modem_status, `last_update`=:last_update WHERE `modem_phone`=:sms_from";
                        $result = $this->db->prepare($sql);
                        
                        echo "Device Answer: ";
                        print_r($res['device_answer']);
                        echo "Last Update: ";
                        print_r(date('Y-m-d H:i:s'));
                        echo "New Message: ";
                        print_r($newMessage);
                        $result->bindParam(':modem_status', $res['device_answer']);
                        $result->bindParam(':last_update', date('Y-m-d H:i:s'));
                        $result->bindParam(':sms_from', $newMessage['sms_from']);
                        $result->execute();
                    }
                }
            }
        }
    }
    
    private function getModemIdByPhone($modemPhone) 
    {
        if (!$this->db) {
            $this->setDbConnection();
        }
        $sql = "SELECT * FROM `$this->dbname`.`modems` WHERE `modem_phone`=:modem_phone";
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
        
        $sql1 = "INSERT INTO `$this->dbname`.`smsmessages` (`sms_text`, `sms_from`, `sms_sentdate`) VALUES (:smsText, :smsFrom, :smsSentDate)";
        $sql2 = "INSERT INTO `$this->dbname`.`smsmessages` (`modem_id`, `sms_text`, `sms_from`, `sms_sentdate`) VALUES (:modem_id, :smsText, :smsFrom, :smsSentDate)";
        foreach ($newMessages as $newMessage) {
            
            if (!empty($newMessage['sms_from'])) {
                $modem_id = $this->getModemIdByPhone($newMessage['sms_from']);
                $this->setDbConnection();
            }

            if (empty($modem_id)) {
                $result = $this->db->prepare($sql1);
            } else {
                $result = $this->db->prepare($sql2);
                $newMessage['modem_id'] = $modem_id;
            }
            
	    $sentdate = date($newMessage['sms_date']. ' '. $newMessage['sms_time']);
        if (!empty($newMessage['modem_id'])) {
            $result->bindParam(':modem_id', $newMessage['modem_id']);
        }
	    $result->bindParam(':smsText', $newMessage['sms_text']);
	    $result->bindParam(':smsFrom', $newMessage['sms_from']);
	    $result->bindParam(':smsSentDate', $sentdate);
          
	    $result->execute();
            
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
		    $now = getdate();
                    $timeblock_date = getdate(strtotime($v['timeblock_date']));
                    $timeblock_starttime = getdate(strtotime($v['timeblock_starttime']));
                    $timeblock_endtime = getdate(strtotime($v['timeblock_endtime']));

                    if ($now['year'] == $timeblock_date['year'] &&
                        $now['mon'] == $timeblock_date['mon'] &&
                        $now['mday'] == $timeblock_date['mday']) {
                        if ($now['hours'] == $timeblock_starttime['hours'] && $now['minutes'] == $timeblock_starttime['minutes']) {
                            $this->sendSMS($v['modem_phone'], $this->commandSwitchOn);
                        }
                        if ($now['hours'] == $timeblock_endtime['hours'] && $now['minutes'] == $timeblock_endtime['minutes']) {
                            $this->sendSMS($v['modem_phone'], $this->commandSwitchOff);
                        }
                    }

                    print_r($timeblock_date);
                    print_r($timeblock_starttime);
                    print_r($timeblock_endtime);
		    print_r($now);

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
        // Создаем дочерний процесс
        // весь код после pcntl_fork() будет выполняться
        // двумя процессами: родительским и дочерним
	
        $pid = pcntl_fork();
        if ($pid == -1) {
            // Не удалось создать дочерний процесс
            error_log('Could not launch new job, exiting');
            return FALSE;
        } 
        elseif ($pid) {
            // Этот код выполнится родительским процессом
            $this->currentJobs[$pid] = TRUE;
	    //call_user_method('getMessages',$this);
	    call_user_method('loopCycle', $this);
            sleep(59);
        } 
        else { 
            // А этот код выполнится дочерним процессом
	    //call_user_method('getMessages',$this);
	    //echo "PID ".getmypid().PHP_EOL;
            exit(); 
        } 
        return TRUE; 
    }

    public function childSignalHandler($signo, $pid = null, $status = null) {
        switch($signo) {
            case SIGTERM:
                // При получении сигнала завершения работы устанавливаем флаг
                $this->stop_server = true;
                break;
            case SIGCHLD:
                // При получении сигнала от дочернего процесса
                if (!$pid) {
                    $pid = pcntl_waitpid(-1, $status, WNOHANG); 
                } 
                // Пока есть завершенные дочерние процессы
                while ($pid > 0) {
                    if ($pid && isset($this->currentJobs[$pid])) {
                        // Удаляем дочерние процессы из списка
                        unset($this->currentJobs[$pid]);
                    } 
                    $pid = pcntl_waitpid(-1, $status, WNOHANG);
                } 
                break;
            default:
                // все остальные сигналы
        }
    } 

    public function isDaemonActive($pid_file) {
        if( is_file($pid_file) ) {
	    $pid = file_get_contents($pid_file);
            //проверяем на наличие процесса
            if(posix_kill($pid,0)) {
                //демон уже запущен
                return true;
            } else {
                //pid-файл есть, но процесса нет 
                if(!unlink($pid_file)) {
                    //не могу уничтожить pid-файл. ошибка
                    exit(-1);
                }
            }
	}
        return false;
    }
    
    public function checkInbox($inboxPath) {
	$files = array();
        if ($handle = opendir($inboxPath)) {
	    while(false !== ($file = readdir($handle))) {
		if ($file != '.' && $file != '..') {
		    array_push($files, $this->parseInboxFile($file, $inboxPath."/".$file));
                    if (!file_exists($inboxPath . "/../archive/")) {
                        mkdir($inboxPath . "/../archive/");
                    }
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
    public function sendSMS($phone, $smsText) {
        file_put_contents($this->getOutboxPath().'OUT'. $phone . '.txt', $smsText);
    }
    
    public function getOutboxPath() {
        print_r($this->gammuPath . '/outbox/');
        return $this->gammuPath . "/outbox/";
    }
}