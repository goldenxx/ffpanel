<?
function ffmpegver() {
	`ffmpeg > /tmp/temp 2>/tmp/temp`;
	exec('cat /tmp/temp', $ffmpeg, $errno);
	`rm -rf /tmp/temp`;
	#$lines = explode("\n", $ffmpeg);
	$words = explode(" ", $ffmpeg[0]);
	$ver = explode("-", $words[2]);
	$ver = $ver[0];
	return $ver;
}

function virtwhat() {
	$virt = `sudo virt-what`;
	$virt = str_replace("\n", "", $virt);
	if($virt == '') {
		return 'dedicated';
	} else {
		return $virt;
	}
}

$ip = `ip addr | egrep -o '[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}' | egrep -v "^192\.168|^172\.1[6-9]\.|^172\.2[0-9]\.|^172\.3[0-2]\.|^10\.|^127\.|^255\.|^0\." | head -n 1`;
#$ip = str_replace("addr:","","$ip");
$ip = str_replace("\n","","$ip");

function ipipdetailget($input) {
	$ipipjson = file_get_contents("http://ip.huomao.com/ip?ip=$input");
	#echo $ipipjson;
	$ipiparr = json_decode($ipipjson, true);
	$ipiparrkey = array ('country', 'province', 'city', 'isp');
	for ($x=0; $x<=2; $x++) {
		$keynow = $ipiparrkey[$x];
		$datenow = $ipiparr[$keynow];
		$fornext = $x + 1;
		$keynext = $ipiparrkey[$fornext];
		$datenext = $ipiparr[$keynext];
		if ($datenow == $datenext) $ipiparr[$keynext] = null;
		if ($datenow != null) $ipiparr[$keynow] = $ipiparr[$keynow].' ';
	}
	return $ipiparr['country'].$ipiparr['province'].$ipiparr['city'].$ipiparr['isp'];
}

function latencycount($input) {
	$ping = `ping -c 1 $input | grep 'icmp_seq' | awk '{print $7}'`;
	$ping = str_replace("time=","",$ping);
	$ping = str_replace("\n","",$ping);
	$ping = $ping.'ms';
	return $ping;
}

function resultoutput($input) {
	$hostresult = `host $input | grep 'address' | awk '{print $4}'`;
	$lines = explode("\n", $hostresult);
	$realipcount = count($lines) - 1;
	for ($x=0; $x<$realipcount; $x++) {
		$realip = $lines[$x];
		$realip = str_replace("\n","",$realip);
		$arr[$x] = array(
				"ip" => $realip,
				"detail" => ipipdetailget($realip),
				"latency" => latencycount($realip)
		);
	}
	return $arr;
	#return $realip;
}

function cpuinfo() {
	$cpuinfo = file('/proc/cpuinfo');
	foreach ($cpuinfo as $line) {
		$templine = str_replace("\t", '', $line);
		$templine = str_replace("\n", '', $templine);
		$cpuinfo[] = $templine;
	}
	$cpu = array();
	foreach ($cpuinfo as $line) {
		$cpu[] = explode(':', $line);
	}
	$json = str_replace('\t', '', json_encode($cpu));
	$json = str_replace('\n', '', $json);
	$cpuinfo = json_decode($json, true);
	unset($json, $cpu);
	$cpu = array();
	foreach($cpuinfo as $array) {
		$name = $array[0];
		if($name == '') continue;
		$val = ltrim($array[1]);
		$cpu[$name] = $val;
	}
	return $cpu['model name'];
}

$json['localipinfo'] = ipipdetailget($ip);
$json['virt'] = virtwhat();
$json['cpu'] = cpuinfo();
$json['huomaosenda'] = resultoutput('send-a.huomaotv.cn');
$json['huomaosend'] = resultoutput('send.huomaotv.com');
$json['ffmpegver'] = ffmpegver();
$json = str_replace("\n","",$json);
$json = json_encode($json);
header("Content-Type: application/json");
echo $json."\n";