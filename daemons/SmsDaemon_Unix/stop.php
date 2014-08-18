<?php
    $pid = file_get_contents('log/daemon_pid_file.pid');
    echo $pid;
    exec("kill $pid");
?>