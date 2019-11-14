// 全局js
$(function(){
  G.__message && showHeadTip(G.__message);
});


/* ================================================ */
/*                     分割线                        */
/* ================================================ */
function l(o){
  console.log(o);
}
// 给ellipsis元素加上title @ 2014-06-08 00:49:14
function addTitleToEllipsis(){
  $('.ellipsis').each(function(k, v){
    $(v).attr('title', $.trim($(v).text()).replace(/\s+/g, ' '));
  });
}


// 得到Json数据的长度 @ 2014-05-06 10:41:34
function getObjectLength(obj){
  var len = 0;
  if( typeof(obj)=='object' ){
    // IE8及以下，不支持Object.keys方法 @ 2014-06-13 16:21:12
    if($.browser.msie && parseInt($.browser.version)<=8){
      $.each(obj, function(k, v){
        len++;
      });
    }
    else{
      len = Object.keys(obj).length;
    }
  }
  return len;
}

// 模拟睡眠时间 @ 2014-04-13 18:37:11
function sleep(n){
  var start=new Date().getTime();//定义起始时间的
  while(true){
    var time=new Date().getTime();//每次执行循环取
    if(time-start>n){//如果当前时间的毫秒数减去起始
      break;
    }
  }
}

// 误关闭页面时提示保存
function beforeUnloadSet(msg){
  var UnloadConfirm = {};
  if( msg===undefined ){
    msg = "数据尚未保存，离开后可能会导致数据丢失！";
  }
  UnloadConfirm.set = function(a) {
    window.onbeforeunload = function(b) {
      b = b || window.event;
      b.returnValue = a;
      return a;
    }
  };
  UnloadConfirm.set(msg);
}
function beforeUnloadClear(){
  window.onbeforeunload = function() {}
}

// 显示通知
function showHeadTip(msg, type){
  if( msg===undefined ){
    return;
  }
  var _mt = mt();
  hideHeadTip();
  var is_mobile_html = $('body').width()<700 ? ' mini' : '';
  $('body').prepend('<div id="_m'+_mt+'" class="__message'+is_mobile_html+'">'+msg+'</div>');

  // 自动识别成功、失败
  if( type===undefined ){
    if( msg.indexOf('成功')!==-1 ){
      type = 'success';
    }
    else if( msg.indexOf('失败')!==-1
      || msg.indexOf('错误')!==-1
      || msg.indexOf('出错')!==-1 ){
      type = 'error';
    }
  }
  if( type=='error' ){
    $('#_m'+_mt).css({background:'#FA3F54',border:'1px solid #BD362F'});
  }
  else if( type=='success' ){
    $('#_m'+_mt).css({background:'#5BB75B',border:'1px solid #51A351'});
  }
  $('#_m'+_mt).fadeIn(120);
  setTimeout(function(){$('#_m'+_mt).fadeOut();}, 2000);
}
function hideHeadTip(){
  $('.__message').remove();
}

// 全局搜索 @ 2014-03-17 12:04:54
function searchGlobal(){
  kw = $('.input-search').val();
  kw = $.trim(kw);
  if( kw=='' ){
    return false;
  }
  kw = kw.substr(0, 30);
  kw = kw.replace(new RegExp("/","g"), 'qgda');
  kw = kw.replace(new RegExp("'","g"), 'qgdb');
  kw = kw.replace(new RegExp('"',"g"), 'qgdc');
  kw = kw.replace(new RegExp(':',"g"), 'qgdd');
  kw = kw.replace(new RegExp('#',"g"), 'qgde');
  //showHeadTip(kw);
  window.location = G.ROOT_URL+'q/'+kw.toLowerCase();
  return false;
}

// 禁用目标元素内的所有表单 @ 2014-03-09 23:15:54
function disableInputs($ta, $excludes){
  $ta.find('select, input, textarea').each(function(k, v){
    if( $.inArray(v, $excludes)!==-1 ){
      return;
    }
    $(v).attr('disabled', true);
  });
  $ta.find('.tags').addClass('disabled');

  typeof(UE)!=='undefined'
  && UE.getEditor('ueditor').setDisabled('fullscreen');
}

