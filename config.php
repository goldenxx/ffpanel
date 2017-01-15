<?php
function putintofile($word, $file) {
	$fopen = fopen($file, "a+");
	fwrite($fopen, $word);
	fclose($fopen);
}

$baiduceip = array('180.97.106.36','180.97.106.37','180.97.106.161','180.97.106.162','115.239.212.7','115.239.212.8','115.239.212.9','115.239.212.10','115.239.212.11','115.239.212.6','115.239.212.4','115.239.212.5','115.239.212.65','115.239.212.66','115.239.212.67','115.239.212.68','115.239.212.69','115.239.212.70','115.239.212.71','115.239.212.72','115.239.212.134','115.239.212.135','115.239.212.136','115.239.212.137','115.239.212.138','115.239.212.139','115.239.212.132','115.239.212.133','115.239.212.193','115.239.212.194','115.239.212.195','115.239.212.196','115.239.212.197','115.239.212.198','115.239.212.199','115.239.212.200',);
$botagent = array('googlebot','baiduspider');

$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
$ifbot = 0;
if(in_array($_SERVER['REMOTE_ADDR'], $baiduceip)) $ifbot++;
foreach ($botagent as $ua) {
	if(strstr($hostname, $ua)) $ifbot++;
}

if($ifbot > 0) {
	@header('HTTP/1.0 403 Forbidden');
	$stime = date('Y-m-d H:i:s');
	$log = $stime.' '.$hostname.' '.$_SERVER['REMOTE_ADDR'].' returned 403.'."\n";
	putintofile($log, $log_file);
	exit();
}

require('stream.php');
date_default_timezone_set('prc');
$programname='FFPanel';
$vernumber='1.0.5';
$buildat='20170113';
$file2 = '/tmp/twitch.txt';
$file3 = '/tmp/status.txt';
$log_file = '/tmp/bot.log';
$cron_off_file = '/tmp/cronoff';
$cron_on_file = '/tmp/cronon';
$huomao[]="send-a.huomaotv.cn";
?>