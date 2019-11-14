<?php
include('util/common.inc.php');


$mod = !empty($_GET['__mod']) ? $_GET['__mod'] : 'portal';
$act = !empty($_GET['__act']) ? $_GET['__act'] : 'index';


// 确保module和act的合法性，后续eval很危险
if( strlen($mod)>16
 || !preg_match('/^[a-z0-9\-]+$/', $mod) ){
	header404();
	_P('404 not found: 模型被系统拒绝');
}
if( strlen($act)>32
 || preg_match('/^[0-9]$/', $act)
 || !preg_match('/^[a-z0-9\-]+$/', $act) ){
	header404();
	_P('404 not found: 页面被系统拒绝');
}

// 解析GET，默认的$_GET已失效 @ 2014-04-21 22:36:22
// deb($_SERVER);
$uri = $_SERVER['REQUEST_URI'];
if( substr_count($uri, '?') ){
	$_GET_REWRITE = $_GET;
	parse_str(end(explode('?', $uri, 2)), $_GET);
	$_GET = array_merge((array)$_GET, (array)$_GET_REWRITE);
}
// deb($_GET);

// 判断控制者是否存在
$controller = ROOT_PATH.'app/'.strtr($mod, '-', '_').'/controller.php';
if( !file_exists($controller) ){
	_P("module not found");
}
// 引入全局模型
include (ROOT_PATH.'app/global/model.php');
// 引入控制者，其中__construct将会直接执行
include $controller;
if( !class_exists(CTL_.ucfirst(strtr($mod, '-', '_'))) ){
	_P("module not ready");
}



eval('$controller = new CTL_'.ucfirst(strtr($mod, '-', '_')).';');
$handler = array($controller, strtr($act, '-', '_').'A');
$params = array();

// deb($handler);
if ( is_callable($handler) ) {
	call_user_func_array($handler , $params);
}
else{
	_P("act error");
}



/*
eval('$controller = new CTL_'.ucfirst($mod).';
if( !method_exists($controller, '.str_replace('-', '_', $act).'A) ) _P("act error");
$controller->'.str_replace('-', '_', $act).'A();');
*/
