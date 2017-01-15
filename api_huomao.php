<?php
require('config.php');

function getflvurl($sUrl){
$oCurl = curl_init();
$header[] = "Content-type: application/x-www-form-urlencoded";
$user_agent = "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.146 Safari/537.36";
curl_setopt($oCurl, CURLOPT_URL, $sUrl);
curl_setopt($oCurl, CURLOPT_HEADER, true);
curl_setopt($oCurl, CURLOPT_NOBODY, true);
curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt($oCurl, CURLOPT_POST, false);
$sContent = curl_exec($oCurl);
$headerSize = curl_getinfo($oCurl, CURLINFO_HEADER_SIZE);
$header = substr($sContent, 0, $headerSize);
curl_close($oCurl);
$temp = explode("\n", $header);
$temp = explode(": ", $temp[3]);
return $temp[1];
}

function readlastline($file) {
	$filearr = file($file);
	$count = count($filearr) - 1;
	return $filearr[$count];
}

function flvcheck($input) {
	$do = getflvurl("http://live-ws-hdl.huomaotv.cn/live/$input.flv");
	$do1 = `curl -ILs -m 3 $do | head -1`;
	return $do1;
}

function dostr_replace($input) {
	$dostr_replacekey = array('-a','.','rtmp://','send','huomaotv','/','cn','com','/live/','live');
	$count = count($dostr_replacekey);
	for($i=0;$i<$count;$i++) {
		$keynow = $dostr_replacekey[$i];
		$input = str_replace($keynow,'',$input);
	}
	return $input;
}

function getNeedBetween($kw1,$mark1,$mark2,$mustr){
	$kw = $kw1;
	$st = stripos($kw,$mark1);
	$ed = stripos($kw,$mark2);
	if(($st==false||$ed==false)||$st>=$ed)
	return 0;
	$kw=substr($kw,($st+$mustr),($ed-$st-$mustr));
	return $kw;
}

$laststat = readlastline('/tmp/huomao.log');
/* $laststat = explode(' ',$laststat);
if($laststat[1] == 'die') */
if(strstr($laststat, 'die')) {
	$huomaolastlive = false;
} else {
	$huomaolastlive = true;
}

$stime = date('Y-m-d H:i:s');
$live2 = dostr_replace($live2);
$live2 = explode('?k=', $live2);
$live2 = $live2[0];
$result = flvcheck($live2);
#echo $result;
if ($result!=null) {
	$huomaonowlive = true;
	$return = $live2.' HTTP/1.1 200 OK'."\n";
} else {
	$huomaonowlive = false;
	$return = $live2.' die'."\n";
}
if($huomaolastlive == $huomaonowlive) {
	exit();
} else {
	echo $stime."\n";
	echo $return;
}