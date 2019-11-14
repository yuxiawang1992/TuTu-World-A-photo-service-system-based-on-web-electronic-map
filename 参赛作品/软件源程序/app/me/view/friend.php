<?php
if( !defined('DCCMS') ) die();
include 'header.php';
?>


<section class="ta-column-right in">
	<div class="wrapper">
		<div class="chatbox hide" data-user_id="0">
			<div class="chat_title"><span class="icon icon-wall"></span> 和<span class="user_nickname">用户</span>聊天</div>
			
			<div class="chat_body">
				<div class="chat_wall">
					
				</div>
				<div class="chat_input">
					<div class="chat_input_inner" id="input_area" contentEditable="true"></div>
				</div>
			</div>

			<div class="chat_tool">
				<span class="fr"><button class="btn btn-xs btn-primary btn_send" data-loading-text="发送中…">发送(Ctrl+Enter)</button></span>
				<span class="fr"><button class="btn btn-xs btn-default btn_close">关闭(Alt+C)</button></span>
			</div>
		</div>

		<div class="contactbox">
			<div class="contact_title"><span class="icon icon-user"></span> 好友列表</div>

			<ul class="contact_list">
				<?=Mdl_Me_Friend::getContactList($uid);?>
			</ul>

			<div class="contact_tool">
				<span class="fr hide">共有<span class="friend_count">?</span>位好友</span>
				<button class="btn btn-xs btn-primary btn_addfriend">添加好友</button>
			</div>
		</div>
	</div>
</section>


<script type="text/javascript">
$(function(){
	G.uin = '<?=$uid?>';
	G.AJAX_URL = G.ROOT_URL+'me/friend';
	// 更新聊天框架高度
	var chat_height = $(window).height()-150;
	$('.chatbox, .contactbox').css({height: chat_height});
	$('.contactbox>ul.contact_list').css({height: $('.contactbox>ul.contact_list').height()-45});
	$('.chat_wall').css({height: chat_height-200});

	// 计算好友数量
	$('.friend_count').text($('.contact_list>li[data-user_id]').length).parent().removeClass('hide');

	// 点击发送按钮
	$('.chat_tool .btn_send').on('click', function(){
		var $sf = $(this);
		if( $sf.hasClass('disabled') ){
			return;
		}
		var content = $('.chat_input_inner').html();
		if( content=='' ){
			$('.chat_input_inner').focus();
			return;
		}
		var params = {
			method		: 'send_message',
			user_id_to 	: $sf.parents('[data-user_id]').data('user_id'),
			msg 		: content,
		};
		$sf.button('loading');
		disable_chat_input();
		$.post(G.AJAX_URL, params, function(d){
			if( d.msg.indexOf('失败')===-1 ){
				$('.chat_input_inner').html('');
				put_to_chat_wall(d.chat_time, content);
			}
			else{
				showHeadTip(d.msg);
			}
			$sf.button('reset');
			enable_chat_input();
		});
	});

	// 点击关闭按钮
	$('.chat_tool .btn_close').on('click', function(){
		$('.chatbox').addClass('hide');
	});

	// 点击添加好友
	$('.btn_addfriend').live('click', function(){
		var uin = window.prompt("请输入对方的图图号（数字编号）", "");
		if( uin!=null ){
			params = {
				method 		: 'get_user_data',
				uin 		: uin,
			};
			showHeadTip('正在查找用户..');
			// ajax传输数据
			$.get(G.AJAX_URL, params, function(d){
				hideHeadTip();
				if(d!=''){
					if( confirm('确定添加 '+d+' 为好友吗？') ){
						var params = {
							method 		: 'add_friend',
							uin 		: uin,
						};
						showHeadTip('正在发送好友请求..');
						$.get(G.AJAX_URL, params, function(d){
							showHeadTip(d);
						});
					}
				}
				else{
					showHeadTip('找不到此用户');
				}
			});
		}

	});

	// 绑定热键
	bind_hot_keys();

	// 绑定粘贴传图
	past_upload_pic();

	// 开始轮询
	polling();
});


function bind_hot_keys(){
	var KEY = {UP: 38,DOWN: 40,DEL: 46,TAB: 9,RETURN: 13,ESC: 27,COMMA: 188,PAGEUP: 33,PAGEDOWN: 34,BACKSPACE: 8};
	// ctrl+enter
	$('.chat_input_inner').live('keydown', function(e) {
		if(e.keyCode==KEY.RETURN && e.ctrlKey) {
			e.preventDefault();
			$('.chat_tool .btn_send').click();
		}
	});
	
	// alt+c
	$('.chat_input_inner').live('keydown', function(e) {
		if(e.keyCode==67 && e.altKey) {
			e.preventDefault();
			$('.chat_tool .btn_close').click();
		}
	});
}

