<?php
if( !defined('DCCMS') ) die();
include 'header.php';
?>


<div id="BaseMapDiv" style="position:fixed;left:0px;right:0px;width:100%;height:100%;"></div>

<div class="retrival_left_toolbox" style="padding-top:0;">
	<div class="bs-callout bs-callout-primary">
	    <h4>全局搜索</h4>
	    <!-- <p>可以自定义关键词，检索你想要的地点。</p> -->
	</div>
	<div class="row">
	  <div class="col-lg-12">
	    <div class="input-group">
	      <input id="QueryBox" type="text" value="武汉大学" class="form-control">
	      <span class="input-group-btn">
	        <button class="btn btn-primary" onclick="allsearch()" type="button">搜索</button>
	      </span>
	    </div><!-- /input-group -->
	  </div><!-- /.col-lg-6 -->
	</div><!-- /.row -->
	<br>

	<div class="bs-callout bs-callout-success">
	    <h4>常用检索</h4>
	    <!-- <p>可以快速定位常用检索。</p> -->
	</div>
	<div class="row dv">
		<div class="col-lg-6"><input type="button" class="btn btn-success btn-sm" onclick="search('餐饮')" value="餐饮" size="20"></div>
		<div class="col-lg-6"><input type="button" class="btn btn-success btn-sm" onclick="search('住宿')" value="住宿" size="20"></div>
	
		<div class="col-lg-6"><input type="button" class="btn btn-success btn-sm" onclick="search('酒吧')" value="酒吧" size="20"></div>
		<div class="col-lg-6"><input type="button" class="btn btn-success btn-sm" onclick="search('停车场')" value="停车场" size="20"></div>

		<div class="col-lg-6"><input type="button" class="btn btn-success btn-sm" onclick="search('医院诊所')" value="医院诊所" size="20"></div>
		<div class="col-lg-6"><input type="button" class="btn btn-success btn-sm" onclick="search('地铁')" value="地铁" size="20"></div>

		<div class="col-lg-6"><input type="button" class="btn btn-success btn-sm" onclick="search('火车站')" value="火车站" size="20"></div>
		<div class="col-lg-6"><input type="button" class="btn btn-success btn-sm" onclick="search('ATM')" value="ATM" size="20"></div>

		<div class="col-lg-6"><input type="button" class="btn btn-success btn-sm" onclick="search('商场')" value="商场" size="20"></div>
		<div class="col-lg-6"><input type="button" class="btn btn-success btn-sm" onclick="search('超市')" value="超市" size="20"></div>

		<div class="col-lg-6"><input type="button" class="btn btn-success btn-sm" onclick="search('电影院')" value="电影院" size="20"></div>
		<div class="col-lg-6"><input type="button" class="btn btn-success btn-sm" onclick="search('学校')" value="学校" size="20"></div>

		<div class="col-lg-6"><input type="button" class="btn btn-success btn-sm" onclick="search('丽人')" value="丽人" size="20"></div>
		<div class="col-lg-6"><input type="button" class="btn btn-success btn-sm" onclick="search('金融')" value="金融" size="20"></div>

		<div class="col-lg-6"><input type="button" class="btn btn-success btn-sm" onclick="search('景点')" value="景点" size="20"></div>
		<div class="col-lg-6"><input type="button" class="btn btn-success btn-sm" onclick="search('娱乐')" value="娱乐" size="20"></div>

		<div class="col-lg-6"><input type="button" class="btn btn-success btn-sm" onclick="search('加油站')" value="加油站" size="20"></div>
		<div class="col-lg-6"><input type="button" class="btn btn-success btn-sm" onclick="search('购物')" value="购物" size="20"></div>

		<div class="col-lg-6"><input type="button" class="btn btn-success btn-sm" onclick="search('国家机构')" value="国家机构" size="20"></div>
		<div class="col-lg-6"><input type="button" class="btn btn-success btn-sm" onclick="search('企业')" value="企业" size="20"></div>
	</div>
</div>

<script type="text/javascript" src="http://api.map.baidu.com/api?v=1.2"></script>
<script type="text/javascript">
$(function(){
	// 百度地图API功能
	var map = new BMap.Map("BaseMapDiv");                      // 创建map实例
	map.centerAndZoom(new BMap.Point(114.361, 30.541), 14);
	map.addControl(new BMap.NavigationControl());               // 添加平移缩放控件
	map.addControl(new BMap.ScaleControl());                    // 添加比例尺控件
	map.addControl(new BMap.OverviewMapControl());              //添加缩略地图控件
	map.enableScrollWheelZoom();                               //启用滚轮放大缩小
	map.addControl(new BMap.MapTypeControl());
	G.local = new BMap.LocalSearch(map, {renderOptions:{map: map} });
});


function search(KeyWords){
	G.local.search(KeyWords);}
function allsearch(){
	G.local.search(document.getElementById("QueryBox").value);}
</script>



<?php
include 'footer.php';
?>
