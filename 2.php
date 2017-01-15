<?php
require_once('config.php');
if(!isset($_GET['twitch'])) die('no input');
$input = $_GET['twitch'];
$l_twitch = parse_url($_GET['twitch']);
$input_host = $l_twitch['host'];
$input_path = $l_twitch['path'];

if (strstr($input, '-3a')) {
	@header('HTTP/1.0 403 Forbidden');
	$log = $stime.' '.$input.' '.$userip.' baiduyungc return 403'."\n";
	putintofile($log, $log_file);
	exit();
}

$stime = date('Y-m-d H:i:s');
$userip = $_SERVER['REMOTE_ADDR'];
$log = $stime.' '.$input.' '.$userip.' '.$_SERVER['HTTP_USER_AGENT']."\n";
putintofile($log, $log_file);

function getNeedBetween($kw1, $mark1, $mark2, $mustr){
	$kw = $kw1;
	$st = stripos($kw,$mark1);
	$ed = stripos($kw,$mark2);
	if(($st==false||$ed==false)||$st>=$ed) die();
	$kw=substr($kw,($st+$mustr),($ed-$st-$mustr));
	return $kw;
}

function curl($input){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $input);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

if(strstr($input_host, "twitch.tv")) {
	$realtwitch = strtolower($input); //大写转小写
	#echo 'twitch';
}

if(strstr($input_host, "iptv.tsinghua.edu.cn")) {
	$realtwitch = strtolower($input); //大写转小写
	#echo 'twitch';
}

if(strstr($input_host, "hltv.org")) {
	if (strstr($input,"286")) {
		#echo 'streamid';
		$hltvstreampage = $_GET['twitch'];
		#echo $hltvstreampage;
		$output = curl($hltvstreampage);
		$input = getNeedBetween($output, '<iframe src="http://player.twitch.tv/?channel=' , '" frameborder="0"' , '46');
		$inputa = 'https://www.twitch.tv/'.strtolower($input);
		$realtwitch = $inputa;
	} elseif (strstr($input,"match")) {
		#echo 'match';
		$keyword = curl($input);
		$tmparr = explode("\n", $keyword);
		foreach ($tmparr as $line) {
			if(strstr($line,'<div class="hotmatchroundbox" style=""><div style="cursor:pointer;width:240px;" class="stream"')) {
				$streamid = getNeedBetween($line, 'class="stream" id="' , '"><img style=' , '19');
			}
		}
		if(isset($streamid)) {
			$hltvstreampage = 'www.hltv.org/?pageid=286&streamid='.$streamid;
		} else {
			die('no result for match');
		}
		#echo $hltvstreampage;
		$output = curl($hltvstreampage);
		$input = getNeedBetween($output, '<iframe src="http://player.twitch.tv/?channel=' , '" frameborder="0"' , '46');
		$inputa = 'https://www.twitch.tv/'.strtolower($input);
		$realtwitch = $inputa;
		}
}
if(!is_string($realtwitch)) die('not a valid date');
$file = fopen($file2, "w");
fwrite($file, $realtwitch);
fclose($file);
$kill1 = `killall ffmpeg`;
$log = $stime.' '.$input.' '.$userip.' '.$_SERVER['HTTP_USER_AGENT']."\n";
putintofile($log, $log_file);
?>
<script>
window.close();
history.go(-1);
</script>