function polling(){
	if( G.polling==1 ){
		return 'polling..';
	}

	var params = {
		method 		: 'poll',
	};
	G.polling = 1;
	$.get(G.AJAX_URL, params, function(d){
		if( typeof(d)=='object' && get_object_length(d)>0 ){
			$.each(d, function(k, v){
				if( v.type=='add_friend' ){
					if( confirm(v.from_user_nickname+' 于 '+v.date_time+'请求添加您为好友， 同意吗？') ){
						var params = {
							method 		: 'add_friend',
							uin 		: v.user_id_from,
						};
						showHeadTip('正在发送好友请求..');
						$.get(G.AJAX_URL, params, function(d){
							showHeadTip(d);
						});
					}
				}
				else if( v.type=='send_message' ){
					chat_with_friend(v.user_id_from, 'recieve');
					put_to_chat_wall(v.chat_time, v.msg);
					// @todo 这个ajax方法后台查询较多，要优化
					fresh_contact_list();
				}
				else if( v.type=='is_friend' ){
					showHeadTip('添加好友 '+v.from_user_nickname+' 成功！');
					fresh_contact_list();
				}

			});
		}
		G.polling = 0;
		polling();
	});
}

function put_to_chat_wall(chat_time, content){
	var mine_class = '';
	if( chat_time.indexOf('('+G.uin+')')!==-1 ){
		mine_class = ' mine';
	}
	$('.chat_wall').append('<div class="chat_list"><p class="chat_time'+mine_class+'">'+chat_time+'</p><p class="chat_content">'+content+'</p></div>');
	$('.chat_wall').scrollTop(10000000);
	setTimeout(function(){
		$('.chat_wall').scrollTop(10000000);
		setTimeout(function(){
			$('.chat_wall').scrollTop(10000000);
		}, 700);
	}, 300);
}

function fresh_contact_list(){
	var params = {
		method 		: 'fresh_contact_list',
	};
	$.get(G.AJAX_URL, params, function(d){
		$('.contact_list').html(d);
	});
}

function chat_with_friend(uin, type){
	if( uin!=$('.chatbox').data('user_id') ){
		$('.chat_wall').html('');
	}
	$a = $('.contact_list>li[data-user_id="'+uin+'"]>a');
	$('.chatbox .user_nickname').text($a.find('>h3').text());
	$('.chatbox').data('user_id', $a.parent('li').data('user_id'));
	$('.chatbox').removeClass('hide');
	// 发送按钮保持宽度
	$('.chat_tool .btn_send').css({width:$('.chat_tool .btn_send').parent().width()});
	//
	if( type!='recieve' ){
		$('.chat_input_inner').focus();
	}

}

function disable_chat_input(){
	$('#input_area').attr('contenteditable', false);
	$('#input_area').css('background', '#eee url(static/img/loading-24-1.gif) center center no-repeat');
}
function enable_chat_input(){
	$('#input_area').attr('contenteditable', true);
	$('#input_area').css('background', '#fff');
	$('#input_area').focus();
}

function past_upload_pic(){
	$the = $('#input_area');
	G.uploadpic = {
		token : '<?=$this->H->getQNUploadToken()?>',
		callback : {
			before:function(){disable_chat_input();},
			error :function(){$the.attr('disabled', false);alert('图片上传失败');},
			success:function(src){
				enable_chat_input();
				$the.html($the.html()+'<img src="http://static.geeyan.com/'+src.key+'">');
			},
		},
	};

	/** 粘贴上传图片 **/
	document.getElementById("input_area").addEventListener('paste', function(e) {
	    if( typeof(G)=='undefined' || typeof(G.uploadpic)=='undefined' ){
	        return;
	    }
	    var clipboard = e.clipboardData;
	    for(var i=0,len=clipboard.items.length; i<len; i++) {
	        if(clipboard.items[i].kind == 'file' || clipboard.items[i].type.indexOf('image') > -1) {
	            var imageFile = clipboard.items[i].getAsFile();
	            var form = new FormData;
	            form.append('file', imageFile);
	            form.append('token', G.uploadpic.token);
	            form.append('key', 'images/project/tutu/pasteup/'+new Date().getTime());
	            var callback = G.uploadpic.callback || {before:function(){}, error:function(){}, success:function(data){}};
	            $.ajax({
	                url: 'http://up.qiniu.com/',
	                type: "POST",
	                data: form,
	                processData: false,
	                contentType: false,
	                beforeSend: callback.before,
	                error: callback.error,
	                success: callback.success,
	            })
	            e.preventDefault();
	        }
	    }
	});
}
</script>

<?php
include 'footer.php';
?>
