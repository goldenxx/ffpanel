<?php
$time_start = microtime(true);
date_default_timezone_set('PRC');
require('config.php');

$spanclass = array('default','primary','success','info','warning','danger');
$spanclassa = 'label label-';

function isMobile() {
	$_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';  
	$mobile_browser = '0';
	if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) $mobile_browser++;
	if((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false)) $mobile_browser++;
	if(isset($_SERVER['HTTP_X_WAP_PROFILE'])) $mobile_browser++;
	if(isset($_SERVER['HTTP_PROFILE'])) $mobile_browser++;  
	$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
	$mobile_agents = array(
		'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
		'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
		'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
		'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
		'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
		'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
		'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
		'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
		'wapr','webc','winw','winw','xda','xda-'
	);  
	if(in_array($mobile_ua, $mobile_agents)) $mobile_browser++;
	if(strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false) $mobile_browser++;
	if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false) $mobile_browser=0;
	if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false) $mobile_browser++;
	if($mobile_browser > 0) {
		return true;
	} else {
		return false;
	}
}

error_reporting(E_ALL);
header('content-Type: text/html; charset=utf-8');

if ($backgroundimg) {
$bingstr = file_get_contents('http://cn.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1');
$bingarray = json_decode($bingstr,true);
#var_dump($bingarray);
$bingimgurl = 'http://cn.bing.com/'.$bingarray['images'][0]['url'];
}

class CaoLa {	
	function server_ffpanel() {
		$jarr = json_encode(ffpanel()); 
		$_GET['callback'] = htmlspecialchars($_GET['callback']);
		return $_GET['callback'].'('.$jarr.')';
	}
}

//FFPanel
$statsjson = file_get_contents('/tmp/stats.json');
$stats = json_decode($statsjson, true);

function dostr_replace($input) {
	$dostr_replacekey = array('https','http','://','/','www','twitch.tv','.','-3a','-2f');
	for($i=0;$i<count($dostr_replacekey);$i++) {
		$keynow = $dostr_replacekey[$i];
		$input = str_replace($keynow,'',$input);
	}
	return $input;
}

function loadavg() {
	if(!is_readable('/proc/loadavg')) return false;
	$loadavg = explode(' ', file_get_contents('/proc/loadavg'));
	return implode(' ', current(array_chunk($loadavg, 4)));
}

function readlastline($file) {
	$filearr = file($file);
	$count = count($filearr) - 1;
	$return = str_replace("\n",'',$filearr[$count]);
	return $return;
}

function formatsize($size) {
	$key = 0;
	$danwei = array('B','K','M','G','T');
	while($size > 1024) {
		$size = $size / 1024;
		$key++;
	}
	$return = round($size, 3).$danwei[$key];
	return $return;
}

