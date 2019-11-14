<?php
class HLP_Panel extends CTL_Panel{

	private $parent;
	function __construct($parent){
		$this->parent = $parent;
	}

	function getTagsByItemId($id){
		$db = $this->parent->db;
		$tag_arr = array();
		$res = $db->query('SELECT id, name FROM tag');
		while( $r=$db->r($res) ){
			$tag_arr[$r['id']] = $r['name'];
		}
		$tags = '';
		$res = $db->query("SELECT tag_id
			FROM item_tag_relate
			WHERE item_id=$id");
		while( $r=$db->r($res) ){
			$tags .= ','.$tag_arr[$r['tag_id']];
		}
		return substr($tags, 1);
	}

	function getTagIdsByItemId($id){
		$db = $this->parent->db;
		$tag_ids = '';
		$res = $db->query("SELECT tag_id
			FROM item_tag_relate
			WHERE item_id=$id
			ORDER BY tag_id");
		while( $r=$db->r($res) ){
			$tag_ids .= ','.$r['tag_id'];
		}
		return substr($tag_ids, 1);
	}

	/**
	 * 抓取指定链接页面的内容并解析
	 * @param  string $url 需要抓取的页面链接
	 * @return array       返回抓取结果数组
	 */
	function curlItemDetail($url){
		// 引入phpQuery（类jQuery）库
		include ROOT_PATH.'app/panel/plugin/phpQuery.php';
		// 使用php的curl扩展抓取$url页面源代码、将抓取结果载入到phpQuery
		$result_content = curl($url);
		$doc = phpQuery::newDocumentHTML($result_content);
		phpQuery::selectDocument($doc);
		// 指定url的页面可被抓取（否则需要匹配多套模版）
		if( substr_count($url, 'http://ai.taobao.com/') ) {
			$arr = array(
				'title'  => pq('.product-title')->text(),
				'price'  => pq('.promo-price:first>.value>.val>strong')->text(),
				'score'  => pq('.sub-block-title>.dib.score')->attr('data-grade'),
				'sales'  => pq('.get-more-comment>a')->text()+0,
				'avatar' => pq('.left-part-container img:eq(0)')->attr('src'),
				'spread' => '0',
			);
		}
		// 否则返回不能抓取的消息
		else{
			$arr = array(
				'status' 	=> 0,
				'message'	=> '待抓取链接不在处理范围内',
			);
		}
		// 过滤掉换行、首尾空格等不安全字符
		$arr = getSafeInput($arr);
		return $arr;
	}
}