function enableInputs($ta, $excludes){
  $ta.find('select, input, textarea').each(function(k, v){
    if( $.inArray(v, $excludes)!==-1 ){
      return;
    }
    $(v).removeAttr('disabled');
  });
  $ta.find('.tags').removeClass('disabled');

  typeof(UE)!=='undefined'
  && UE.getEditor('ueditor').setEnabled();
}


// 取标签 @ 2014-02-16 11:03:27
function getTags(obj, id){
  var tags = '', value = '';
  // only get visible tags
  $(obj+" span").each(function(k, v) {
    if($(v).hasClass('active')){
      value = typeof(id)!='undefined' ? $(v).data(id) : $(v).text();
      tags += ','+value;
    }
  });
  tags = tags.substr(1);
  return tags;
}
// 设置标签 @ 2014-02-16 11:18:43
function setTags(obj, str, id){
  if( !str||0 ){
    return;
  }
  var flags = str.split(','), value = '';
  $(obj+" span").removeClass('active');
  $(obj+" span").each(function(k, v) {
    $.each(flags, function(kk, vv){
      value = typeof(id)!='undefined' ? $(v).data(id) : $(v).text();
      if( vv==value ){
        $(v).addClass('active');
        return false;
      }
    });
  });
}

// cookie读写函数 @ 2013-08-15 00:54:13
function ck(k, v){
  // 如果直接调用ck()，则返回document.cookie @ 2014-02-14 03:31:50
  if( typeof(k)=='undefined' && typeof(v)=='undefined' )
    return document.cookie;
  // 如果用==，则V为0时也将失效 @ 2013-11-01 14:31:02
  if( typeof(v)=='undefined' ) return $.cookie(G.ckprefix+k); //v===''
  return $.cookie(G.ckprefix+k, v, {path:G.ckpath});
}


// 引自php
function deb(o){
  console.log(getObj(o));
}

// 返回当前时间戳 @ 2013-10-31 17:43:34
function time(cmd){
  if(cmd=='-mt') return new Date().getTime();
  return Date.parse(new Date())/1000;
}
function mt(){
  return time('-mt');
}

// 返回datetime
function dt(timestamp){var fixTime=function(t){if(t<10)t="0"+t;return t;}
if( timestamp===undefined ){
  var today=new Date();
}
else{
  var today=new Date(timestamp * 1000);
}
var years=today.getFullYear();var months=fixTime(today.getMonth()+1);var date=fixTime(today.getDate());var hours=fixTime(today.getHours());var minutes=fixTime(today.getMinutes());var seconds=fixTime(today.getSeconds());return years+"-"+months+"-"+date+" "+hours+":"+minutes+":"+seconds;}

// 获取屏幕分辨率
function resolution(){
  return window.screen.width+'x'+window.screen.height;
}

// 是否支持FLASH
function ableFlash(){var hasFlash=0;var flashVersion=0;var isIE=0;if(isIE){var swf=new ActiveXObject('ShockwaveFlash.ShockwaveFlash');if(swf){hasFlash=1;VSwf=swf.GetVariable("$version");flashVersion=parseInt(VSwf.split(" ")[1].split(",")[0]);}}else{if(navigator.plugins&&navigator.plugins.length>0){var swf=navigator.plugins["Shockwave Flash"];if(swf){hasFlash=1;var words=swf.description.split(" ");for(var i=0;i<words.length;++i){if(isNaN(parseInt(words[i])))continue;flashVersion=parseInt(words[i]);}}}}
return hasFlash;}

// 是否支持JAVA
function ableJava(){
  if( navigator.javaEnabled() ) return 1;
}
// 是否支持html5
function fullHTML5(){var html5=0;if(!!document.createElement('video').canPlayType){var vidTest=document.createElement("video");oggTest=vidTest.canPlayType('video/ogg; codecs="theora, vorbis"');if(!oggTest){h264Test=vidTest.canPlayType('video/mp4; codecs="avc1.42E01E, mp4a.40.2"');if(!h264Test){html5=0;}
else{if(h264Test=="probably")html5=2;else html5=1;}}
else{if(oggTest=="probably")html5=2;else html5=1;}}
return html5;}

