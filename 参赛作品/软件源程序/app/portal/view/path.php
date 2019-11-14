<?php
if( !defined('DCCMS') ) die();
include 'header.php';
?>

<script type="text/javascript" src="<?=$this->view_static?>js/libs/SuperMap.Include.js"></script>
<script type="text/javascript" src="<?=$this->view_static?>js/libs/osp.js"></script>
<script type="text/javascript" src="<?=$this->view_static?>js/examples/js/layer/Baidu.js"></script>
<script type="text/javascript" src="<?=$this->view_static?>js/examples/js/layer/Tianditu.js"></script>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=1.2"></script>

<script type="text/javascript" src="http://www.supermapcloud.com/demo/china/samples/include.js"></script>



<!-- <script type="text/javascript" src="http://services.supermapcloud.com/iserver/api?key=3kJBQAvOH6Ra40DaHBIxF1Sd596aB6uySZcqOwqbpN9yDM76DYu9rhV%2FhwQ8fEMe"></script> -->
<script type="text/javascript">
G.STATIC_URL = G.ROOT_URL+'<?=$this->view_static?>';


//定义

markerLayer = null;   //marker图层
featuresLayer = null; //矢量图层
poiManager = null;    //POI管理
poiSearchGroup = null;  //POI搜索结果分组

function init(){
	//初始化地图
	map = new SuperMap.Map("mapDiv");
	//添加地图控件
	map.addControl(new SuperMap.Control.Attribution());
	map.addControl(new SuperMap.Control.ScaleLine());
	map.addControl(new SuperMap.Control.OverviewMap());
	map.addControl(new SuperMap.Control.LayerSwitcher()); 
	map.addControl(new SuperMap.Control.Navigation({
                        dragPanOptions: {
                            enableKinetic: true
                        }
                    }));
	//map.addControl(new SuperMap.Control.MousePosition());获得EPSG900913下的地图坐标
	//初始化图层及其他要素
	CloudLayer = new SuperMap.Layer.CloudLayer("CloudLayer");
	BaiduLayer = new SuperMap.Layer.Baidu("BaiduLayer");
	TiandituLayer = new SuperMap.Layer.Tianditu({"layerType":"img"}); //img,ter,vec

	markerLayer = new SuperMap.Layer.Markers("markerLayer");
	featuresLayer = new SuperMap.Layer.Vector("featuresLayer");
	//添加图层
	map.addLayers([CloudLayer,BaiduLayer,TiandituLayer,markerLayer,featuresLayer]);	
	//初始化地图中心
	map.setCenter(new SuperMap.LonLat(12731244.396596, 3572116.6913969).transform(
				new SuperMap.Projection("EPSG:4326"),map.getProjectionObject()),10);	
	createPoiService();
	
	$('#mapDiv').on('click', function(ev){
		var oEvent=ev||event;
		var x=oEvent.offsetX;
		var y=oEvent.offsetY;
		var px = new SuperMap.Pixel(x,y);
		var templonlat = map.getLonLatFromLayerPx(px).transform(new SuperMap.Projection("EPSG:900913"),map.getProjectionObject());
		var lon_lat = templonlat.lon+', '+templonlat.lat;
		var btn_putstart = ' <button class="btn btn-xs btn-default" onclick="put_to_start('+lon_lat+')">设为起点</button>';
		var btn_putend   = ' <button class="btn btn-xs btn-default" onclick="put_to_end('+lon_lat+')">设为终点</button>';
		var msg = "经纬度：" + lon_lat + btn_putstart + btn_putend; 
		show_head_tip(msg, 'success', 15000);
	});
}
//初始化POI管理
function createPoiService(){
	poiManager = new SuperMap.OSP.UI.POIManager(map);
	poiManager.markerLayer = markerLayer;
	poiSearchGroup = new SuperMap.OSP.UI.POIGroup("poipathGroupId");
	poiSearchGroup.caption = "poi搜索分组";
	poiManager.addPOIGroup(poiSearchGroup);
}


// 路径分析-起始点和结束点固定ID
var findPath_StartID = 'findPath_POI_start';
var findPath_EndID = 'findPath_POI_end';
//路径分析结果处理
var drawLine = "";
var pathFeatures = [];
var pathItemPoints = []; //路径途径节点集合
var routePoints = [];
var pointMap;
var poiIndex = 0;//途经点索引
var pPntWay = [];//途经点坐标集合

