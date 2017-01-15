# ffpanel
转播Twitch到任意国内直播平台

Todo:
新建一个stream.php
```php
<?
$live2 = '/live/'; //火猫串流码
$room = ''; //火猫房间号
$serverid = '';
$zhibojian = 'http://www.huomao.com/outplayer/index/'.$room;
$backgroundimg = true;
?>
```
crontab:
```bash
* * * * * php /data/www/default/api_ff.php >>/tmp/ffmpeg.log
* * * * * sleep 3; php /data/www/default/api_ff.php >>/tmp/ffmpeg.log
* * * * * sleep 6; php /data/www/default/api_ff.php >>/tmp/ffmpeg.log
* * * * * sleep 9; php /data/www/default/api_ff.php >>/tmp/ffmpeg.log
* * * * * sleep 12; php /data/www/default/api_ff.php >>/tmp/ffmpeg.log
* * * * * sleep 15; php /data/www/default/api_ff.php >>/tmp/ffmpeg.log
* * * * * sleep 18; php /data/www/default/api_ff.php >>/tmp/ffmpeg.log
* * * * * sleep 21; php /data/www/default/api_ff.php >>/tmp/ffmpeg.log
* * * * * sleep 24; php /data/www/default/api_ff.php >>/tmp/ffmpeg.log
* * * * * sleep 27; php /data/www/default/api_ff.php >>/tmp/ffmpeg.log
* * * * * sleep 30; php /data/www/default/api_ff.php >>/tmp/ffmpeg.log
* * * * * sleep 33; php /data/www/default/api_ff.php >>/tmp/ffmpeg.log
* * * * * sleep 36; php /data/www/default/api_ff.php >>/tmp/ffmpeg.log
* * * * * sleep 39; php /data/www/default/api_ff.php >>/tmp/ffmpeg.log
* * * * * sleep 42; php /data/www/default/api_ff.php >>/tmp/ffmpeg.log
* * * * * sleep 45; php /data/www/default/api_ff.php >>/tmp/ffmpeg.log
* * * * * sleep 48; php /data/www/default/api_ff.php >>/tmp/ffmpeg.log
* * * * * sleep 51; php /data/www/default/api_ff.php >>/tmp/ffmpeg.log
* * * * * sleep 54; php /data/www/default/api_ff.php >>/tmp/ffmpeg.log
* * * * * sleep 57; php /data/www/default/api_ff.php >>/tmp/ffmpeg.log
* * * * * php /data/www/default/api_huomao.php >>/tmp/huomao.log
* * * * * sleep 3; php /data/www/default/api_huomao.php >>/tmp/huomao.log
* * * * * sleep 6; php /data/www/default/api_huomao.php >>/tmp/huomao.log
* * * * * sleep 9; php /data/www/default/api_huomao.php >>/tmp/huomao.log
* * * * * sleep 12; php /data/www/default/api_huomao.php >>/tmp/huomao.log
* * * * * sleep 15; php /data/www/default/api_huomao.php >>/tmp/huomao.log
* * * * * sleep 18; php /data/www/default/api_huomao.php >>/tmp/huomao.log
* * * * * sleep 21; php /data/www/default/api_huomao.php >>/tmp/huomao.log
* * * * * sleep 24; php /data/www/default/api_huomao.php >>/tmp/huomao.log
* * * * * sleep 27; php /data/www/default/api_huomao.php >>/tmp/huomao.log
* * * * * sleep 30; php /data/www/default/api_huomao.php >>/tmp/huomao.log
* * * * * sleep 33; php /data/www/default/api_huomao.php >>/tmp/huomao.log
* * * * * sleep 36; php /data/www/default/api_huomao.php >>/tmp/huomao.log
* * * * * sleep 39; php /data/www/default/api_huomao.php >>/tmp/huomao.log
* * * * * sleep 42; php /data/www/default/api_huomao.php >>/tmp/huomao.log
* * * * * sleep 45; php /data/www/default/api_huomao.php >>/tmp/huomao.log
* * * * * sleep 48; php /data/www/default/api_huomao.php >>/tmp/huomao.log
* * * * * sleep 51; php /data/www/default/api_huomao.php >>/tmp/huomao.log
* * * * * sleep 54; php /data/www/default/api_huomao.php >>/tmp/huomao.log
* * * * * sleep 57; php /data/www/default/api_huomao.php >>/tmp/huomao.log
```
支持lamp环境，lnmp下未测试。
