<?php
require_once('model.php');
require_once('helper.php');

class CTL_Panel extends MDL_Global{

	public $M;
	public $H;
	public $db;
	public $module_name;
	public $module_name_ch = '控制面板';
	public $view_static = '';
	public $nav_list = array();
	public $act_name = '';
	public $act_name_ch = '';

	function __construct(){
		global $db, $mod, $act;
		$this->no_login 	= array(
			'login',
			'h5_list',
			'h5_topic',
		);
		if(!in_array(strtr($act, '-', '_'), $this->no_login)
			&& !$_SESSION['panel']['id'] ){
			trd(ROOT_URL.'panel/login');
		}
		$this->module_name 	= strtr($mod, '-', '_');
		$this->M 			= new MDL_Panel($this);
		$this->H 			= new HLP_Panel($this);
		$this->db 			= $db;
		$this->view_static 	= ROOT_URL.'app/'.$this->module_name
							.'/view/static/';
		$this->nav_list 	= array(
			'login' 		=> '登录',
			'index' 		=> '首页',
			'user_message' 	=> '留言',
			'configuration' => '配置',
		);
		$this->act_name 	= strtr($act, '-', '_');
		$this->act_name_ch 	= $this->nav_list[$this->act_name];

	}

	function loginA(){
		$_SESSION['panel']['id'] && trd('您已登录！', ROOT_URL.'panel/index');
		if( $_POST['username'] ){
			extract(getSafeInput($_POST));
			$passwd_md5 = md5($passwd);
			$row = $this->db->row("SELECT * FROM admin
				WHERE username='$username' AND passwd='$passwd_md5'");
			if( is_array($row) ){
				$_SESSION['panel'] = $row;
				trd('登录成功~', ROOT_URL.'panel/index');
			}
			else{
				// 登录失败
				// $login_msg = '登录失败，用户名或密码错误。';
				setSystemMessage('登录失败，用户名或密码错误。');
			}
		}
		include 'view/'.$this->act_name.'.php';
	}

	function indexA(){
		include 'view/'.$this->act_name.'.php';
	}


	function configurationA(){
		if( $_POST['method'] ){
			extract(getSafeInput($_POST));
			if( $method=='edit_config_value' && $meta ){
				$res = $this->db->query("UPDATE config
					SET value='$value'
					WHERE `meta`='$meta'
					LIMIT 1");
				echo $res ? 'ok' : '数据库操作失败！';
			}
			die();
		}
		include 'view/'.$this->act_name.'_list.php';

	}

	/**
	 * 监听url：panel/ajax，负责ajax请求的处理
	 */
	function ajaxA(){
		// 参数过滤、判断参数是否合法，保证安全
		$get_data = getSafeInput($_GET);
		if( !isset($get_data['method']) || strlen($get_data['method'])>32 ){
			deb('参数不正确');
		}

		switch ($get_data['method']) {
			// 抓取商品详情
			case 'curl_item_detail':
				// 调用当前模型的Helper助手类处理的方法
				$arr = $this->H->curlItemDetail($get_data['url']);
				// 将返回的结果以json数据发回请求者
				headerJson();
				echo enjson($arr);
				break;

			default:
				# code...
				break;
		}
	}

	function user_messageA(){
		$BASE_URL = ROOT_URL.'panel/user-message';
		$PARTNAME = '留言管理';
		if( isset($_GET['method'])
		 && $_GET['method']=='delete'
		 && !empty($_GET['id']) ){
		 	$id = intval($_GET['id'])+0;
			$res = $this->M->deleteUserMessage($id);
			trd($res?'删除成功！':'删除失败', $BASE_URL);
		}
		include 'view/'.$this->act_name.'.php';
	}

	function logoutA(){
		unset($_SESSION['panel']);
		trd(ROOT_URL.'panel/login');
	}


}
