<?php
if( !defined('DCCMS') ) die();

class Mdl_Me_Life{
	
	static function getImageCount($user_id, $status=array()){
		global $db;
		$user_id = $user_id+0;
		$status_default = array(0, 1, 2, 3);
		if( is_array($status) ){
			foreach( $status as $k=>$v ){
				if( !in_array($v, $status_default) ){
					unset($status[$k]);
				}
			}
		}
		if( empty($status) ){
			$status = $status_default;
		}
		$album_ids = $db->jo("SELECT id FROM album WHERE user_id={$user_id} AND status IN (".join(',', $status).")");
		$album_ids = empty($album_ids) ? 0 : $album_ids;
		return $db->val("SELECT count(*) FROM image WHERE user_id={$user_id} AND album_id IN ({$album_ids})");
	}


	static function getAlbumCount($user_id, $status=array()){
		global $db;
		$user_id = $user_id+0;
		$status_default = array(0, 1, 2, 3);
		if( is_array($status) ){
			foreach( $status as $k=>$v ){
				if( !in_array($v, $status_default) ){
					unset($status[$k]);
				}
			}
		}
		if( empty($status) ){
			$status = $status_default;
		}
		return $db->val("SELECT count(*) FROM album WHERE user_id={$user_id} AND status IN (".join(',', $status).")");
	}

	static function getFriendCount($user_id){
		global $db;
		$user_id = $user_id+0;
		return $db->val("SELECT count(*) FROM user_friend WHERE user_id_from={$user_id} AND type='is_friend'");
	}


	static function getChatSendCount($user_id){
		global $db;
		$user_id = $user_id+0;
		return $db->val("SELECT count(*) FROM user_friend WHERE user_id_from={$user_id} AND type='send_message'");
	}


	static function getChatRecieveCount($user_id){
		global $db;
		$user_id = $user_id+0;
		return $db->val("SELECT count(*) FROM user_friend WHERE user_id_to={$user_id} AND type='send_message'");
	}

	static function getImageDistinctPosition($user_id){
		global $db;
		$user_id = $user_id+0;
		$final = array();
		$final = $db->selectAll("SELECT DISTINCT (position), COUNT(*) as count
			FROM image
			WHERE user_id={$user_id}
			GROUP BY position
			ORDER BY count DESC");
		return $final;
	}

	static function getImageDistinctTag($user_id){
		global $db;
		$user_id = $user_id+0;
		$final = array();
		$res_tag = $db->query("SELECT id, name FROM tag");
		while( $r_tag=$db->r($res_tag) ){
			$final[] = array(
				'name' 	=> $r_tag['name'],
				'count'	=> $db->val("SELECT count(*) FROM image WHERE user_id={$user_id} AND FIND_IN_SET({$r_tag['id']}, tag_ids)"),
			);
		}
		return $final;
	}

	
}

//end of script
