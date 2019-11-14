<?
define('DCCMS', 1);
define('ROOT_PATH', str_replace('\\', '/', dirname($_SERVER['SCRIPT_FILENAME'])).'/');
define('ROOT_URL', 'http'.($_SERVER['SERVER_PORT']=='443'?'s':'').'://'.$_SERVER['HTTP_HOST'].str_replace($_SERVER['DOCUMENT_ROOT'], '', ROOT_PATH));
define('CONFIGED_URL', 'http://tutu.geeyan.com/');
define('STATIC_IMAGES_URL', 'http://static.geeyan.com/images/project/tutu/');

define('CRYPT_SALT', '@tvqwwtv.,by#');// 加密添加的混合串元,勿改动！

define('DEBUG', 1);
define('APP_VERSION', '0.3.153');

define('IS_LOCALHOST', substr_count(ROOT_URL, 'localhost')||substr_count(ROOT_URL, '127.0.0.1')?TRUE:FALSE);
