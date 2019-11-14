<?

function get_cdn_url($avatar){
	if( substr_count(ROOT_URL, $avatar) ){
		$avatar = str_replace(ROOT_URL, '', $avatar);
	}
	return STATIC_IMAGES_URL.str_replace('static/images/', '', $avatar);
}

function get_mt($precision=3){
	$mt = number_format(microtime(1), $precision, '.', '');
	return $mt;
}

function transfer_img_to_static($avatar){
	return get_cdn_url($avatar);
}

function mod(){
	global $mod;
	return strtr($mod, '-', '_');
}

function act(){
	global $act;
	return strtr($act, '-', '_');
}

//从body中提取img
function get_img_src($body){
	if( stripos($body, '<img ')===FALSE ){
		$body_img = ROOT_URL.'static/images/random/'.rand(1,10).'.jpg';
	}
	else{
		$body_img = end(explode('<img ', $body, 2));
		$body_img = reset(explode('>', $body_img, 2));
		$body_img = end(explode('src="', $body_img, 2));
		$body_img = reset(explode('"', $body_img, 2));
	}
	$url = $body_img;
	return $url;
}

// 读写kookie @ 2013-08-12 19:09:32
function ck($s, $v = FALSE, $st = NULL) {
    $time = time();
    if ($s == '') $ret = '未定义cookie名';
    $prefix = _S('cookie_prefix');
    if (strstr($s, $prefix) === false) $s = $prefix . $s;
    if ($v !== FALSE) {
        $st = $st !== NULL ? $st : ($time + 90 * 86400);
        setcookie($s, $v, $st, _S('cookie_path'));
        $ret = 1;
    } else {
        $ret = $_COOKIE[$s];
    }
    return $ret;
}

/**
 * 兼容.htaccess的额外参数
 * @return string 
 */
function get_url_addition(){
	return $_GET['__addition'];
}

function uploadPic($file){
	$tempFile = $file['tmp_name'];
	$targetFile = getImageUploadPath($tempFile);
	if( file_exists($targetFile) ){
		$src = str_replace(ROOT_PATH, '', $targetFile);
		return $src;
	}
	$typerror = '格式不正确';
	if( strpos($targetFile, 'no-extension-founded.jpg')!==FALSE ){
		return $typerror;
	}
	$fileTypes = array('jpg','jpeg','gif','png');
	$fileParts = pathinfo($file['name']);

	if (in_array(strtolower($fileParts['extension']), $fileTypes)) {
		$res = move_uploaded_file($tempFile, $targetFile);
		if($res){
		  $src = str_replace(ROOT_PATH, '', $targetFile);
		  return $src;
		}
		else{
		  return '服务器权限有误';
		}
	} else {
		return $typerror;
	}
}

//正确切汉字字符串函数 Jimack 121102
//$String为输入汉字，$Length为数量 仅适用UTF-8
function sub_str($String, $Length = 10, $Append = '...', $triple = 1) {
    if (function_exists('mb_substr')) {
        $sub = mb_substr($String, 0, $Length, 'utf-8');
        if ($sub != $String) $needappend = $Append;
        return $sub . $needappend;
    }
    if ($triple) $Length*= 3;
    if (strlen($String) <= $Length) {
        return $String;
    } else {
        $I = 0;
        while ($I < $Length) {
            $StringTMP = substr($String, $I, 1);
            if (ord($StringTMP) >= 224) {
                $StringTMP = substr($String, $I, 3);
                $I = $I + 3;
            } elseif (ord($StringTMP) >= 192) {
                $StringTMP = substr($String, $I, 2);
                $I = $I + 2;
            } else {
                $I = $I + 1;
            }
            $StringLast[] = $StringTMP;
        }
        $StringLast = implode("", $StringLast);
        if ($Append) {
            $StringLast.= $Append;
        }
        return $StringLast;
    }
}

function getSafeInput($arr){
	$post = array();
	$arr = is_array($arr) ? $arr : $_POST;
	foreach( $arr as $k=>$v ){
		$post[mysql_real_escape_string(trim($k))] = mysql_real_escape_string(trim($v));
	}
	return $post;
}

function timeInterval($iv) {
    if ($iv > 86400) {
        $res = round($iv / 86400, 0) . '天';
    } else if ($iv > 3600) {
        $res = round($iv / 3600, 0) . '小时';
    } else if ($iv >= 0) {
        $res = round($iv / 60, 0) . '分钟';
    } else {
        // 已过
        $res = '-' . timeInterval(0 - $iv);
    }
    return $res;
}

function setSystemMessage($msg){
	if( $msg=='' ){
		return FALSE;
	}
	$_SESSION['__message'] = $msg;
}
function getSystemMessage(){
	$msg = $_SESSION['__message'];
	unset($_SESSION['__message']);
	return $msg;
}


