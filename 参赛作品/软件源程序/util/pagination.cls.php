<?php
if( !defined('DCCMS') ) die();

class Pagination{

	// 常用变量
	private $_total;// 总信息数
	private $_all;// 总页数
	private $_now;// 当前页
	private $_per;// 每页显示行数
	private $_link;// 链接
	private $_show;// 显示页的数量

	// 初始化函数
	function __construct($total, $now, $link, $per=20, $show=7){
		$this->_total = $total;
		$this->_all = ceil($total/$per);
		$this->_now = $now;
		$this->_link = $link;
		$this->_per = $per;
		$this->_show = $show;
	}

	function getHtml(){
		$s = '';
		$now = $this->_now;
		// 小于$show页的
		if( $this->_all<=$this->_show ){
			$beg = 1;
			$end = $this->_all;
		}
		// 大于$show页，要计算窗口
		else{
			$dec = $now-ceil($this->_show/2);
			$beg = $dec>0 ? $dec+1 : 1;
			$beg = min($beg, $this->_all-$this->_show+1);
			$end = min($beg+$this->_show-1, $this->_all);
		}
		$s_prev = $now==1 ?
		'<li class="disabled"><span>Prev</span></li>' :
		'<li><a href="'.$this->_p($now-1).'">Prev</a></li>';
		$s_m = '';
		for($p = $beg; $p<=$end; $p++){
			if( $p==$now )
				$s_m .= '<li class="active"><span>'.$p.'</span></li>';
			else
				$s_m .= '<li><a href="'.$this->_p($p).'">'.$p.'</a></li>';
		}
		$s_post = $now>=$this->_all ?
		'<li class="disabled"><span>Next</span></li>' :
		'<li><a href="'.$this->_p($now+1).'">Next</a></li>';

		$s_info = '<span class="info">共<span class="total">'.$this->_total.'</span>条，每页<span class="per">'.$this->_per.'</span>条，分<span class="pages">'.$this->_all.'</span>页显示</span>&emsp;';
		
		$s .= '<div class="pagination-box">'.$s_info.'<ul class="pagination">';
		$s .= $s_prev.$s_m.$s_post;
		$s .= '</ul></div>';

		return $s;
	}

	private function _p($p){
		$href = str_replace('{p}', $p, $this->_link);
		if( substr_count($href, '?')==0 && $_SERVER['QUERY_STRING']!='' ){
			// $href .= '?'.$_SERVER['QUERY_STRING'];
		}
		return $href;
	}
}//End Class