//新路径分析方法
//起点116.37566618893023, "y":39.98860245609523
//终点x":116.45470821660479, "y":39.96279383689269
function findNewPath() {
	clearPath();

	var startPoint = new SuperMap.LonLat(document.getElementById("txtPathStartLon").value,document.getElementById("txtPathStartLat").value);
	var endPoint = new SuperMap.LonLat(document.getElementById("txtPathEndLon").value,document.getElementById("txtPathEndLat").value);
	var startImg = SuperMap.OSP.Core.Utility.latLonToMeters(startPoint);
	var startSize = new SuperMap.Size(25,30);
	var startIcon = new SuperMap.Icon('http://www.supermapcloud.com/cloudweb/images/start.png', startSize);
	var startMarker = new SuperMap.Marker(new SuperMap.LonLat(startImg.lon,startImg.lat),startIcon);
	markerLayer.addMarker(startMarker);
	
	var endImg = SuperMap.OSP.Core.Utility.latLonToMeters(endPoint);
	var endSize = new SuperMap.Size(25,30);
	var endIcon = new SuperMap.Icon('http://www.supermapcloud.com/cloudweb/images/end.png', endSize);
	var endMarker = new SuperMap.Marker(new SuperMap.LonLat(endImg.lon,endImg.lat),endIcon);
	markerLayer.addMarker(endMarker);
	
	map.setCenter(new SuperMap.LonLat(startPoint.lon,startPoint.lat).transform(new SuperMap.Projection("EPSG:4326"),
				new SuperMap.Projection("EPSG:900913"),map.getProjectionObject()),12);
	
	var param = new SuperMap.OSP.Service.TransportionAnalystParameter();
	var findPathStartPoint = new SuperMap.OSP.Service.SePoint();
	findPathStartPoint.x = document.getElementById("txtPathStartLon").value;
	findPathStartPoint.y = document.getElementById("txtPathStartLat").value;
	param.startPoint = findPathStartPoint;
	
	var findPathEndPoint = new SuperMap.OSP.Service.SePoint();
	findPathEndPoint.x = document.getElementById("txtPathEndLon").value;
	findPathEndPoint.y = document.getElementById("txtPathEndLat").value;
	param.endPoint = findPathEndPoint;
	param.pPntWay = pPntWay;
	param.nSearchMode = 1;//最佳路径
	param.coordsysType = 0; //设置坐标系统参数，0为经纬度坐标，1为墨卡托坐标,默认为0
	var transportationAnalyst = new SuperMap.OSP.Service.TransportationAnalyst();
	transportationAnalyst.url = "http://services.supermapcloud.com";
	transportationAnalyst.findPath(param,function(result){
		featuresLayer.style = {fillColor: "#FF0000",strokeColor: "#FF0000",pointRadius:6,strokeWidth:4};
		var line = new SuperMap.Geometry.LineString(result.path);
		var lineFeature = new SuperMap.Feature.Vector(line);
		featuresLayer.addFeatures(lineFeature);
		pathFeatures.push(lineFeature);
		var pathInfoList = result.pathInfoList;
		findPathResult(result);
		drawLine = result;
	},function(error){
		alert(error.information);
		return false;
	});
}

// 路径分析的添加方法
function addFindPathPointAction(stat){
	if(!stat){
		stat = 'start'
	}
	var z = stat;
	var onDrawCompleted = function(drawGeometryArgs) {
		var panAction = $create(SuperMap.Web.Actions.Pan, { map: map }, null, null, null);
        map.set_action(panAction);
        
        // 清除
        featuresLayer.clearFeatures();
        
        if(z == 'start'){
        	var startPOI = createFindPathPoint(findPath_StartID, '标注起始点', drawGeometryArgs.geometry.x, drawGeometryArgs.geometry.y);
        	var endPOI = poiSearchGroup.getPOIs(findPath_EndID);
        	
        	poiSearchGroup.clearPOIs();
        	poiSearchGroup.addPOIs(startPOI);
        	if(endPOI){
        		poiSearchGroup.addPOIs(endPOI);
        	}
        	poiManager.editPOIGroup(poiSearchGroup);
            poiManager.refreshPOI();
            
            $('#findPath_start').val('');
            $('#findPath_start').val(startPOI.name?startPOI.name:startPOI.title);
//            $('#findPath_start').val('标注起始点');
            
            // findPath_end
        } else {
        	var endPOI = createFindPathPoint(findPath_EndID, '标注结束点', drawGeometryArgs.geometry.x, drawGeometryArgs.geometry.y);
        	var startPOI = poiSearchGroup.getPOIs(findPath_StartID);
        	
        	poiSearchGroup.clearPOIs();
        	poiSearchGroup.addPOIs(endPOI);
        	if(startPOI){
        		poiSearchGroup.addPOIs(startPOI);
        	}
        	poiManager.editPOIGroup(poiSearchGroup);
            poiManager.refreshPOI();
            
            $('#findPath_end').val('');
            $('#findPath_end').val(endPOI.name?endPOI.name:endPOI.title);
//            $('#findPath_end').val('标注结束点');
        }
	}
	var drawAction = $create(SuperMap.OSP.UI.Actions.DrawPoint, { map: map }, null, null, null);
    drawAction.add_actionCompleted(onDrawCompleted);
    map.set_action(drawAction);
}