function trd($msg, $url=NULL){
	if($msg!='' && $url!=''){
		setSystemMessage($msg);
	}
	if( $msg!='' && $url===NULL ){
		$url = $msg;
	}
	$url = $url ?: ROOT_URL;
	header('Location:'.$url);
	die();
}

function _S($meta, $value=FALSE){
	global $db, $cfg_;
	if( $meta=='' ){
		return;
	}
	$db_name = 'config';
	$meta = mysql_real_escape_string($meta);
	// 取值
	if( $value===FALSE ){
		if( !empty($cfg_[$meta]) ){
			return $cfg_[$meta];
		}
		return $db->val("select value from $db_name where `meta`='$meta'");
	}
	// 强制过期
	elseif( $value===NULL ){
		$db->query("delete from $db_name where `meta`='$meta'");
	}
	// 设值
	else{
		// 要使用NULL来判断，可能缓存被清空时value是""
		if( _S($meta)!==NULL ){
			$res = $db->query("update $db_name set value='$value' where `meta`='$meta'");
		}
		else{
			$res = $db->query("insert into $db_name values ('$meta', '$value', '1')");
		}
		return $res;
	}
}

// 下载远程图片 @ 2013-08-10 20:19:46
function downPic($url){
	if( substr_count($url, 'http')>0 && substr_count($url, ROOT_URL)==0 ){
		$tmpfname = tempnam(sys_get_temp_dir(), 'DCMS');
		file_put_contents($tmpfname, curl($url));
		$filename = getImageUploadPath($tmpfname);
		rename($tmpfname, $filename);
	}
	else{
		$filename = $url;
	}

	$src = str_replace(ROOT_PATH, '', $filename);
	return $src;
}

function curl($url, $post_arr=NULL){
	if( !function_exists('curl_init') ){
		die('未开启curl扩展。');
	}
	$curl = curl_init();
    $header = array();
    $host = reset(explode('/', end(explode('://', $url, 2))));
    $cookie = '';
    $header[] = 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1736.2 Safari/537.36';
    $header[] = 'Cache-Control: max-age=0';
    $header[] = 'Connection: keep-alive';
    $header[] = 'Cookie: '.$cookie;
    $header[] = 'Host: '.$host;

    //$Ref = "http://www.thinkful.cn";
    $Options = array(
      CURLOPT_HTTPHEADER => $header,
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_FOLLOWLOCATION => TRUE,
      //CURLOPT_REFERER    => $Ref,
      CURLOPT_CONNECTTIMEOUT => 10
    );
	if( is_array($post_arr) ){
		$Options = array_merge(
			$Options,
			array(
				CURLOPT_POST => TRUE,
				CURLOPT_POSTFIELDS => $post_arr
			)
		);
	}
	//deb($Options);
    curl_setopt_array($curl, $Options);
    $result = curl_exec($curl);
    if ( $result === FALSE ){
    	return FALSE;
	}
    $contents = trim($result);
    $info = curl_getinfo($curl);
    curl_close($curl);
    return $contents;
}

function format($o){
	if( is_array($o) ){
		foreach( $o as $k=>$v ){
			$o[$k] = format($v);
		}
		return $o;
	}
	else{
		return mysql_real_escape_string(trim(htmlspecialchars($o)));
	}
}

// 与js相关的unicode函数 @ 2014-02-09 16:37:31
function enunicode($name){
	if( stripos($_SERVER['PATH'], '\\Program Files (x86)\\')!==FALSE ){
		$GLOBALS['PROCESSOR_ARCHITECTURE'] = 'x86';
	}
	$name = iconv('UTF-8', 'UCS-2', $name);
	$len  = strlen($name);
	$str  = '';
	for ($i = 0; $i < $len - 1; $i = $i + 2){
		// 64位 | Linux系统需要将$c和$c2调换位置 @ 2014-02-11 16:52:21
		$c  = $name[$i];
		$c2 = $name[$i + 1];
		if( $GLOBALS['PROCESSOR_ARCHITECTURE']!='x86' ){
			$tmp = $c;
			$c = $c2;
			$c2 = $tmp;
		}
		if (ord($c) > 0){   //两个字节的文字
			$str .= '\u'.base_convert(ord($c), 10, 16).str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
		} else {
			$str .= '\u'.str_pad(base_convert(ord($c2), 10, 16), 4, 0, STR_PAD_LEFT);
		}
	}
	return $str;
}
function deunicode($name){
	if( stripos($_SERVER['PATH'], '\\Program Files (x86)\\')!==FALSE ){
		$GLOBALS['PROCESSOR_ARCHITECTURE'] = 'x86';
	}
	// 转换编码，将Unicode编码转换成可以浏览的utf-8编码
	// 改进使之适用于-和空格 @ 2014-03-17 02:39:23
	$pattern = '/([\w-\s]+)|(\\\u([\w]{4}))/i';
	preg_match_all($pattern, $name, $matches);
	if (!empty($matches)){
		//deb($matches);
		$name = '';
		for ($j = 0; $j < count($matches[0]); $j++){
			$str = $matches[0][$j];
			if (strpos($str, '\\u') === 0){
				// 64位 | Linux系统需要将$c和$c2调换位置 @ 2014-02-11 16:52:21
				$c = base_convert(substr($str, 2, 2), 16, 10);
				$c2 = base_convert(substr($str, 4), 16, 10);
				if( $GLOBALS['PROCESSOR_ARCHITECTURE']!='x86' ){
					$tmp = $c;
					$c = $c2;
					$c2 = $tmp;
				}
				$c = chr($c).chr($c2);
				$c = iconv('UCS-2', 'UTF-8', $c);
				$name .= $c;
			}
			else{
				$name .= $str;
			}
		}
	}
	return $name;
}


