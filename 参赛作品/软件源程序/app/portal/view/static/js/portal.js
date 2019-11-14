G.x_pi = 3.14159265358979324 * 3000.0 / 180.0;
G.lat_mod = 5.4135;
G.lng_mod = 13.6495;

// 全局js
$(function(){

  // 修改nav
  if( G.act!='' ){
    $('[data-label]').removeClass('active');
    $('[data-label="'+G.act+'"]').addClass('active');
  }

});



function convert_latlng(lat, lng){
	lat = 100000*(lat+G.lat_mod);
	lng = 100000*(lng+G.lng_mod);
	return [lat, lng];
}


/// <summary>
/// 中国正常坐标系GCJ02协议的坐标，转到 百度地图对应的 BD09 协议坐标
/// </summary>
/// <param name="lat">维度</param>
/// <param name="lng">经度</param>
function Convert_GCJ02_To_BD09(lat, lng){
	var x = lng, y = lat;
	var z =Math.sqrt(x * x + y * y) + 0.00002 * Math.sin(y * G.x_pi);
	var theta = Math.atan2(y, x) + 0.000003 * Math.cos(x * G.x_pi);
	lng = z * Math.cos(theta) + 0.0065;
	lat = z * Math.sin(theta) + 0.006;
	return [lat, lng];
}
/// <summary>
/// 百度地图对应的 BD09 协议坐标，转到 中国正常坐标系GCJ02协议的坐标
/// </summary>
/// <param name="lat">维度</param>
/// <param name="lng">经度</param>
function Convert_BD09_To_GCJ02(lat, lng){
	var x = lng - 0.0065, y = lat - 0.006;
	var z = Math.sqrt(x * x + y * y) - 0.00002 * Math.sin(y * G.x_pi);
	var theta = Math.atan2(y, x) - 0.000003 * Math.cos(x * G.x_pi);
	lng = z * Math.cos(theta);
	lat = z * Math.sin(theta);
	return [lat, lng];
} 

