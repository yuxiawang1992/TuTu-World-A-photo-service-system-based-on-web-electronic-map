<?php
if( !defined('DCCMS') ) die();

require_once('helper.php');

class CTL_Me extends MDL_Global{

	public $H;
	public $db;
	public $module_name;
	public $module_name_ch = '个人中心';
	public $view_static = '';
	public $path = '';
	public $nav_list = array();
	public $act_name = '';
	public $act_name_ch = '';
	public $uid = 0;

	function __construct(){
		global $db, $mod, $act;
		$this->no_login 	= array(
			'login',
		);
		$this->uid = $_SESSION['me']['id'];
		if(!in_array(strtr($act, '-', '_'), $this->no_login)
			&& !$this->uid ){
			// ajax的页面不能太丑
			if( is_ajax() ){
				deb('登录会话已失效，请重新登录');
			}
			trd(ROOT_URL.'me/login');
		}
		$this->module_name 	= strtr($mod, '-', '_');
		$this->H 			= new HLP_Me($this);
		$this->db 			= $db;
		$this->path 		= ROOT_PATH.'app/'.$this->module_name.'/';
		$this->view_static 	= 'app/'.$this->module_name.'/view/static/';
		$this->nav_list 	= array(
			'login' 		=> '登录',
			'index' 		=> '首页',
			'album' 		=> '我的相册',
			'image' 		=> '我的照片',
			'friend' 		=> '我的好友',
			'life' 			=> '快捷生活',
		);
		$this->act_name 	= strtr($act, '-', '_');
		$this->act_name_ch 	= $this->nav_list[$this->act_name];

	}

