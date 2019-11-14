<?php
if( !defined('DCCMS') ) die();

class Mdl_Portal_Share{
	
	static function getPublicImages( $tag_id=NULL ){
		global $db;
		
		$tags_sql = '';
		if( is_int($tag_id) ){
			$tags_map = Mdl_Me_Image::getTagsMap();
			if( in_array($tag_id, array_keys($tags_map), TRUE) ){
				$tags_sql = " AND FIND_IN_SET({$tag_id}, tag_ids)";
			}
		}

		$album_ids = '0,'.$db->jo("SELECT id FROM album WHERE status>0");
		return $db->selectAll("SELECT * FROM image WHERE album_id in ({$album_ids})$tags_sql ORDER BY time_update DESC");
	}

	static function generateJsData($img_data){
		global $db;
		$tags_map = Mdl_Me_Image::getTagsMap();
		$final = 'D = new Array();';
		// deb($img_data);
	 	foreach( $img_data as $k=>$v ){
	 		$user_data = $db->row("SELECT nickname, avatar FROM user where id='{$v['user_id']}'");
	 		list($username, $user_avatar) = array($user_data['nickname'], get_cdn_url($user_data['avatar']));
	 		$lon = !empty($v['longitude']) ? $v['longitude'] : 0;
	 		$lat = !empty($v['latitude'])  ? $v['latitude']  : 0;
	 		$tag_names = '';
	 		foreach( explode(',', $v['tag_ids']) as $vv ){
	 			if( !empty($tags_map[$vv]) ){
	 				$tag_names .= ', '.$tags_map[$vv];
	 			}
	 		}
	 		$tag_names = substr($tag_names, 2);
	 		$final .= "\n".'D['.$k.'] = {id:"'.$v['id'].'", lonlat:new SuperMap.LonLat('.$lon.', '.$lat.'),img:"'.get_cdn_url($v['avatar']).'",title:"'.$v['name'].'",description:"'.$v['description'].'",time:"'.dt($v['time_update']).'",username:"'.$username.'",position:"'.$v['position'].'",user_avatar:"'.$user_avatar.'", tag_ids:"'.$v['tag_ids'].'", tag_names:"'.$tag_names.'"};';
	 	}
	 	return $final;
	}

}

//end of script
