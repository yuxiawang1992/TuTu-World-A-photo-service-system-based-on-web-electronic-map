<?
error_reporting(E_ALL & ~E_STRICT & ~E_WARNING & ~E_NOTICE);
#error_reporting(E_ALL);
// 解析开始时间，common.func.php用到
$start_time = microtime(true);

date_default_timezone_set("Asia/Shanghai");
header("Content-type: text/html; charset=utf-8");

session_start();
require_once 'define.inc.php';
require_once 'config.inc.php';
require_once 'db.cls.php';
require_once 'global.fuc.php';

if( DEBUG==0 ){
	ini_set('html_errors', 0);
	ini_set('display_errors', 0);
}
else{
	ini_set('html_errors', 1);
	ini_set('display_errors', 1);
}


// 初始化数据库对象，连接后销毁数据库连接信息

$db = new DB;
$db->connect($dbs);
unset($dbs);