	function loginA(){
		$this->uid && trd('您已登录！', ROOT_URL.'me');
		if( $_POST['username'] ){
			extract(getSafeInput($_POST));
			$passwd_md5 = md5($passwd);
			$row = $this->db->row("SELECT * FROM user
				WHERE username='$username' AND passwd='$passwd_md5'");
			if( is_array($row) ){
				$row_last = array(
					'last_login_ip' => $row['login_ip'],
					'last_time_update' => $row['time_update'],
				);
				$this->db->query("UPDATE user SET time_update='".time()."', login_ip='".ip()."' WHERE id={$row['id']}");
				$row = $this->db->row("SELECT * FROM user
					WHERE username='$username' AND passwd='$passwd_md5'");
				$row = array_merge((array)$row, (array)$row_last);
				$_SESSION['me'] = $row;
				trd('登录成功，欢迎。', ROOT_URL.'me');
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

	/**
	 * 监听url：me/ajax，负责ajax请求的处理
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

	function albumA(){
		$uid = $this->uid;
		$this->G_loadModel();
		$BASE_URL = ROOT_URL.$this->module_name.'/'.$this->act_name;
		$PARTNAME = $this->act_name_ch;
		// 删除
		if( isset($_GET['method'])
		 && $_GET['method']=='delete'
		 && !empty($_GET['id']) ){
			$handler_name = '删除';
		 	$id = intval($_GET['id'])+0;
			$res = MDL_Me_Album::delete($uid, $id);
			trd($res?$handler_name.'成功！':$handler_name.'失败', $BASE_URL);
		}
		// 添加、编辑的界面
		elseif( isset($_GET['method'])
		 && ($_GET['method']=='add' || $_GET['method']=='edit') ){
		 	if( !empty($_GET['id']) ){
		 		$id = intval($_GET['id'])+0;
				$handler_name = '编辑';
				$edit_data = MDL_Me_Album::getOne($uid, $id);
				// deb($edit_data);
			}
			else{
				$handler_name = '添加';
			}
			include 'view/'.$this->act_name.'_update.php';
		}
		// 添加、编辑的事件
		elseif( isset($_POST['name']) ){
		 	$post_data = getSafeInput($_POST);
		 	// deb($post_data);
		 	if( !empty($_POST['id']) ){
		 		$id = intval($_POST['id'])+0;
				$handler_name = '编辑';
		 		$res = MDL_Me_Album::update($uid, $post_data, $id);
		 	}
			else{
				$handler_name = '添加';
				$res = MDL_Me_Album::insert($uid, $post_data);
			}
			trd($handler_name.( $res ? '成功！' : '失败，请重试' ), $BASE_URL);
		}
		// 列表
		else{
			include 'view/'.$this->act_name.'_list.php';
		}
	}




	function imageA(){
		$uid = $this->uid;
		$this->G_loadModel();
		$BASE_URL = ROOT_URL.$this->module_name.'/'.$this->act_name;
		$PARTNAME = $this->act_name_ch;
		// 生成tag_arr哈希表
		$tags_html = MDL_Me_Image::getTagsHtml();
		// 删除
		if( isset($_GET['method'])
		 && $_GET['method']=='delete'
		 && !empty($_GET['id']) ){
			$handler_name = '删除';
		 	$id = intval($_GET['id'])+0;
			$res = MDL_Me_Image::delete($uid, $id);
			trd($res?$handler_name.'成功！':$handler_name.'失败', $BASE_URL);
		}
		// ajax
		elseif( isset($_GET['method'])
		 && $_GET['method']=='put_image_lnglat'
		 && !empty($_GET['id']) ){
			$handler_name = '更新经纬度';
		 	$id = intval($_GET['id'])+0;
		 	$lnglat = explode(',', $_GET['lnglat']);
		 	$post_data = array(
		 		'longitude' => $lnglat[0],
		 		'latitude' => $lnglat[1],
		 	);
			$res = MDL_Me_Image::update($uid, $post_data, $id);
			echo $handler_name.( $res ? '成功！' : '失败，请重试' );
		}
		// 添加、编辑的界面
		elseif( isset($_GET['method'])
		 && ($_GET['method']=='add' || $_GET['method']=='edit') ){
		 	if( !empty($_GET['id']) ){
		 		$id = intval($_GET['id'])+0;
				$handler_name = '编辑';
				$edit_data = MDL_Me_Image::getOne($uid, $id);
				// deb($edit_data);
			}
			else{
				$handler_name = '添加';
			}
			$edit_data['tags_html'] = $tags_html;
			include 'view/'.$this->act_name.'_update.php';
		}
		// 添加、编辑的事件
		elseif( isset($_POST['name']) ){
		 	$post_data = getSafeInput($_POST);
		 	// deb($post_data);
		 	if( !empty($_POST['id']) ){
		 		$id = intval($_POST['id'])+0;
				$handler_name = '编辑';
		 		$res = MDL_Me_Image::update($uid, $post_data, $id);
		 	}
			else{
				$handler_name = '添加';
				$res = MDL_Me_Image::insert($uid, $post_data);
			}
			trd($handler_name.( $res ? '成功！' : '失败，请重试' ), $BASE_URL);
		}
		// 列表
		else{
			include 'view/'.$this->act_name.'_list.php';
		}
	}

	function lifeA(){
		$uid = $this->uid;
		$this->G_loadModel();
		include 'view/'.$this->act_name.'.php';
	}


	function friendA(){
		$uid = $this->uid;
		$this->G_loadModel();
		// 聊天
		if( !empty($_POST['method']) ){
			$this->_friendChat();
			exit();
		}
		// 轮询
		elseif( !empty($_GET['method']) && $_GET['method']=='poll' ){
			$this->_friendPoll();
			exit();
		}
		// 刷新好友列表
		elseif( !empty($_GET['method']) && $_GET['method']=='fresh_contact_list' ){
			echo Mdl_Me_Friend::getContactList($uid);
			exit();
		}
		// 添加好友等
		elseif( !empty($_GET['method']) ){
			$this->_friendAdd();
			exit();
		}
		include 'view/'.$this->act_name.'.php';
	}


	/**
	 * 控制聊天
	 */
	private function _friendChat(){
		if( empty($_POST['method']) ){
			_P('非法请求');
		}
		$uid = $this->uid;
		$this->G_loadModel();
		// 发消息
		if( $_POST['method']=='send_message' ){
			$post_data = getSafeInput($_POST);
			if( empty($post_data['user_id_to']) ){
				_P('没有找到收件人');
			}
			if( empty($post_data['msg']) ){
				_P('消息不能为空');
			}
			$msg = Mdl_Me_Friend::sendMessage($uid, $post_data['user_id_to'], $post_data['msg']);
			$arr = array(
				'msg'		=> $msg,
				'chat_time'	=> $_SESSION['me']['nickname'].'('.$_SESSION['me']['id'].') '.date('m-d H:i:s'),
			);
			headerJson();
			echo enjson($arr);
		}
	}

	/**
	 * 控制加好友
	 */
	private function _friendAdd(){
		if( empty($_GET['method']) ){
			_P('非法请求');
		}
		$uid = $this->uid;
		$this->G_loadModel();

		// 加好友
		if( $_GET['method']=='add_friend' ){
			echo Mdl_Me_Friend::addFriend($uid, $_GET['uin']+0);
		}
		// 查好友数据
		elseif( $_GET['method']=='get_user_data' ){
			$user_data = Mdl_Me_Friend::getUserData($_GET['uin']+0);
			echo $user_data['nickname'];
		}
	}

	/**
	 * 控制消息轮询
	 */
	private function _friendPoll(){
		set_time_limit(60);
		if( $_GET['method']!='poll' ){
			_P('非法请求');
		}
		$uid = $this->uid;
		$this->G_loadModel();

		$timeout = 29;
		// session写会阻塞
		session_write_close();
		for( $i=0; $i<$timeout; $i++ ){
			$poll_data = Mdl_Me_Friend::getPollData($uid);
			if( $poll_data ){
				headerJson();
				echo enjson($poll_data);
				flush();
				exit();
			}
			sleep(1);
			//usleep(1000000);
		}
		flush();
		exit();

	}

	function logoutA(){
		unset($_SESSION['me']);
		trd(ROOT_URL.'me/login');
	}



}
// end of script
