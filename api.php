<?
require_once('config.php');

if(isset($_GET['autooff'])) {
	$act = $_GET['autooff'];
	$unixsjcneed = strtotime($_GET['autooff']);
	$do = `echo $unixsjcneed > $cron_off_file`;
	echo 'Autooff at '.$unixsjcneed;
	exit;
} elseif(isset($_GET['autoon'])) {
	$act = $_GET['autoon'];
	$unixsjcneed = strtotime($_GET['autoon']);
	$do = `echo $unixsjcneed > $cron_on_file`;
	echo 'Autoon at '.$unixsjcneed;
	exit;
} elseif(isset($_GET['do'])) {
	$act = $_GET['do'];
	switch ($act) {
		case on:
			$do = `echo 1 > $file3`;
		break;  
		case off:
			$do = `rm -rf $file3`;
			$do2 = `sudo killall ffmpeg`;
		break;
		case rst:
		/* 	$delay = '20';
			$delaydo = `sleep $delay killall ffmpeg`; */
			$do2 = `sudo killall ffmpeg`;
		break;
		case auto_off_off:
			$do = `rm -rf $cron_off_file`;
		break;
		case auto_on_off:
			$do = `rm -rf $cron_on_file`;
		break;
		default:
		echo 'ERROR.';
	}
}

$stime = date('Y-m-d H:i:s');
$userip = $_SERVER['REMOTE_ADDR'];
$log = $stime.' '.$act.' '.$userip.' '.$_SERVER['HTTP_USER_AGENT']."\n";
putintofile($log, '/tmp/control.log');
?>
<script>
window.close();
history.go(-1);
</script>