function ffpanel() {
	global $file2,$file3,$cron_off_file,$cron_on_file;

	$pcff = str_replace("\n",'',`program_check ffmpeg`);
	if ($pcff != "") {
		$ffpanel['fstat'] = $pcff;
	} else {
		$ffpanel['fstat'] = "ffmpeg not running";
	}
	
	if (file_exists($file3)) {
		$ffpanel['MasterSwitch'] = '开';
	} else {
		$ffpanel['MasterSwitch'] = '关';
	}
	
	if (file_exists($cron_off_file)) {
		$time = file_get_contents($cron_off_file);
		$ffpanel['cronoff'] = '计划于 '.date('Y-m-d H:i:s',$time).' 关播';
	} else {
		$ffpanel['cronoff'] = '未设置';
	}
	
	if (file_exists($cron_on_file)) {
		$time = file_get_contents($cron_on_file);
		$ffpanel['cronon'] = '计划于 '.date('Y-m-d H:i:s',$time).' 开播';
	} else {
		$ffpanel['cronon'] = '未设置';
	}
	
	$ffpanel['LastLog1'] = readlastline('/tmp/ffmpeg.log');
	
	$start_time = microtime(true);
	$twnow = file_get_contents($file2);
	if(strstr($twnow, 'iptv.tsinghua.edu.cn')) {
		$url = explode('?vid=',file_get_contents($file2));
		$ffpanel['StreamResult'] = $url[1].' Unknown';
		$ffpanel['TwitchTotal'] = 'Unknown';
		$ffpanel['ZhuanboAddr'] = 'iptv://'.$url[1];
	} else {
		$doing2 = dostr_replace($twnow);
		$doing3 = "https://api.twitch.tv/kraken/streams/".$doing2;
		$doing4 = `curl -LsH 'Client-ID: jzkbprff40iqj646a697cyrvl0zt2m6' "$doing3"`;
		$doing5 = json_decode($doing4,true);
		$ffpanel['twitchapi_time'] = round((microtime(true) - $start_time) * 1000, 2).'ms';
		
		$start_time = microtime(true);
		$twitchapi = 'http://advent.inovh.dazhizi.cloud/twitch.inc.php?twitch='.$doing2;
		$twitchjson = `curl -Ls -m 3 $twitchapi`;
		$twitcharray = json_decode($twitchjson,true);
		$ffpanel['twitchjson_time'] = round((microtime(true) - $start_time) * 1000, 2).'ms';
		
		if(isset($doing5['stream']['_id']) ) {
			$ffpanel['StreamResult'] = $doing2.' streaming';
			if(isset($twitcharray['media']['NAME'])) {
				$StreamBandwidth = $twitcharray['stream']['BANDWIDTH'] / 1000;
				$ffpanel['TwitchTotal'] = $doing2.' '.$twitcharray['media']['NAME'].':'.$twitcharray['stream']['RESOLUTION'].','.round($doing5['stream']['average_fps'], 2).'fps @ '.$StreamBandwidth.'Kbps';
			} else {
				$ffpanel['TwitchTotal'] = 'Twitch api timeout.';
			}
		} else {
			$ffpanel['StreamResult'] = $doing2.' not streaming';
			$ffpanel['TwitchTotal'] = $doing2.' not streaming';
		}
		$ffpanel['ZhuanboAddr'] = $twnow;
	}

	$ffpanel['timenow'] = date('Y-m-d H:i:s');
	#.'@'.$videoheightandfps;
	$huomaotxt = '/tmp/huomao.log';
	$ffpanel['HuomaoStat'] = readlastline($huomaotxt);
	$ffpanel['loadavg'] = loadavg();

	$net = file("/proc/net/dev"); 
	$netcount = count($net);
	$dev = array();
	for($i=2;$i<$netcount;$i++) {
		$linenow = $net[$i];
		$arrnow = explode(':', $linenow);
		if(strstr($arrnow[0], 'eth0')) {
			$strs = $arrnow[0].':'.$arrnow[1];
		} elseif (strstr($arrnow[0], 'venet0')) {
			$strs = $arrnow[0].':'.$arrnow[1];
		} else {
			continue;
		}
	}
	
	preg_match_all( "/([^\s]+):[\s]{0,}(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/", $strs, $info );
	#var_dump($info);
	$ffpanel['NetOutSpeed'] = $info[10][0];
	$ffpanel['NetInputSpeed'] = $info[2][0];
	$ffpanel['NetInput'] = formatsize($info[2][0]);
	$ffpanel['NetOut'] = formatsize($info[10][0]);

	return $ffpanel;
}

$hltvjson = file_get_contents('http://184.170.243.122:12101/api.php');

function hltvoutput($what) {
	global $hltvjson;
	$hltvjarr = json_decode($hltvjson, true);
	if($what == 'Match') {
		if($hltvjarr[0] != '') echo '<td colspan="8">'.$hltvjarr[0].'</td>';
	} elseif ($what == 'Stream') {
		if($hltvjarr[1] != '') echo '<td colspan="8">'.$hltvjarr[1].'</td>';
	} elseif ($what == 'ffmpeg') {
		echo $hltvjarr['ffmpeg'];
	}
}

$caola = new CaoLa();

$net = file("/proc/net/dev"); 
$netcount = count($net);
$dev = array();

for($i=2;$i<$netcount;$i++) {
	$linenow = $net[$i];
	$arrnow = explode(':', $linenow);
 	if(strstr($arrnow[0], 'eth0')) {
		$strs = $arrnow[0].':'.$arrnow[1];
		$netname = $arrnow[0];
	} elseif (strstr($arrnow[0], 'venet0')) {
		$strs = $arrnow[0].':'.$arrnow[1];
		$netname = $arrnow[0];
	} else {
		continue;
	}
}

