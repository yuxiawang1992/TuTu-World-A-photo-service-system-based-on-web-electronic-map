<?php

// mysql database config
$dbs['type'] = 'mysql';
$dbs['server'] = 'rds2014061.mysql.rds.aliyuncs.com';
$dbs['username'] = 'product';
$dbs['passwd'] = 'rds_productx';
$dbs['dbname'] = 'product_tutu';
if( IS_LOCALHOST ){
	$dbs['server'] = 'localhost';
	$dbs['username'] = 'root';
	$dbs['passwd'] = 'GeeDream2014';
	$dbs['dbname'] = 'product_tutu';
}
$dbs['prefix'] = '';
$dbs['persistent'] = 0;

// website config
$cfg_ = array(
	'unsafe_varible' => 'time,sConf,uConf,cfg_,db,tpl,mod,act', 												// 不安全的变量
	'dcc_crypt_maxlen' => '2500', 																				// 加密字符串最大长度
	'dcc_crypt_salt' => CRYPT_SALT, 																			// 加密混合剂
	'cookie_path' => '/', 																						// cookie保存路径
	'cookie_prefix' => 't_', 																					// cookie前缀
	'create_date' => '2014-07-17'																				// 系统创建时间
);