// 通过上传图片的hash指纹创建唯一位置 @ 2014-02-17 23:22:19
function getImageUploadPath($tempFile, $pathprefix=''){
    $pathprefix = $pathprefix!='' ? $pathprefix : 'static/images/';
    // 使用新的获取ext方式 @ 2014-07-20 16:58:14
    $info = getimagesize($tempFile);
    $extension_hash = array(
    	'1' => 'gif',
    	'2' => 'jpg',
    	'3' => 'png',
    	'4' => 'swf',
    	'5' => 'psd',
    	'6' => 'bmp',
    	'7' => 'tiff',
    );
    $file_extention = $extension_hash[$info[2]];
    $file_extention = $file_extention ? $file_extention : 'noext.jpg';

    $index = md5(file_get_contents($tempFile));
    $dir1 = substr($index, 0, 2);
    $dir2 = substr($index, 2, 2);
    $targetPath = ROOT_PATH.$pathprefix.$dir1.'/'.$dir2.'/';
    createFolder($targetPath);
    $targetFile = $targetPath.substr($index, 4).'.'.$file_extention;
    return str_replace(ROOT_PATH, '', $targetFile);
}

function is_ajax(){
	    return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])=='xmlhttprequest') ? 1 : 0;
}

function isPc(){
	return !isMobile();
}
// 是否是手机版的 @ 2013-08-07 12:03:23
function isMobile(){
  $mobile = 0;
  $ua = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : "";
  $regex = "/.*(mobile|nokia|iphone|ipod|andriod|bada|motorola|^mot\-|softbank|foma|docomo|kddi|ip\.browser|up\.link|";
  $regex.="htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|ppc|";
  $regex.="blackberry|alcate|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";
  $regex.="symbian|smartphone|midp|wap|phone|windows\sphone|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";
  $regex.="jig browser|hiptop|uc|^benq|haier|^lct|opera\s*mobi|opera\s*mini|2.0 MMP|240x320|400X240|Cellphone|WinWAP).*/i";
  if ( preg_match($regex, $ua)){
	  $mobile = 1;
  }
  return $mobile;
}

// 输出结果
function _P($data){
	if( 1 || stripos(ua(), 'chrome')!==FALSE ){
		$output = $data;
	}
	else{
		headerJson();
		$output = enjson($data);
	}
	deb($output);
}

// 记录事件 @ 2013-12-24 23:20:09
function logEvent($type, $arr){
	global $db;
	$json = enjson($arr);
	$db->query("INSERT INTO record VALUES (NULL, '".ss('cid')."', '$type', '$json', '".dt()."');");
}

// 返回当前访问的url @ 2013-11-12 00:01:07
function url( $t=NULL ){
	$s = 'http'.($_SERVER['SERVER_PORT']=='443'?'s':'').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	if( $t=='-s' ){
		$s = str_replace(ROOT_URL, '', $s);
	}
	return $s;
}
// 返回当前访问的前一站 @ 2013-11-12 00:13:29
function referer($cmd=NULL){
	$s = $_SERVER['HTTP_REFERER'];
	if( $cmd=='-s' ){
		$s = str_replace(ROOT_URL, '', $s);
	}
	return $s;
}

// 发送一些头 @ 2013-08-20 16:36:29
function headerJson(){
	header('Content-type:text/json');
}
function headerJavascript(){
	header('Content-type:text/javascript');
}
function header200(){
	header('HTTP/1.1 200 OK');
}
function header301(){
	header('HTTP/1.1 301 Moved Permanently');
}
function header403(){
	header('HTTP/1.1 403 Forbidden');
}
function header404(){
	header('HTTP/1.1 404 Not Found');
}