preg_match_all( "/([^\s]+):[\s]{0,}(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/", $strs, $info );

$NetInput = $info[2][0];
$NetOut = $info[10][0];

if(isset($_GET['act'])){
	$act = $_GET['act'];
	switch($act) {
		case 'ffpanel':
		exit($caola->server_ffpanel());
		break;
		default: return $act; break;
	}
}
$shouji = isMobile();
?>
<!DOCTYPE html>
<html>
<head>
<title><?="$programname $vernumber build $buildat"?></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1<? if($shouji) { echo ', maximum-scale=1, user-scalable=no">';} else {echo '">';} ?>
<!-- 新 Bootstrap 核心 CSS 文件 -->
<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.0/css/bootstrap.min.css">

<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>

<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="http://cdn.bootcss.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>

<? if ($backgroundimg) { ?><style>.backgroundimg{background-image:url('<?=$bingimgurl;?>') ;background-attachment:fixed;background-repeat:no-repeat;background-size:cover;-moz-background-size:cover;-webkit-background-size:cover;}</style><? } ?>
<script type="text/javascript">
$(document).ready(function(){getJSONData();});
var OutSpeed=<?=$NetOut?>;
var InputSpeed=<?=$NetInput?>;
function getJSONData()
{
	setTimeout("getJSONData()", 1000);
	$.getJSON('?act=ffpanel&callback=?', displayData);
}

function ForDight(Dight,How)
{ 
  if (Dight<0){
  	var Last=0+"B/s";
  }else if (Dight<1024){
  	var Last=Math.round(Dight*Math.pow(10,How))/Math.pow(10,How)+"B/s";
  }else if (Dight<1048576){
  	Dight=Dight/1024;
  	var Last=Math.round(Dight*Math.pow(10,How))/Math.pow(10,How)+"K/s";
  }else{
  	Dight=Dight/1048576;
  	var Last=Math.round(Dight*Math.pow(10,How))/Math.pow(10,How)+"M/s";
  }
	return Last; 
}

function displayData(dataJSON)
{
	$("#cronon").html(dataJSON.cronon);
	$("#cronoff").html(dataJSON.cronoff);
	$("#svrtime").html(dataJSON.timenow);
	$("#loadavg").html(dataJSON.loadavg);
	$("#TwitchTotal").html(dataJSON.TwitchTotal);
	$("#HuomaoStat").html(dataJSON.HuomaoStat);
	$("#Twitchdelay").html(dataJSON.Twitchdelay);
	$("#Twitchtitle").html(dataJSON.Twitchtitle);
	$("#Videoheightandfps").html(dataJSON.Videoheightandfps);
	$("#StreamResult").html(dataJSON.StreamResult);
	$("#LastLog1").html(dataJSON.LastLog1);
	$("#RealtimePort").html(dataJSON.RealtimePort);
	$("#FFmpegStat").html(dataJSON.fstat);
	$("#LivestreamerStat").html(dataJSON.lstat);
	$("#MasterSwitch").html(dataJSON.MasterSwitch);
	$("#ZhuanboAddr").html(dataJSON.ZhuanboAddr);
	
	$("#NetOut").html(dataJSON.NetOut);
	$("#NetInput").html(dataJSON.NetInput);
	
	$("#NetOutSpeed").html(ForDight((dataJSON.NetOutSpeed-OutSpeed),3));	OutSpeed=dataJSON.NetOutSpeed;
	$("#NetInputSpeed").html(ForDight((dataJSON.NetInputSpeed-InputSpeed),3));	InputSpeed=dataJSON.NetInputSpeed;
}

<? if (!$shouji) { ?>
function partRefresh() {
document.getElementById("huomaoplayer").src = "<?=$zhibojian;?>";
}
<? } else {
$huomaoname = explode('/', $live2);
$huomaoname = explode('?', $huomaoname[2]);
$huomaoname = $huomaoname[0]; 
} ?>
function ajaxOn() {
document.getElementById("controlbox").src = "api.php?do=on";
}

