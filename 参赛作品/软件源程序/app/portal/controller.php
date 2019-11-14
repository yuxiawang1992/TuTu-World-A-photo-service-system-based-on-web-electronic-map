<?php
if( !defined('DCCMS') ) die();

require_once('helper.php');

class CTL_Portal extends MDL_Global{

	public $H;
	public $db;
	public $module_name 		= '';
	public $module_name_ch 		= '图图世界';
	public $view_static 		= '';
	public $part_url 			= '';
	public $path 				= '';
	public $nav_list 			= array();
	public $act_name 			= '';
	public $act_name_ch 		= '';

	function __construct(){
		global $db, $mod, $act;
		require_once ROOT_PATH.'app/me/model/image.mod.php';

		$this->module_name 	= strtr($mod, '-', '_');
		$this->act_name 	= strtr($act, '-', '_');
		$this->H 			= new HLP_Portal($this);
		$this->db 			= $db;
		$this->path 		= ROOT_PATH.'app/'.$this->module_name.'/';
		$this->view_static 	= 'app/'.$this->module_name.'/view/static/';
		$this->part_url 	= ROOT_URL.$this->module_name.'/'.$this->act_name;
		$this->nav_list 	= array(
			'index' 		=> '首页',
			'share' 		=> '照片共享',
			'path' 			=> '路径查询',
			'retrieval' 	=> '信息检索',
			'map' 			=> '地图管理',
		);
		$this->act_name_ch 	= $this->nav_list[$this->act_name];
	}

	function indexA(){
		trd(ROOT_URL.'portal/share');
	}

	function shareA(){
		$this->G_loadModel();
		// 取公开图片
		if( isset($_GET['method'])
		 && $_GET['method']=='get_public_images' ){
		 	$tag_id = NULL;
		 	if( $_GET['sub_method']=='filter' && !empty($_GET['tag']) ){
		 		$tag_id = $_GET['tag']+0;
		 		// deb($tag_id);
		 	}
		 	$img_data = Mdl_Portal_Share::getPublicImages($tag_id);
		 	// deb($img_data);
		 	$final = Mdl_Portal_Share::generateJsData($img_data);
		 	headerJavascript();
		 	deb($final);
		}
		$tags_map = Mdl_Me_Image::getTagsMap();
		include 'view/'.$this->act_name.'.php';
	}

	function retrievalA(){
		include 'view/'.$this->act_name.'.php';
	}

	function mapA(){
		include 'view/'.$this->act_name.'.php';
	}

	function pathA(){
		include 'view/'.$this->act_name.'.php';
	}


}
// end of script