// 创建一个查询点
var createFindPathPoint = function(id, name, x, y){
	var poi = new SuperMap.OSP.UI.POI(id);
	poi.position = new SuperMap.Web.Core.Point2D(parseFloat(x), parseFloat(y));
    var scaledContent = new SuperMap.OSP.UI.ScaledContent();
    if(id.indexOf('start') != -1){
    	scaledContent.content = "<img src='../../images/start.png' />";
    }else {
    	scaledContent.content = "<img src='../../images/end.png' />";
    }
    
    scaledContent.offset = new SuperMap.Web.Core.Point(15, 33);
    poi.title = name;
    poi.scaledContents = scaledContent;
    poi.properties = {
        code: 'searchBuffer'
    };
    poi.addEventListerner("click", function(e){
//    	alert('zzz');
    	
    });
    
    return poi;
}

function findPathResult(result){
//	setTab(-1, null);
	// 获取转向信息
	var getDirToSwerve = function(i){
		var turn = '';
		switch (i) {
	        case 0:
	            turn = '直行';
	            break;
	        case 1:
	            turn = '左前转弯';
	            break;
	        case 2:
	            turn = '右前转弯';
	            break;
	        case 3:
	            turn = '左转弯';
	            break;
	        case 4:
	            turn = '右转弯';
	            break;
	        case 5:
	            turn = '左后转弯';
	            break;
	        case 6:
	            turn = '右后转弯';
	            break;
	        case 7:
	            turn = '调头';
	            break;
	        case 8:
	            turn = '右转弯绕行至左';
	            break;
	        case 9:
	            turn = '直角斜边右转弯';
	            break;
	        case 10:
	            turn = '环岛';
	            break;
	        case 11:
	            turn = '直角斜边左转弯';
	            break;
	    }
		return turn;
	}
	
	/**
	 * @index 转向信息索引号
	 * @dRouteLength 道路长度
	 * @iDirToSwerve 转向信息
	 * @strRouteName 道路名称
	 * @xy			 经纬度
	 * @NextstrRouteName 下条道路名称，最后点无效
	 * @markName 当是起点或者终点时，显示点名称
	 */
	var getRountTable = function(index, dRouteLength, iDirToSwerve, strRouteName, x, y, NextstrRouteName, markName, isEnd){
		
		var rout = getDirToSwerve(iDirToSwerve);
		var tab = '';
		var length = loadLengthFormat(dRouteLength);
		var nextIndex = index + 1;
		if(index == 0){
			tab = '<table style="font-size:11px"><tr>' +
				'<th>' + (index + 1) + '.</th>' +
				'<td>从<strong>' + markName + '</strong>出发，沿<span><a href="#">' + strRouteName + '</a></span>行驶' + length + '，' + rout + '进入<span><a href="#">' + NextstrRouteName + '</a></span></td>' + 
				'<td>' + '</td>' + 
			'</tr></table>';
		} else if(index > 0 && !isEnd) {
			tab = '<table style="font-size:11px"><tr>' +
				'<th>' + (index + 1) + '.</th>' +
				'<td>沿<span><a href="#">' + strRouteName + '</a></span>行驶' + length + '，' + rout + '进入<span><a href="#">' + NextstrRouteName + '</a></span></td>' +
				'<td>' + '</td>' + 
			'</tr></table>';
		} else if(isEnd){
			// 结束点
			tab = '<table style="font-size:11px"><tr>' +
				'<th>' + (index + 1) + '.</th>' +
				'<td>沿<span><a href="#">' + strRouteName + '</a></span>行驶' + length + '' + '，到达<strong>' + markName + '</strong></td>' + 
				'<td>' + '</td>' + 
			'</tr></table>';
			//tab += '<div class="con_title"><img src="http://www.supermapcloud.com/cloudweb/images/bus/zhongdian.gif" align="absmiddle" /><span>终点</span></div>';
		}
		return tab;
	}
	
	var path = result.path;
	var rout = result.pathInfoList;
	
	// dLength
	var html = '<div class="con">';
	//html += '<div class="con_title"><img src="http://www.supermapcloud.com/cloudweb/images/bus/qidian.gif" alt="起点" align="absmiddle" /><span>起点</span></div>';
	var routLength = rout.length;
	if(routLength && routLength > 0){
		html +='<div class="msg">全程：约<span>' + loadLengthFormat(eval(rout[0].dLength)) + '</span></div>';
		if(loadLengthFormat(eval(rout[0].dLength)) < 400){
			html = "当前距离小于400米，建议步行";
		}else{
			var i = 0;
			for(; i < routLength; i++){
				var markName = '起点';
				if(i == (routLength - 1)){
					markName = '终点';
					// 结束点
					html += getRountTable(i, rout[i].dRouteLength, rout[i].iDirToSwerve, rout[i].strRouteName, rout[i].x, rout[i].y, null, markName, true);
				}else if(i == 0){
					html += getRountTable(i, rout[i].dRouteLength, rout[i].iDirToSwerve, rout[i].strRouteName, rout[i].x, rout[i].y, rout[i + 1].strRouteName, markName, false);
				}else {
					html += getRountTable(i, rout[i].dRouteLength, rout[i].iDirToSwerve, rout[i].strRouteName, rout[i].x, rout[i].y, rout[i + 1].strRouteName, null, false);
				}
			}
		}
	}
	
	html += '</table><div class="search_bottom">查看:<a href="#">返程</a>│<a href="#">公交</a></div>';
	html += '</div>';
	document.getElementById("divInfo").innerHTML = html;
	document.getElementById("divInfo").style.display = "block";

}

