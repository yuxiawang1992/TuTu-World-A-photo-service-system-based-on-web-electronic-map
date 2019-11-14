<?php
if( !defined('DCCMS') ) die();

class Mdl_Me_Image{
	// 表名
	const TABLE_NAME = 'image';
	// 表字段
	static $TABLE_FIELDS = array('id', 'user_id', 'album_id', 'name', 'description', 'avatar', 'longitude', 'latitude', 'position', 'tag_ids', 'time_insert', 'time_update');
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
		if( empty($data_arr['avatar']) ){
			unset($data_arr['avatar']);
		}
		$data_arr['time_update'] = time();
		// deb($data_arr);
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
		$final_sql = "UPDATE ".self::TABLE_NAME."
			SET {$set_sql} 
			WHERE id={$update_id} AND user_id={$user_id} 
			LIMIT 1";
		// deb($final_sql);
		return $db->query($final_sql);
	}

	static function insert($uid, $data_arr){
		global $db;
		$user_id = $uid+0;
		extract($data_arr);
		$avatar = self::_uploadImage();
		$name_en = '';
		$time_insert = $time_update = time();
		return $db->query("INSERT INTO ".self::TABLE_NAME." (`".join('`, `', self::$TABLE_FIELDS)."`)
		 VALUES (NULL, '$user_id', '$album_id', '$name', '$description', 
		 '$avatar', '', '', '$position', '$tag_ids', '$time_insert', '$time_update')");
	}

	static function delete($uid, $id){
		global $db;
		$user_id = $uid+0;
		$update_id = $id+0;
		return $db->query("DELETE FROM ".self::TABLE_NAME." WHERE id={$update_id} AND user_id={$user_id} LIMIT 1");
	}

	static function getAllAlbumList($uid){
		global $db;
		$user_id = $uid+0;
		return $db->selectAll("SELECT id, name, status FROM album 
			WHERE user_id={$user_id} 
			ORDER BY time_update DESC");
	}

	static function getAlbumMap($uid){
		global $db;
		$user_id = $uid+0;
		$final = array();
		$all = $db->selectAll("SELECT id, name FROM album 
			WHERE user_id={$user_id} 
			ORDER BY time_update DESC");
		foreach($all as $v){
			$final[$v['id']] = $v['name'];
		}
		return $final;
	}

	static function getTagsHtml(){
		global $db;
		$tag_arr = array();
		$res = $db->query('SELECT id, name FROM tag ORDER BY id');
		$tags_html = '';
		while( $r=$db->r($res) ){
			$tags_html .= '<span data-key="'.$r['id'].'">'.$r['name'].'</span>';
			if( $r['id']%4==0 ){
				$tags_html .= '<br />';
			}
		}
		return $tags_html;
	}


	static function getTagsMap(){
		global $db;
		$user_id = $uid+0;
		$final = array();
		$res = $db->query('SELECT id, name FROM tag ORDER BY id');
		while( $r=$db->r($res) ){
			$final[$r['id']] = $r['name'];
		}
		return $final;
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