// 邮箱号验证
function chkMail(o){
  var reEml = /^[\w\-\.]+@[a-z0-9]+(\-[a-z0-9]+)?(\.[a-z0-9]+(\-[a-z0-9]+)?)*\.[a-z]{2,4}$/i;
  return reEml.test(o);
}

// 返回数组的串
function getObj(o,rec){if(typeof(o)=='string'||typeof(o)=='number'){return o;}
if(typeof(rec)=='undefined')rec=0;rec++;var i=0;var str="(\n";var end_space='';var space=use_space="　　";while(i++<rec-1){end_space+=space;use_space+=space;}
var j=0;$.each(o,function(k,v){j++;if(typeof(v)=='object'){v=getObj(v,rec);}
if(j==rec){var i=0;while(i++<rec-1){use_space=use_space.replace(space,'');}}
else if(rec==1){use_space=space;}
str+=use_space+'['+k+'] => '+v+"\n";});return str+end_space+')';}

function l(o){
  console.log(o);
}


/******    some jquery plugins   ******/
// _GET
jQuery.extend({
  _GETS: function(){
    var vars = {}, hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');

    for(var i = 0; i < hashes.length; i++){
      hash = hashes[i].split('=');
      vars[hash[0]] = (hash[1]||"").split('#').shift();
    }
    return vars;
  },
  _GET: function(name){
    return $._GETS()[name];
  }
});

// bootstrap button function
!function(a){var b=function(b,c){this.$element=a(b),this.options=a.extend({},a.fn.button.defaults,c)};b.prototype.setState=function(a){var b="disabled",c=this.$element,d=c.data(),e=c.is("input")?"val":"html";a+="Text",d.resetText||c.data("resetText",c[e]()),c[e](d[a]||this.options[a]),setTimeout(function(){a=="loadingText"?c.addClass(b).attr(b,b):c.removeClass(b).removeAttr(b)},0)},b.prototype.toggle=function(){var a=this.$element.closest('[data-toggle="buttons-radio"]');a&&a.find(".active").removeClass("active"),this.$element.toggleClass("active")};var c=a.fn.button;a.fn.button=function(c){return this.each(function(){var d=a(this),e=d.data("button"),f=typeof c=="object"&&c;e||d.data("button",e=new b(this,f)),c=="toggle"?e.toggle():c&&e.setState(c)})},a.fn.button.defaults={loadingText:"loading..."},a.fn.button.Constructor=b,a.fn.button.noConflict=function(){return a.fn.button=c,this},a(document).on("click.button.data-api","[data-toggle^=button]",function(b){var c=a(b.target);c.hasClass("btn")||(c=c.closest(".btn")),c.button("toggle")})}(window.jQuery);


// cookie封装
jQuery.cookie=function(name,value,options){if(typeof value!='undefined'){if(typeof(options)=="undefined")options={};options.expires=options.expires|90;if(value===null){value='';options.expires=-1;}
var expires='';if(options.expires&&(typeof options.expires=='number'||options.expires.toUTCString)){var date;if(typeof options.expires=='number'){date=new Date();date.setTime(date.getTime()+(options.expires*24*60*60*1000));}else{date=options.expires;}
expires='; expires='+date.toUTCString();}
var path=options.path?'; path='+options.path:'';var domain=options.domain?'; domain='+options.domain:'';var secure=options.secure?'; secure':'';document.cookie=[name,'=',encodeURIComponent(value),expires,path,domain,secure].join('');}else{var cookieValue=null;if(document.cookie&&document.cookie!=''){var cookies=document.cookie.split(';');for(var i=0;i<cookies.length;i++){var cookie=jQuery.trim(cookies[i]);if(cookie.substring(0,name.length+1)==(name+'=')){cookieValue=decodeURIComponent(cookie.substring(name.length+1));break;}}}
return cookieValue;}};

