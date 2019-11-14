<?php
if( !defined('DCCMS') ) die();

class MDL_Global{
	
	private $view_path;
	
	function __construct(){
		// EXTEND 不算初始化
	}
	
	
	function getGlobalView($name){
		return $this->G_getView('global', $name);
	}
	
	function G_getView($module, $name){
		$view_path = ROOT_PATH.'app/'.$module.'/view/';
		$path = $view_path.$name.'.htm';
		if( file_exists($path) ){
			return file_get_contents($path);
		}
		else{
			return 'view '.$path.' not exists';
		}
	}
	
	function G_mt(){
		$time = microtime(1);
		return (number_format($time, 3, '.', ''));
	}


	function G_loadModel(){
		$fpath = ROOT_PATH.'app/'.mod().'/model/'.act().'.mod.php';
		if( file_exists($fpath) ){
			require_once $fpath;
		}
		else{
			_P('Global: model not found');
		}

	}
}
