<?php 

$cmd = "/usr/bin/python ".$ep."main.py -f -p /var/";
echo exec($cmd);
//echo exec($cmd." >/dev/null 2>&1 & echo $!",$pidoutput);

echo $cmd;
// Return PID of this process to be monitored
$pid = $pidoutput[0];

?>