// 循环创建文件夹 @ 2013-08-10 20:32:13
function createFolder($in_folder_path){
    if(!file_exists($in_folder_path)){
        createFolder(dirname($in_folder_path));
        mkdir($in_folder_path);
    }
	return $in_folder_path;
}

// 返回数组样子
function getObj( $o, $rec='' ){
	if( $rec=='' ) $rec = 0;
	$rec++;
	$i = 0;
	if($rec>1) $str = "(\n";
	$end_space = '';
	if($rec>1) $space = $use_space = "　　";
	while( $i++<$rec-2 ){
		$end_space .= $space;
		$use_space .= $space;
	}
	$j = 0;
	foreach($o as $k => $v){
		$j++;
		if( is_array($v) || is_object($v) ){
			$v = getObj($v, $rec);
		}
		if( $j==$rec ){
			$i = 0;
		}
		else if( $rec==1 ){
			$use_space = $space;
		}
		$str .= $use_space.'['.$k.'] => '.$v."\n";
	};
	return $str.$end_space.( $rec>1 ? ')' : '' );
}

// page time, 2013-05-15 02:34:53增加参数beg_time
function runTime( $beg_time='' ){
	global $start_time;
	if( $beg_time!='' ) $start_time = $beg_time;
	if( empty($start_time) ) $ret = '起始时间未定义';
	else $ret = number_format(microtime(TRUE)-$start_time, 3, '.', '');
	return $ret;
}

function ua(){
	return addslashes($_SERVER['HTTP_USER_AGENT']);
}
function ip( $t=NULL, $ip_a=NULL ){
	$ip = getenv('HTTP_X_REAL_IP');
	if( $ip=='' ) $ip = $_SERVER['REMOTE_ADDR'];
	// 规范化采用-指令形式 @ 2013-08-17 03:26:35
	if( $t=='prefix' || $t=='-prefix' ){
		$ip = $ip_a ? $ip_a : $ip;
		$tmp = explode('.', $ip);
		$ip = $tmp[0].'_'.$tmp[1];
	}
	return $ip;
}
function dt( $t=NULL ){
	// 转换成字符串形式的
	$t .= '';
	// 添加返回日期的 2013-11-13 13:35:00
	if( strlen($t)==19 ){
		$t = strtotime($t);
		$s = date('Y/m/d H:i', $t);
	}
	elseif( $t!='' ){
		$s = date('Y-m-d H:i:s', $t);
	}
	else{
		$s = date('Y-m-d H:i:s');
	}
	return $s;
}
// 返回毫秒 @ 2013-11-13 13:54:23
function mt( $cmd=NULL ){
	$mt = number_format(microtime(1), 2, '.', '');
	if( $cmd==='-d2' ){$mt = substr($mt, 10, 3);}
	return $mt;
}
// Chinese json
function enjson($code){
	/* 加上反而容易出问题 @ 2013-08-12 02:18:54
	$find = array('"', "\\", "\r\n", "\n");
	$repl = array('\"','\\\\','\n','\r\n');
	$code = str_replace($find, $repl, $code);
	*/
	$code = json_encode(urlencodeAry($code));
	return urldecode($code);
}
function urlencodeAry($data){
	if(is_array($data)){
		foreach($data as $meta=>$val){
			$data[$meta] = urlencodeAry($val);
		}
		return $data;
	}
	else{
		return urlencode($data);
	}
}
// Json decode
function dejson($str){
	// 前后\n在双引号和单引号中有差异，需要注意 @ 2013-08-12 00:58:47
	$str = str_replace("\\", '\\\\', $str);
	$str = str_replace("\r\n", '\n', $str);
	$str = str_replace("\n", '\r\n', $str);
	$str = str_replace("\t", '　　', $str);
	$res = json_decode($str, 1);
	if( !$res ){
		// 处理内容中带有双引号"的问题 @ 2013-08-12 01:38:46
		$find = array('{"', '":"', '","', '"}');
		$repl = array('|-_@json_type_01#_-||','|-_@json_type_02#_-||','|-_@json_type_03#_-||','|-_@json_type_04#_-||');
		$str = str_replace($find, $repl, $str);
		$str = str_replace('"', '\"', $str);
		$str = str_replace($repl, $find, $str);
		$res = json_decode($str, 1);
	}
	return $res;
}
// debug need
function deb( $o ){
	if( is_array($o) || is_object($o) ){
		echo '<pre>';
		print_r($o);
		die('</pre>');
	}
	else{
		echo $o;
		die();
	}
}