// 将米数据转化为较为方便的读取方式
function loadLengthFormat(length){
	var getFormat = function(val){
	var v = Math.round(val * 10)/10;
	return v;
	}
	
	var unit = '米';
	var dLength = eval(length);
	if(dLength > 900) {
		dLength = dLength / 1000.0;
		unit = '公里';
	}
	
	return getFormat(dLength) + unit;
}

        
//清除路径分析高亮信息 
function clearPath() {
	document.getElementById("divInfo").innerHTML = "";
	featuresLayer.removeAllFeatures();
	markerLayer.clearMarkers();

	// featuresLayer.clearFeatures(pathFeatures);
    pathFeatures = [];
    poiPathGroup = {};
    poiPathGroup.pois = [];
    // poiManager.editPOIGroup(poiPathGroup);
    poiManager.refreshPOI();
	
	}


function put_to_start(lon, lat){
	$('#txtPathStartLon').val(lon);
	$('#txtPathStartLat').val(lat);
	show_head_tip('已成功设为起点');
}
function put_to_end(lon, lat){
	$('#txtPathEndLon').val(lon);
	$('#txtPathEndLat').val(lat);
	show_head_tip('已成功设为终点');
}

$(function(){
	init();
	var info_height = $('.retrival_left_toolbox').height()-$('.toolbox_wrapper').height();
	$('#divInfo').css({height:info_height-60});
});


        
</script>

<div id="mapDiv" style="position: fixed; left:0; width:100%; height:100%;"></div>


    
<div class="retrival_left_toolbox">
	<div class="toolbox_wrapper">
		<div class="row">
			<div class="col-lg-12"><h4>路径分析模式：</h4></div>
			<div class="col-lg-6">
				<input type="radio" name="radioPathMode" value="length" id="less_length" checked="checked" /> <label for="less_length">最少路程</label>
			</div>
			<div class="col-lg-6">
				<input type="radio" name="radioPathMode" value="time" id="less_time" /> <label for="less_time">最少时间</label>
			</div>
	    </div>

	    <div class="row mt-10 dv_inputbox">
			<div class="col-lg-12"><h4>起点的经纬度坐标：</h4></div>
			<div class="col-lg-12">
				<div class="input-group">
					<span class="input-group-addon">经</span><input class="form-control" type="input" id="txtPathStartLon" value="116.37566618893023" />
				</div>
				
				<div class="input-group">
					<span class="input-group-addon">纬</span><input class="form-control" type="input" id="txtPathStartLat" value="39.98860245609523" />
				</div>
			</div>
	    </div>

	    <div class="row mt-10 dv_inputbox">
			<div class="col-lg-12"><h4>起点的经纬度坐标：</h4></div>
			<div class="col-lg-12">
				<div class="input-group">
					<span class="input-group-addon">经</span><input class="form-control" type="input" id="txtPathEndLon" value="116.45470821660479" />
				</div>
				
				<div class="input-group">
					<span class="input-group-addon">纬</span><input class="form-control" type="input" id="txtPathEndLat" value="39.96279383689269" />
				</div>
			</div>
	    </div>

		<div class="row mt-10 dv">
			<div class="col-lg-6"><input type="button" class="btn btn-primary btn-sm" onclick="findNewPath()" value="路径分析" size="20"></div>
			<div class="col-lg-6"><input type="button" class="btn btn-default btn-sm" onclick="clearPath()" value="清除" size="20"></div>
	    </div>
		<hr>
	</div>

	<div id="divInfo"></div>

</div>



<?php
include 'footer.php';
?>
