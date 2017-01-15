<?php
ignore_user_abort(true); // 后台运行
require_once('config.php');

if(file_exists($cron_off_file)) {
	$cronset = file_get_contents($cron_off_file);
	$timenow = time();
	if($cronset - $timenow < 10) {
		`killall ffmpeg`; //杀死转播进程
		`rm -rf $cron_off_file`; //删除cron文件，前台显示未设置
		`rm -rf $file3`; //设置总开关为关
		putintofile(date('Y-m-d H:i:s').' auto turn off'."\n", $log_file); //写入日志
		exit();
	}
}

if(file_exists($cron_on_file)) {
	$cronset = file_get_contents($cron_on_file);
	$timenow = time();
	if($cronset - $timenow < 10) {
		`rm -rf $cron_on_file`; //删除cron文件，前台显示未设置
		`echo 1 > $file3`; //设置总开关为开
		putintofile(date('Y-m-d H:i:s').' auto turn on'."\n", $log_file); //写入日志
		exit();
	}
}


if(file_exists($file3)) {
	$twnow = dostr_replace(file_get_contents($file2));
	$doing3 = "https://api.twitch.tv/kraken/streams/".$twnow;
	$doing4 = `curl -LsH 'Client-ID: jzkbprff40iqj646a697cyrvl0zt2m6' "$doing3"`;
	$twitch_stat = json_decode($doing4, true);
	$url = 'http://127.0.0.1/twitch.inc.php?twitch='.$twnow;
	$twitchjson = `curl -Ls $url`;
	$twitch_m3u8 = json_decode($twitchjson, true);
} else {
	exit();
}

$ffmpeg = checkffmpeg();
if(!$ffmpeg and strstr(file_get_contents($file2), 'iptv.tsinghua.edu.cn')) {
	$url = explode('?vid=',file_get_contents($file2));
	$m3u8 = 'https://iptv.tsinghua.edu.cn/hls/'.$url[1].'.m3u8';
	$stime = date('Y-m-d H:i:s');
	echo "$stime Starting stream $url[1] to $huomao[0].\n";
	$cmd = "ffmpeg -re -i '".$m3u8."' -acodec aac -strict -2 -vcodec copy -f flv -y ".'"rtmp://'.$huomao[0].'/'.$live2.'"';
	$start = local_exec($cmd);
	exit();
}

function get_ffmpeg_start_time() {
	$tmp = str_replace("\n",'',`program_check ffmpeg`);
	if($tmp != '') {
		$tmp = explode('= ',$tmp);
		$pid = $tmp[1];
		$stats = `ps -aux | grep ffmpeg | grep $pid`;
		$lines = explode("\n",$stats);
		for($i=0;$i<count($lines);$i++) {
			$linenow = $lines[$i];
			if(strstr($linenow,'ffmpeg -re -i')) $line = $linenow;
		}
	$line = explode('ffmpeg -re -i',$line);
	$line = explode('   ',$line[0]);
	$instr = count($line) - 2;
	$start_time = str_replace(' ','',$line[$instr]);
	}
	return $start_time;
}

function dostr_replace($input) {
	$dostr_replacekey = array('https','http','://','/','www','twitch.tv','.');
	$count = count($dostr_replacekey);
	for($i=0;$i<$count;$i++) {
		$keynow = $dostr_replacekey[$i];
		$input = str_replace($keynow,'',$input);
	}
	return $input;
}

function local_exec($cmd) {
    $start_time = microtime(true);
    exec($cmd, $output, $errno);
    $elapsed_time = microtime(true) - $start_time;
    $output = implode("\n", $output);
    return array($errno, $output, $elapsed_time);
}

function readlastline($file) {
	$filearr = file($file);
	$count = count($filearr) - 1;
	return $filearr[$count];
}


function checkffmpeg() {
	$testff = strstr(`program_check ffmpeg`,"pid");
	if ($testff == false) {
		return false;
	} else {
		return true;
	}
}

function checktwitch() {
	global $file2,$twitch_stat;
	if(is_readable($file2)) {
		$twnow = file_get_contents($file2);
	} else {
		die();
	}
	$doing5 = $twitch_stat;
	if (isset($doing5['stream']['_id'])) {
		return true;
	} else {
		return false;
	}
}

function checkhuomao() {
	$laststat = str_replace("\n",'',readlastline('/tmp/huomao.log'));
	$tmp = explode(" ",$laststat);
	$tmp = $tmp[1];
	if(strcmp($tmp,'die') == 0) {
		return false;
	} else {
		return true;
	}
}

function huomaodietimecheck() {
	$huomaostart = get_ffmpeg_start_time();
	if(!strstr($huomaostart,':')) return false;
	$time = explode(':',$huomaostart);
	#$hourstart = $time[0];
	$minstart = $time[1];
	#$hournow = date('H');
	$minnow = date('i');
	$sinceStart = $minnow - $minstart;
	if($sinceStart > 1) {
		`killall ffmpeg`;
		putintofile(date('Y-m-d H:i:s').' auto restart'."\n", '/tmp/ffapi.log'); //写入日志
	}
}

$twstat = checktwitch();
$hmstat = checkhuomao();
$ffstat = checkffmpeg();

if($twstat == false and $hmstat == false and $ffstat == true) `killall ffmpeg`;
#if($twstat == true and $hmstat == false and $ffstat == true) huomaodietimecheck();

if(file_exists($file3)) {
	$testff = checkffmpeg();
	if ($testff == false) {
		$doing5 = $twitch_stat;
		if (isset($doing5['stream']['_id'])) {
			$twitcharray = $twitch_m3u8;
			$stime = date('Y-m-d H:i:s');
			echo "$stime Starting stream $twnow to $huomao[0].\n";
			#echo $cmd;
			$cmd = "ffmpeg -re -i '".$twitcharray['url']."' -acodec aac -strict -2 -vcodec copy -f flv -y ".'"rtmp://'.$huomao[0].'/'.$live2.'"';
			$start = local_exec($cmd);
			#echo $start[2];
		}
	} else {
		exit();
	}
} else {
	exit();
}
?>