function ajaxOff() {
document.getElementById("controlbox").src = "api.php?do=off";
}

function ajaxRst() {
document.getElementById("controlbox").src = "api.php?do=rst";
}

function ajaxOffCronoff() {
document.getElementById("controlbox").src = "api.php?do=auto_off_off";
}

function ajaxOnCronoff() {
document.getElementById("controlbox").src = "api.php?do=auto_on_off";
}

function twitchpush() {
var input = twitch.twitchaddr.value;
$.get("2.php", { twitch: input } );
}

function autooffpush() {
var input = autooff.autooff.value;
$.get("api.php", { autooff: input } );
}

function autoonpush() {
var input = autoon.autoon.value;
$.get("api.php", { autoon: input } );
}
</script>
</head>
<body>
<!--[if lt IE 8]>
    <div style="padding: 8px 0; background: #FBE3E4; color: #8A1F11; text-align: center" role="dialog">当前网页 <strong>不支持</strong> 你正在使用的浏览器. 为了正常的访问, 请 <a href="http://browsehappy.com/" style="color: #8A1F11; text-decoration: underline; font-weight: 700">升级你的浏览器</a>.</div>
<![endif]-->
<div class="backgroundimg">
<div class="container">
	<div class="table-responsive">
	<table></table>
	<table width="94%" class="table table-bordered table-striped">
	  <thead>
	    <tr>
	      <th colspan="7">FFPanel</th>
        </tr>
      </thead>
	  <tbody>
	    <tr>
	      <td width="10%"><code>总开关</code> <span id="MasterSwitch">...</span></td>
	      <td>
	        <a class="btn btn-success btn-xs" href="javascript:ajaxOn();" role="button">开</a> 
	        <a class="btn btn-info btn-xs" href="javascript:ajaxOff();" role="button">关</a> 
	        <a class="btn btn-warning btn-xs" href="javascript:ajaxRst();" role="button">重启</a>
	        <? if(!$shouji) { ?>
	        <a class="btn btn-primary btn-xs" href="javascript:partRefresh();" role="button">刷新播放器</a></td>
	      <? } ?>
	      <td width="10%"><code>程序状态</code></td>
	      <td width="20%" style="white-space: nowrap"><abbr title="Twitch状态"><samp><span id="StreamResult">Loading...</span></samp></abbr></td>
	      <td width="20%" style="white-space: nowrap"><abbr title="火猫状态(参考,不一定准确)"><samp><span id="HuomaoStat">Loading...</span></samp></abbr></td>
	      <td width="20%"><abbr title="转播进程状态"><samp><span id="FFmpegStat">Loading...</span></samp></abbr></td>
        </tr>
