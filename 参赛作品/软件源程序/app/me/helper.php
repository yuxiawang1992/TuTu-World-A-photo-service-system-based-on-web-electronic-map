<?php
class HLP_Me extends CTL_Me{

	private $parent;
	function __construct($parent){
		$this->parent = $parent;
	}

	function getPartOfToday(){
		$part = '';
		$hour = date('H');
		if( $hour>=0 && $hour<4 ){
			$part = '深夜';
		}
		elseif( $hour>=4 && $hour<9 ){
			$part = '早上';
		}
		elseif( $hour>=9 && $hour<12 ){
			$part = '上午';
		}
		elseif( $hour>=12 && $hour<13 ){
			$part = '中午';
		}
		elseif( $hour>=13 && $hour<18 ){
			$part = '下午';
		}
		elseif( $hour>=18 && $hour<24 ){
			$part = '晚上';
		}
		return $part;
	}

	function getWeekName(){
		$week_index = date('w');
		$week_arr = array(
			'0' => '日',
			'1' => '一',
			'2' => '二',
			'3' => '三',
			'4' => '四',
			'5' => '五',
			'6' => '六',
		);
		return $week_arr[$week_index];
	}

	function getQNUploadToken(){
		$QINIU_SDK_PATH = $this->parent->path."plugin/qiniu-6.1.9/";
		require_once($QINIU_SDK_PATH."qiniu/rs.php");
		require_once($QINIU_SDK_PATH."qiniu/io.php");

		$bucket = 'geeyan-static';
		$key1 = "file_name1";
		$accessKey = 'tlKabHDp7KqChyL2_neqU4oTXts6uY3vwVMfdfq6';
		$secretKey = 'Mt-NzuGOLPOdtL2u0a0_yg4AMtvdxSxMdvumlP_9';

		Qiniu_SetKeys($accessKey, $secretKey);
		$putPolicy = new Qiniu_RS_PutPolicy($bucket);
		$upToken = $putPolicy->Token(null);
		return $upToken;
	}
}
