<?php
if( !defined('DCCMS') ) die();
include 'header.php';
?>

<script type="text/javascript" src="<?=$this->view_static?>js/libs/SuperMap.Include.js"></script>
<script type="text/javascript" src="<?=$this->view_static?>js/libs/osp.js"></script>
<script type="text/javascript" src="<?=$this->view_static?>js/examples/js/layer/Baidu.js"></script>
<script type="text/javascript" src="<?=$this->view_static?>js/examples/js/layer/Tianditu.js"></script>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=1.2"></script>

<!-- <script type="text/javascript" src="http://services.supermapcloud.com/iserver/api?key=3kJBQAvOH6Ra40DaHBIxF1Sd596aB6uySZcqOwqbpN9yDM76DYu9rhV%2FhwQ8fEMe"></script> -->
<script type="text/javascript">
G.STATIC_URL = G.ROOT_URL+'<?=$this->view_static?>';
$(function(){
	onPageLoad();
});



var map,BaiduLayer,CloudLayer,TiandituLayer,featuresLayer,markersLayer;
var measureLine,measurePoint;
var poiMeasurementGroup;

function onPageLoad(){
//初始化地图,添加地图控件
map = new SuperMap.Map("mapDiv");
map.addControls([new SuperMap.Control.PanZoomBar(),
				new SuperMap.Control.Attribution(),
				new SuperMap.Control.ScaleLine(),
				  new SuperMap.Control.OverviewMap(),
				  new SuperMap.Control.LayerSwitcher(),
                  new SuperMap.Control.Navigation({
                      dragPanOptions: {
                          enableKinetic: true
                      }
                  })
				  ]);

CloudLayer = new SuperMap.Layer.CloudLayer("CloudLayer");
BaiduLayer = new SuperMap.Layer.Baidu("BaiduLayer");
TiandituLayer = new SuperMap.Layer.Tianditu({"layerType":"img"}); //img,ter,vec

markersLayer = new SuperMap.Layer.Markers("markersLayer");
featuresLayer = new SuperMap.Layer.Vector("featuresLayer");

measureLine = new SuperMap.Control.DrawFeature(featuresLayer,SuperMap.Handler.Path, { multi: true });
measureLine.events.on({"featureadded": drawCompleted});
measurePoint = new SuperMap.Control.DrawFeature(featuresLayer,SuperMap.Handler.Point, { multi: true });
measurePoint.events.on({"featureadded": drawCompleted});
featuresLayer.style = {fillColor: "#7C9DE4",strokeColor: "#7A9BE2",pointRadius:6,strokeWidth:4};
map.addControls([measureLine,measurePoint]);

map.addLayers([CloudLayer,BaiduLayer,TiandituLayer,featuresLayer,markersLayer]);	
map.setCenter(new SuperMap.LonLat(12731244.396596, 3572116.6913969).transform(
            new SuperMap.Projection("EPSG:4326"),map.getProjectionObject()),10);
createPoiService();
}

//初始化POI管理
function createPoiService(){
	poiManager = new SuperMap.OSP.UI.POIManager(map);
	poiManager.markerLayer = markersLayer;
	poiMeasurementGroup = new SuperMap.OSP.UI.POIGroup("poigroup_measurement_id");
	poiMeasurementGroup.caption = "poi搜索分组";
	poiManager.addPOIGroup(poiMeasurementGroup);	
	//路径分析起始点、终止点初始化
}

//函数测距
var iClientMeter = 0;
function setMeasure(){
	show_head_tip('请单击地图选择起点、终点，双击结束', 'success');
	featuresLayer.style ={fillColor: "red",strokeColor: "red",pointRadius:6};
	measureLine.activate();
}