<?
$timenow = time() + 10800;
$time_0 = date('Y-m-d', $timenow);
$time_1 = date('H:i', $timenow);
$timenow = $time_0.'T'.$time_1;
?>
	    <tr>
	      <td>
		  <code>定时开播</code>
		  </td>
	      <td width="100px">
	        <form role="form" name="autoon">
	          <input class="form-control input-sm" type="datetime-local" style="height:20px;width:170px;" name="autoon" value="<?=$timenow;?>"/>
            </form>
		  </td>
	      <td><a class="btn btn-primary btn-xs" onclick="autoonpush();" role="button">GO</a></td>
	      <td colspan="3"><code><span id="cronon">...</span></code> <a class="btn btn-info btn-xs" href="javascript:ajaxOnCronoff();" role="button">取消</a></td>
	      </tr>
	    <tr>
		
	    <tr>
	      <td>
		  <code>定时关播</code>
		  </td>
	      <td width="100px">
	        <form role="form" name="autooff">
				<input class="form-control input-sm" type="datetime-local" style="height:20px;width:170px;" name="autooff" value="<?=$timenow;?>"/>
            </form>
		  </td>
	      <td><a class="btn btn-primary btn-xs" onclick="autooffpush();" role="button">GO</a></td>
	      <td colspan="3"><code><span id="cronoff">...</span></code> <a class="btn btn-info btn-xs" href="javascript:ajaxOffCronoff();" role="button">取消</a></td>
	      </tr>

	      <td><code>设置转播地址</code></td>
	      <td colspan="2"><code><samp><span id="ZhuanboAddr">Loading...</span></samp></code></td>
	      <td colspan="2">
	        <form role="form" name="twitch">
	          <input class="form-control input-sm" type="text" style="height:20px" name="twitchaddr" value=""/>
            </form></td>
	      <td colspan="2"><a class="btn btn-primary btn-xs" onclick="twitchpush();" role="button">GO</a></td>
	      </tr>
	    <tr>
	      <td colspan="3" style="white-space: nowrap"><code><span id="TwitchTotal">Loading...</code></td>
	      <td colspan="4"><code><span id="LastLog1">Loading...</code></td>
	      </tr>
	    </tbody>	
	  </table>
	  <table width="94%" class="table table-bordered table-striped">
	   <tbody>
		<tr>
		    <td width="1%" nowrap><code>当前节点</code></td>
		    <td width="1%" nowrap><code><?=$stats['localipinfo']?></code></td>
		    <td width="1%" nowrap><code>火猫上传</code></td>
		    <td width="1%" nowrap><code><?=$stats['huomaosenda'][0]['detail']?></code></td>
		    <td width="1%" nowrap><code>延迟</code></td>
		    <td width="1%" nowrap><code><?=$stats['huomaosenda'][0]['latency']?></code></td>
		    <td width="1%" nowrap><code>系统平均负载</code></td>
		    <td width="1%" nowrap><code><span id="loadavg">Loading...</span></code></td>
		</tr>
        </tbody>
	  </table>
	  
	  <table width="94%" class="table table-bordered table-striped">
	  	<tbody>
	    <tr>
          <td width="12.5%" nowrap><code>入网：<span id="NetInput">Loading...</span></code></td>
          <td width="12.5%" nowrap><code>实时：<span id="NetInputSpeed">Loading...</span></code></td>
          <td width="12.5%" nowrap><code>出网：<span id="NetOut">Loading...</span></code></td>
          <td width="12.5%" nowrap><code>实时：<span id="NetOutSpeed">Loading...</span></code></td>
		  <td width="25%" nowrap><code>当前数据时间：<span id="svrtime">Loading...</span></code></td>
		  <td width="25%" nowrap><code>FFMPEG local = <?=$stats['ffmpegver']?> remote = <? hltvoutput('ffmpeg');?></code></td>
        </tr>
	    <tr><? hltvoutput('Match');?></tr>
		<tr><? hltvoutput('Stream');?></tr>
      </tbody>
	  </table>
  </div>
</div>

<div align="center">
<? if(!$shouji) {?>
	<iframe id="huomaoplayer" height=641 width=1139 src='<?=$zhibojian;?>' frameborder=0 allowfullscreen></iframe>
<? } else { ?>
	 <video controls autoplay="autoplay" name="media" style="width: 100%;"><source src="http://live-ws-hls.huomaotv.cn/live/<?=$huomaoname?>_720/playlist.m3u8"></video>
<? } ?>
</div>

<div class="container">
<div class="table-responsive">
<table></table>
<iframe id="controlbox" height=1 width=1 src="about:black" style="display:none;"></iframe>

  <table class="table table-bordered">
		<tr>
			<td width="25%"><?=$serverid;?> </td>
			<td width="50%"><div align="center">
	        <code>Processed in <?=(microtime(true) - $time_start)?> seconds. <?=round(memory_get_usage()/1024/1024, 2);?> memory usage.</code></div></td>
			<td width="25%"><div align="center"><code><a href="http://steamcommunity.com/id/ctcloud"><?="$programname $vernumber build $buildat";?></a></code>
			<script language="javascript" type="text/javascript" src="http://js.users.51.la/19075789.js"></script>
			<noscript><a href="http://www.51.la/?19075789" target="_blank"><img alt="&#x6211;&#x8981;&#x5566;&#x514D;&#x8D39;&#x7EDF;&#x8BA1;" src="http://img.users.51.la/19075789.asp" style="border:none" /></a></noscript>
			</div>
			</td>
	</tr>
  </table>
</div>
</div>
</div>
</body>
</html>