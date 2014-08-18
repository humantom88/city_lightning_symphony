<?php
// Без этой директивы PHP не будет перехватывать сигналы
declare(ticks=1); 

class Daemon {
    // Максимальное количество дочерних процессов
    public $maxProcesses = 1;
    // Когда установится в TRUE, демон завершит работу
    protected $stop_server = FALSE;
    // Здесь будем хранить запущенные дочерние процессы
    protected $currentJobs = array();
    
    public $db;

    public function __construct() {
        echo "Сonstructed daemon controller".PHP_EOL;
        // Ждем сигналы SIGTERM и SIGCHLD
        pcntl_signal(SIGTERM, array($this, "childSignalHandler"));
        pcntl_signal(SIGCHLD, array($this, "childSignalHandler"));
    }

    public function setDbConnection() {
	$this->db = new PDO('mysql:host=localhost;dbname=mmanager','root','tot@lcontro1');
	$this->db->exec('SET NAMES utf8');	
    }

    public function closeDbConnection() {
	$this->db = null;
    }

    public function run() {
        echo "Running daemon controller".PHP_EOL;

        // Пока $stop_server не установится в TRUE, гоняем бесконечный цикл
        while (!$this->stop_server) {
            // Если уже запущено максимальное количество дочерних процессов, ждем их завершения
            while(count($this->currentJobs) >= $this->maxProcesses) {
                 sleep(1);
            }
            $this->launchJob();
	    sleep(10);
        } 
	echo  date('Y-m-d-h-m-s') . ': Service closed.';
    }

    private function loopCycle() {
	//Блок 1 Чтение входящих сообщений из папки Inbox, занесение в БД, перенос в архив.
	$newMessages = $this->checkInbox('/var/spool/gammu/inbox');
	if ($newMessages) {
	    $this->insertMessagesToDb ($newMessages);
	}
	//Блок 2 Рассылка по расписанию
	$this->checkSchedule();
	
    }

    private function insertMessagesToDb ($newMessages){
	$this->setDbConnection();
	$sql = "INSERT INTO `mmanager`.`smsmessages` (`sms_text`, `sms_from`, `sms_sentdate`) VALUES (:smsText, :smsFrom, :smsSentDate)";
	$result = $this->db->prepare($sql);
	foreach ($newMessages as $newMessage) {
	    echo "Message\r\n";
    	    print_r($newMessage);
	    $sentdate = date($newMessage['sms_date']. ' '. $newMessage['sms_time']);
	    print_r($sentdate);
	    $result->bindParam(':smsText', $newMessage['sms_text']);
	    $result->bindParam(':smsFrom', $newMessage['sms_from']);
	    $result->bindParam(':smsSentDate', $sentdate);
	    $result->execute();
	}
	$this->closeDbConnection();
    }

    private function getModemIdByPhone($phone) {
	if (!$this->db) {
	    $this->setDbConnection();
	}
	$phone = "";
	$sql = "SELECT `modem_id` FROM `modems` WHERE modem_phone = :phone";
	if ($result = $this->db->query($sql)) {
	    $result->bindParam(':phone', $phone);
	    $result->execute();
	    $phone = $result->fetchAll();
	};

	if ($phone != "" && exist($phone['modem_id'])) {
	    return $phone['modem_id'];
	}
	return false;
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
	    sleep(10);
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