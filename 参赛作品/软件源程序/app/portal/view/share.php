<?php
if( !defined('DCCMS') ) die();
include 'header.php';
?>

<script src="<?=$this->view_static?>js/libs/SuperMap.Include.js"></script>
<script src="<?=$this->part_url?>?method=get_public_images<?php
if( !empty($_GET['method']) ){
	echo '&sub_method='.$_GET['method'].'&tag='.$_GET['tag'];
}
?>"></script>
<script type="text/javascript">
G.PART_URL = '<?=$this->part_url?>';

var map,layer,infowin,clusterLayer;
var popUpData = function(i){
	closeInfoWin();
	openInfoWin(G.ps[i]);
}

function openInfoWin(feature){
	var geo = feature.geometry;
	var bounds = geo.getBounds();
	var center = bounds.getCenterLonLat();
	var contentHTML = '<div class="pop-container">'+
		'<div class="pop-title">'+feature.info.title+' <small> by '+feature.info.username+'</small></div>'+
		'<div class="pop-picture"><a href="'+feature.info.img+'" target="_blank"><img src="'+feature.info.img+'/150.jpg" onload="resizeimg(this,150,150);" /></a></div>'+
		'<div class="pop-content">'+feature.info.description+'<br><div class="pop-time">'+feature.info.time+'</div><div class="pop-info"><span class="f_position"><span class="glyphicon glyphicon-map-marker"></span> '+feature.info.position.substr(0, 11)+'</span><span class="f_tags"><span class="glyphicon glyphicon-tags"></span> '+feature.info.tag_names+'</span></div></div>'+
		'</div>';
	
	var popup = new SuperMap.Popup.FramedCloud("popwin",
		new SuperMap.LonLat(center.lon, center.lat),
		null,
		contentHTML,
		null,
		true
	);
	
	feature.popup = popup;
	infowin = popup;
	map.addPopup(popup);
}

function closeInfoWin(){
	if(infowin){
		try{
			infowin.hide();
			infowin.destroy();
		}catch(e){}
	}
}


function resizeimg(obj,maxW,maxH)
{
    var imgW=obj.width;
    var imgH=obj.height;
    if(imgW>maxW||imgH>maxH)
    {        
		var ratioA=imgW/maxW;
		var ratioB=imgH/maxH;                
		if(ratioA>ratioB)
		{
			imgH=maxW*(imgH/imgW);
			imgW=maxW;
		}
		else
		{
			imgW=maxH*(imgW/imgH);
			imgH=maxH;
		}   
		obj.width=imgW;
		obj.height=imgH;
    }
}

function doCluster(){
	var ps = [];
	for(var i=0;i<D.length;++i){
		var lonlat = D[i].lonlat;
		var latlng_arr = convert_latlng(lonlat.lat, lonlat.lon);
		var f = new SuperMap.Feature.Vector();
		f.geometry = new SuperMap.Geometry.Point(latlng_arr[1], latlng_arr[0]);
		f.index = i;
		f.style = {
			graphic: true,
			externalGraphic:D[i].img+'/50.jpg',
			graphicWidth:50,
			graphicHeight:50,
			border:1,
		};
		f.info = D[i];
		ps.push(f);
	}
	G.ps = ps;
	return ps;
}

function dowork() {
	map = new SuperMap.Map("map", { controls:[
        new SuperMap.Control.ScaleLine(),
        new SuperMap.Control.LayerSwitcher(),
        new SuperMap.Control.PanZoomBar(50,null,13,11,true,true),
        new SuperMap.Control.Navigation({
            dragPanOptions:{
                enableKinetic:true
            }
        })]
    });

    layer = new SuperMap.Layer.CloudLayer();
	clusterLayer = new SuperMap.Layer.ClusterLayer("ClusterLayer",{
		isDiffused:true,
		tolerance:80
	});
	
    map.addLayers([layer,clusterLayer]);
	var select = new SuperMap.Control.SelectCluster(clusterLayer);
	map.addControl(select);
	select.activate();
	
	clusterLayer.events.on({'clickFeature':function(f){
		closeInfoWin();
		openInfoWin(f);
	}});
	clusterLayer.events.on({'clickout':function(f){
		closeInfoWin();
	}});
	clusterLayer.events.on({'moveend':function(e){
		if(e&&e.zoomChanged)closeInfoWin();
	}});
	clusterLayer.events.on({'clickCluster':function(f){
		closeInfoWin();
	}});
		
    map.setCenter(new SuperMap.LonLat(11531244.396596, 3872716.6913969), 5);
    	
	clusterLayer.addFeatures(doCluster());

}



function generate_share_bottom_bar(){
	$('body').append('<div class="share_bottom_bar"><ul></ul></div>');
	for(var i=0;i<D.length;++i){
		var li_html = '<li><a onclick="popUpData('+i+');"><img src="'+D[i].img+'/150.jpg" alt="'+D[i].title+'" /><h4>'+D[i].title+'</h4></a></li>';
		$('.share_bottom_bar>ul').append(li_html);
	}

	var li_width = $('.share_bottom_bar>ul>li:first').width();
	$('.share_bottom_bar>ul').css('width', (D.length*(li_width+22))+'px');

	$('.share_bottom_bar')[0].onmousewheel = function(e) {
	    var left = $('.share_bottom_bar').scrollLeft();
	    $('.share_bottom_bar').scrollLeft(left-e.wheelDeltaY);
	};
}

$(function(){
	dowork();
	generate_share_bottom_bar();

	$('#filter_tags_select').on('change', function(){
		var tag = $(this).val();
		// l(tag);return;
		if( tag=='0' ){
			return;
		}
		else if( tag=='all' ){
			window.location = G.PART_URL;
		}
		else{
			window.location = G.PART_URL+'?method=filter&tag='+tag;
		}
	});

	if( '<?=$_GET['tag']?>'!='' ){
		$('#filter_tags_select').val('<?=$_GET['tag']?>');
	}
});

</script>

<div id="map" style="position:fixed;left:0px;right:0px;width:100%;height:100%;"></div>

<div class="filter_tags">
	筛选标签：
	<select name="filter_tags_select" id="filter_tags_select">
		<option value="all">全部</option>
<?php
	foreach ($tags_map as $k=>$v){
		echo '<option value="'.$k.'">'.$v.'</option>';
	}
?>
	</select>
</div>

<?php
include 'footer.php';
?>
