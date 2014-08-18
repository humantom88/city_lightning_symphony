<?php

include 'daemon.php';
$baseDir = dirname(__FILE__);

//fclose(STDIN);
//fclose(STDOUT);
//fclose(STDERR);

//$STDIN = fopen('/dev/null','r');
$STDOUT = fopen($baseDir.'\\log\\application.log','rw');
$STDERR = fopen($baseDir.'\\log\\daemon.log','rw');

ini_set('error_log',$baseDir.'\log\error.log');
$config = parse_ini_file($baseDir . '\\config\\config.ini');
$daemon = new Daemon($config['gammupath']);
echo "Starting Daemon...";
$daemon->run();