//函数绘制长度
var measureIndex = 0;
var measureHashMap = null;
function drawCompleted(drawGeometryArgs){
	var featureIds = new Array();
	measureLine.deactivate();	
	if(measureHashMap == null){
		measureHashMap = new SuperMap.OSP.Core.HashMap();}
	iClientMeter = 0;
	var index = ++measureIndex;
	var geometry = drawGeometryArgs.feature;
	featureIds.push(geometry.id);
	var start,end;
	var pois = new Array();
	for(var k = 0; k < geometry.geometry.components[0].components.length; k++){
		var point = new SuperMap.Geometry.Point(geometry.geometry.components[0].components[k].x,geometry.geometry.components[0].components[k].y);
		if(k == 0){ start = point; }
		else if(k == (geometry.geometry.components[0].components.length -1)){ end = point;}
		var pointFeature = new SuperMap.Feature.Vector(point);
		featureIds.push(pointFeature.id);
		pointFeature.style  = {fillColor: "#fffff",strokeColor: "#FF0000",pointRadius:5,strokeOpacity:0.5,fillOpacity:0.5};
		featuresLayer.addFeatures(pointFeature);
		pois.push(point);
	}
	measureHashMap.put(index,featureIds);
	for(var i = 0; i < pois.length; i++){
		pois[i] = new SuperMap.LonLat(pois[i].x,pois[i].y);
		pois[i] = SuperMap.OSP.Core.Utility.metersToLatLon(pois[i]);
	}
	for(var j = 0; j < pois.length; j++){
		if(j != (pois.length -1)){
			var p = new SuperMap.LonLat(pois[j].lon,pois[j].lat);
			var p1 = new SuperMap.LonLat(pois[j+1].lon,pois[j+1].lat);
			iClientMeter += SuperMap.Util.distVincenty(p,p1);
		}
	}
	for(var z = 0; z < pois.length; z++){
		//将线段的点变成莫卡托
		pois[z] = SuperMap.OSP.Core.Utility.latLonToMeters(pois[z]);
	}
	var poiStart = new SuperMap.OSP.UI.POI("poi_start_id" + index);
	var startContent = new SuperMap.OSP.UI.ScaledContent();
	startContent.content = G.STATIC_URL+"js/theme/images/marker.png";
	startContent.offset = new SuperMap.OSP.Core.Point2D(-12,-15);
	poiStart.scaledContents = startContent;
	poiStart.position = new SuperMap.OSP.Core.Point2D(start.x,start.y);
	poiStart.imageSize = new SuperMap.Size(25,18);
	var poiClose = new SuperMap.OSP.UI.POI("" + index);
	var closeScaled = new SuperMap.OSP.UI.ScaledContent();
	closeScaled.content = G.STATIC_URL+"js/theme/images/close.gif";
	closeScaled.offset = new SuperMap.OSP.Core.Point2D(132,-13);
	poiClose.scaledContents = closeScaled;
	poiClose.title = "清除本次测量结果";
	poiClose.imageSize = new SuperMap.Size(12,12);
	poiClose.position = new SuperMap.OSP.Core.Point2D(end.x,end.y);
	poiClose.addEventListerner("click",clearMeasure);
	var poiMeasurement = new SuperMap.OSP.UI.POI("poi_measurement_id" + index);
	poiMeasurement.position = new SuperMap.OSP.Core.Point2D(end.x,end.y);
	poiMeasurement.imageSize = new SuperMap.Size(130,20);
	var scaledContent = new SuperMap.OSP.UI.ScaledContent();
	scaledContent.offset = new SuperMap.OSP.Core.Point2D(15,-15);
	poiMeasurement.scaledContents = scaledContent;
	var distance = iClientMeter;
	var distanceinfo = '';
	if(distance * 100 < 100){
		distanceinfo = "总长：<font color=red>" + parseInt(distance * 100) * 10 + "</font>米";
	}else{
		distanceinfo = "总长：<font color=red>" + distance.toFixed(2) + "</font>公里";
	}
	scaledContent.content = '<div style="width:96px;height:17px;padding:2px;border:1px solid #ff0000;text-align:center; background-color:white;font-size:12px">' + distanceinfo + '</div>';
	poiMeasurement.scaledContents = scaledContent;
	poiMeasurementGroup.addPOIs([poiStart,poiMeasurement,poiClose]);
	poiManager.editPOIGroup(poiMeasurementGroup);
	poiManager.refreshPOI();
}


//清除量算结果
function clearMeasure(){
	var id = this.id;
	iClientMeter = 0;
	var array = measureHashMap.get(id);
	poiMeasurementGroup.removePOIs(id);
	poiMeasurementGroup.removePOIs("poi_measurement_id"+id);
	poiMeasurementGroup.removePOIs("poi_start_id"+id);
	poiManager.editPOIGroup(poiMeasurementGroup);
	poiManager.refreshPOI();
	var featureArray = new Array();
	for(var i = 0; i < array.length; i++){
		var feature = featuresLayer.getFeatureById(array[i]);
		featureArray.push(feature);
	}
	featuresLayer.removeFeatures(featureArray);

	measureHashMap.remove(id);
	measureIndex--;
}
//地图打印
function printMap(){
	var printService = new SuperMap.OSP.Service.PrintService();
	printService.printMap();
} 
        
</script>


<div id="mapDiv" style="position: fixed; left:0; width:100%; height:100%;"></div>
    
<div class="retrival_left_toolbox">

	<div class="bs-callout bs-callout-primary">
	    <h4>路线测量</h4>
	    <p>绘制线并量算长度。</p>
	</div>
	<div class="row">
	  <div class="col-lg-12">
		<button class="btn btn-block btn-primary" onclick="setMeasure()">路线测量</button>
	  </div>
	</div>
	<br>


	<div class="bs-callout bs-callout-info">
	    <h4>地图打印</h4>
	    <p>将当前地图视图打印出来。</p>
	</div>
	<div class="row">
	  <div class="col-lg-12">
		<button class="btn btn-block btn-info" onclick="printMap()">打印地图</button>
	  </div>
	</div>


</div>



<?php
include 'footer.php';
?>
