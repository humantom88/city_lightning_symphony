<?php

$child_pid = pcntl_fork();

if ($child_pid) {
    exit();
}

posix_setsid();

$baseDir = dirname(__FILE__);

$config = parse_ini_file($baseDir . '/config/config.ini');

ini_set('error_log',$baseDir.'/log/error.log');

fclose(STDIN);
fclose(STDOUT);
fclose(STDERR);
$STDIN = fopen('/dev/null','r');
$STDOUT = fopen($baseDir.'/log/application.log','ab');
$STDERR = fopen($baseDir.'/log/daemon.log','ab');

include 'daemon.php';
$baseDir = dirname(__FILE__);
ini_set('error_log',$baseDir.'/log/error.log');
$config = parse_ini_file($baseDir . '/config/config.ini');
$daemon = new Daemon($config['gammupath'], $config['dbhost'], $config['dbname'], $config['dbuser'], $config['dbpassword']);

if ($daemon->isDaemonActive($baseDir.'/log/daemon_pid_file.pid')) {
    echo 'Daemon is already active';
    exit;
} else {
    file_put_contents($baseDir.'/log/daemon_pid_file.pid', getmypid());
    $daemon->run();
}