<?php
if( !defined('DCCMS') ) die();
include 'header.php';
?>


<section class="ta-column-right in">
	<div class="statistic_wrapper">
		
		<div class="row">
			<div class="col-lg-6">
				<div class="panel panel-default">
				  	<div class="panel-heading">
				  		欢迎<?=$_SESSION['me']['nickname']?>，登录信息：
				  	</div>
				    <ul class="list-group">
						<li class="list-group-item">
							<strong>此次登录于</strong><span class="inner_data editable"><?=dt(dt($_SESSION['me']['time_update']))?></span>
						</li>
						<li class="list-group-item">
							<strong>此次登录IP</strong><span class="inner_data editable"><?=$_SESSION['me']['login_ip']?></span>
						</li>
						<li class="list-group-item">
							<strong>上次登录于</strong><span class="inner_data editable"><?=dt(dt($_SESSION['me']['last_time_update']))?></span>
						</li>
						<li class="list-group-item">
							<strong>上次登录IP</strong><span class="inner_data editable"><?=$_SESSION['me']['last_login_ip']?></span>
						</li>
					</ul>
				</div>

				<div class="panel panel-default">
				  	<div class="panel-heading">
				  		账号信息：
				  		<button class="btn btn-success btn-xs fr"><span class="glyphicon glyphicon-edit"></span> 编辑</button>
				  		<button class="btn btn-primary btn-xs fr hide"><span class="glyphicon glyphicon-ok"></span> 提交</button>
				  	</div>
				    <ul class="list-group">
						<li class="list-group-item">
							<strong>图图号</strong><span class="inner_data"><?=$_SESSION['me']['id']?></span>
						</li>
						<li class="list-group-item">
							<strong>登录名</strong><span class="inner_data editable"><?=$_SESSION['me']['username']?></span>
						</li>
						<li class="list-group-item">
							<strong>昵　称</strong><span class="inner_data editable"><?=$_SESSION['me']['nickname']?></span>
						</li>
						<li class="list-group-item">
							<strong>头　像</strong><span class="inner_data"><a href="<?=get_cdn_url($_SESSION['me']['avatar'])?>" target="_blank"><img src="<?=get_cdn_url($_SESSION['me']['avatar'])?>/25.jpg" height="22"></a></span>
						</li>
					</ul>
				</div>

			</div>

			<div class="col-lg-6">
				<div class="panel panel-default">
				  	<div class="panel-heading">
				  		设置背景图
				  	</div>
				    <ul class="list-group">
						<li class="list-group-item">
							<?php
								for($i=0; $i<=7; $i++){
									$src = get_cdn_url('static/images/me-background/bg'.$i.'.jpg');
									echo '<a href="javascript:;" class="bg_a_wrap"><img class="img-thumbnail" data-src="'.$src.'" src="'.$src.'/150.jpg" width="140" height="140"></a>';
								}
							?>
						</li>
					</ul>
				</div>

			</div>
		</div>
	</div>
</section>



<script type="text/javascript">
$(function(){
	$('.statistic_wrapper>.row>.col-lg-6').css({height:$('.ta-column-right').height()-40});

	$('.bg_a_wrap').on('click', function(){
		var src = $(this).find('img').data('src');
		ck('me_bg', src);
		$('.ta-background').css({opacity:1, 'background-image':'url('+src+')'});
	});
});
</script>


<?php
include 'footer.php';
?>
