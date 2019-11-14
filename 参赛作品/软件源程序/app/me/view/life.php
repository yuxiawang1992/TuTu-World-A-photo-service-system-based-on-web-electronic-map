<?php
if( !defined('DCCMS') ) die();
include 'header.php';
?>


<section class="ta-column-right in">
	<div class="statistic_wrapper">
		<div class="row">
			<div class="col-lg-4">
				<h3>相册数量统计</h3>
				<ul class="list-group">
					<li class="list-group-item">
						总相册数量
						<span class="badge"><?=Mdl_Me_Life::getAlbumCount($this->uid)?></span>
					</li>
					<li class="list-group-item">
						私人相册数
						<span class="badge"><?=Mdl_Me_Life::getAlbumCount($this->uid, array(0))?></span>
					</li>
					<li class="list-group-item">
						朋友可见相册数
						<span class="badge"><?=Mdl_Me_Life::getAlbumCount($this->uid, array(1))?></span>
					</li>
					<li class="list-group-item">
						公开相册数
						<span class="badge"><?=Mdl_Me_Life::getAlbumCount($this->uid, array(2))?></span>
					</li>
				</ul>
				<h3>照片数量统计</h3>
				<ul class="list-group">
					<li class="list-group-item">
						照片总数
						<span class="badge"><?=Mdl_Me_Life::getImageCount($this->uid)?></span>
					</li>
					<li class="list-group-item">
						私人照片数
						<span class="badge"><?=Mdl_Me_Life::getImageCount($this->uid, array(0))?></span>
					</li>
					<li class="list-group-item">
						朋友可见照片数
						<span class="badge"><?=Mdl_Me_Life::getImageCount($this->uid, array(1))?></span>
					</li>
					<li class="list-group-item">
						公开照片数
						<span class="badge"><?=Mdl_Me_Life::getImageCount($this->uid, array(2))?></span>
					</li>
				</ul>
				<h3>好友信息统计</h3>
				<ul class="list-group">
					<li class="list-group-item">
						好友总数
						<span class="badge"><?=Mdl_Me_Life::getFriendCount($this->uid)?></span>
					</li>
					<li class="list-group-item">
						发送聊天消息数
						<span class="badge"><?=Mdl_Me_Life::getChatSendCount($this->uid)?></span>
					</li>
					<li class="list-group-item">
						收到聊天消息数
						<span class="badge"><?=Mdl_Me_Life::getChatRecieveCount($this->uid)?></span>
					</li>
				</ul>
			</div>

	
			<div class="col-lg-4">
				<h3>相片按地点统计</h3>
				<ul class="list-group">
					<?php
$distinct_position = Mdl_Me_Life::getImageDistinctPosition($this->uid);
$final = '';
foreach ($distinct_position as $v){
	$final .= '<li class="list-group-item">
					'.sub_str($v['position'], 15).'
					<span class="badge">'.$v['count'].'</span>
				</li>';
}
echo $final;
					?>
				</ul>
			</div>

	
			<div class="col-lg-4">
				<h3>相片按标签统计</h3>
				<ul class="list-group">
					<?php
$distinct_posiiton = Mdl_Me_Life::getImageDistinctTag($this->uid);
$final = '';
foreach ($distinct_posiiton as $v){
	$final .= '<li class="list-group-item">
					'.sub_str($v['name'], 15).'
					<span class="badge">'.$v['count'].'</span>
				</li>';
}
echo $final;
					?>
				</ul>
			</div>

		</div>
	</div>
</section>



<script type="text/javascript">
$(function(){
	$('.statistic_wrapper>.row>.col-lg-4').css({height:$('.ta-column-right').height()-40});
});
</script>

<?php
include 'footer.php';
?>
