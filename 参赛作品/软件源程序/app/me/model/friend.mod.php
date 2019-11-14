<?php
if( !defined('DCCMS') ) die();

class Mdl_Me_Friend{
	// 表名
	const TABLE_NAME = 'user_friend';
	// 表字段
	static $TABLE_FIELDS = array('id', 'user_id_from', 'user_id_to', 'type', 'content', 'read', 'time_insert');

	static function addFriend($user_id_from, $user_id_to){
		global $db;
		$user_id_from = $user_id_from+0;
		$user_id_to   = $user_id_to+0;
		if( $user_id_from==$user_id_to ){
			return '不能添加自己为好友';
		}
		// 已经是好友
		if( $db->val("SELECT id FROM ".self::TABLE_NAME." 
			WHERE user_id_from={$user_id_from} 
			AND user_id_to={$user_id_to} 
			AND type='is_friend'") ){
			return '已经是好友了';
		}
		// 已发送好友请求
		elseif( $db->val("SELECT id FROM ".self::TABLE_NAME." 
			WHERE user_id_from={$user_id_from} 
			AND user_id_to={$user_id_to} 
			AND type='add_friend'") ){
			return  '已发送过好友请求';
		}
		// 对方发送过好友请求，表示同意
		elseif( $db->val("SELECT id FROM ".self::TABLE_NAME." 
			WHERE user_id_from={$user_id_to} 
			AND user_id_to={$user_id_from} 
			AND type='add_friend'") ){
			// 加对方
			$db->query("INSERT INTO ".self::TABLE_NAME." 
				(`id`, `user_id_from`, `user_id_to`, `type`, `content`, `time_insert`) 
				VALUES 
				(NULL, '{$user_id_from}', '{$user_id_to}', 'is_friend', NULL, '".get_mt()."')");
			// 对方加我
			$db->query("INSERT INTO ".self::TABLE_NAME." 
				(`id`, `user_id_from`, `user_id_to`, `type`, `content`, `time_insert`) 
				VALUES 
				(NULL, '{$user_id_to}', '{$user_id_from}', 'is_friend', NULL, '".get_mt()."')");
			return '添加好友成功';
		}
		else{
			// 发送加好友请求
			$db->query("INSERT INTO ".self::TABLE_NAME." 
				(`id`, `user_id_from`, `user_id_to`, `type`, `content`, `time_insert`) 
				VALUES 
				(NULL, '{$user_id_from}', '{$user_id_to}', 'add_friend', NULL, '".get_mt()."')");
			return '发送好友请求成功';
		}
	}

	static function sendMessage($user_id_from, $user_id_to, $content){
		global $db;
		$user_id_from = $user_id_from+0;
		$user_id_to   = $user_id_to+0;
		$res = $db->query("INSERT INTO ".self::TABLE_NAME." 
			(`id`, `user_id_from`, `user_id_to`, `type`, `content`, `time_insert`) 
			VALUES 
			(NULL, '{$user_id_from}', '{$user_id_to}', 'send_message', '$content', '".get_mt()."')");
		return $res ? '消息发送成功！' : '消息发送失败，请检查网络';
	}

	static function getFriendList($user_id){
		global $db;
		$user_id = $user_id+0;
		$final = $db->jo("SELECT user_id_to FROM ".self::TABLE_NAME." WHERE user_id_from={$user_id} AND type='is_friend' order by time_insert DESC");
		return $final;
	}

	static function getUserData($user_id){
		global $db;
		$user_id = $user_id+0;
		return $db->row("SELECT * FROM user WHERE id={$user_id}");
	}

	static function getPollData($user_id){
		global $db;
		$user_id = $user_id+0;
		$type = array(
			'send_message',
			'add_friend',
			'is_friend',
		);
		$final = array();
		$res = $db->query("SELECT id, user_id_from, type, content, time_insert FROM ".self::TABLE_NAME." 
			WHERE user_id_to={$user_id} AND type in ('".join("','", $type)."') AND `read`=0 ORDER BY time_insert ASC");
		while( $r=$db->r($res) ){
			$db->query("UPDATE ".self::TABLE_NAME." SET `read`=1 WHERE id=".$r['id']);
			$ret_data = array();
			$ret_data['type'] = $r['type'];
			$ret_data['user_id_from'] = $r['user_id_from'];
			// @todo，把nickname存为一张map，避免多次查询
			$nickname = $db->val("SELECT nickname FROM user WHERE id=".$r['user_id_from']);

			if( $r['type']=='send_message' ){
				$ret_data['msg'] = str_replace('"', '\"', $r['content']);
				$ret_data['chat_time'] = $nickname.'('.$r['user_id_from'].') '.date('m-d H:i:s', $r['time_insert']);
			}
			elseif( $r['type']=='add_friend' ){
				$ret_data['from_user_nickname'] = $nickname.'('.$r['user_id_from'].')';
				$ret_data['date_time'] = date('Y-m-d H:i', $r['time_insert']);
			}
			elseif( $r['type']=='is_friend' ){
				$ret_data['from_user_nickname'] = $nickname.'('.$r['user_id_from'].')';
				$ret_data['date_time'] = date('Y-m-d H:i', $r['time_insert']);
			}
			$final[] = $ret_data;
		}
		return $final;
	}

	static function getContactList($user_id){
		global $db;
		$user_id = $user_id+0;
		$friend_count = 0;
		// $friend_list = self::getFriendList($user_id);
		$sql = "SELECT user_id_to FROM ".self::TABLE_NAME." WHERE user_id_from={$user_id} AND type='is_friend' order by time_insert DESC";
		$contact_res = $db->query($sql);
		$final = '';
		while( $contact_row=$db->r($contact_res) ){
			$contact_user_id = $contact_row['user_id_to'];
			$res = $db->query("SELECT u.id as user_id, u.nickname, u.avatar 
				FROM user u 
				WHERE u.id={$contact_user_id}");
			while( $r=$db->r($res) ){
				$friend_count++;
				$time_fr = '';
				$position = $db->val("SELECT position FROM image WHERE user_id={$r['user_id']} AND position<>'' ORDER BY time_update DESC");
				$r['time_insert'] = $db->val("SELECT time_insert FROM ".self::TABLE_NAME." WHERE 
					( user_id_from={$user_id} AND user_id_to={$r['user_id']} )
					OR
					( user_id_from={$r['user_id']} AND user_id_to={$user_id} )
					ORDER BY time_insert DESC");
				if( $r['time_insert']>strtotime('today') ){
					$time_fr = date('H:i', $r['time_insert']);
				}
				else{
					$time_fr = date('m-d', $r['time_insert']);
				}
				$final .= '
				<li data-user_id="'.$r['user_id'].'">
					<a href="javascript:chat_with_friend('.$r['user_id'].');">
						<img src="'.transfer_img_to_static($r['avatar']).'" alt="'.$r['nickname'].'">
						<h3>'.$r['nickname'].'</h3>
						<p><i class="icon icon-location"></i> '.($position ? $position : '暂无').'<span class="fr">'.$time_fr.'</span></p>
					</a>
				</li>';
			}
		}
		if( empty($final) ){
			$final = '<li class="no_friend">还没有好友，请 <button class="btn btn-xs btn-primary btn_addfriend">添加</button></li>';
		}
		return $final;
	}
}

//end of script
