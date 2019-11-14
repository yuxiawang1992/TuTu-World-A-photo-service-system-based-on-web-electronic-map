<?php
if( !defined('DCCMS') ) die();

class Mdl_Me_Album{
	// 表名
	const TABLE_NAME = 'album';
	// 表字段
	static $TABLE_FIELDS = array('id', 'user_id', 'name', 'name_en', 'description', 'avatar', 'status', 'time_insert', 'time_update');
	// 不更新的字段
	static $FIELDS_NO_UPDATES = array('id', 'user_id', 'time_insert');




	static function getList($uid, $page=0, $pagesize=10){
		global $db;
		$uid = $uid+0;
		return $db->selectAll("SELECT * FROM ".self::TABLE_NAME." 
			WHERE user_id={$uid}
			ORDER BY time_update DESC
			LIMIT {$page}, {$pagesize}");
	}

	static function getOne($uid, $id){
		global $db;
		$uid = $uid+0;
		return $db->row("SELECT * FROM ".self::TABLE_NAME." 
			WHERE user_id={$uid} AND id={$id}");
	}

	static function getCount($uid){
		global $db;
		$uid = $uid+0;
		return $db->val("SELECT count(*) FROM ".self::TABLE_NAME." 
			WHERE user_id={$uid}");
	}

	static function update($uid, $data_arr, $id){
		global $db;
		$user_id = $uid+0;
		$update_id = $id+0;
		$data_arr['avatar'] = self::_uploadImage();
		$data_arr['time_update'] = time();
		$set_sql = '';
		foreach( $data_arr as $k=>$v ){
			if( !in_array($k, self::$TABLE_FIELDS) || in_array($k, self::$FIELDS_NO_UPDATES) ){ continue; }
			$set_sql .= $k."='{$v}', ";
		}
		if( $set_sql=='' ){
			return FALSE;
		}
		else{
			$set_sql = substr($set_sql, 0, -2);
		}
		return $db->query("UPDATE ".self::TABLE_NAME."
			SET {$set_sql} WHERE id={$update_id} AND user_id={$user_id} LIMIT 1");
	}

	static function insert($uid, $data_arr){
		global $db;
		$user_id = $uid+0;
		extract($data_arr);
		$avatar = self::_uploadImage();
		$name_en = '';
		$time_insert = $time_update = time();
		return $db->query("INSERT INTO ".self::TABLE_NAME."
		 VALUES (NULL, '$user_id', '$name', '$name_en', '$description', 
		 '$avatar', '$status', '$time_insert', '$time_update')");
	}

	static function delete($uid, $id){
		global $db;
		$user_id = $uid+0;
		$update_id = $id+0;
		return $db->query("DELETE FROM ".self::TABLE_NAME." WHERE id={$update_id} AND user_id={$user_id} LIMIT 1");
	}


	private static function _uploadImage(){
		// 上传图片
		if( !empty($_FILES['avatar']['name']) ){
			$res = uploadPic($_FILES['avatar']);
			if( !substr_count($res, '/') ){
				trd('上传图片出错：'.$res, ROOT_URL.mod().'/'.act());
			}
			else{
				$avatar = $res;
			}
		}
		elseif( !empty($_POST['avatar_js']) ){
			$avatar = downPic($_POST['avatar_js']);
		}
		return $avatar;
	}

}

//end of script
