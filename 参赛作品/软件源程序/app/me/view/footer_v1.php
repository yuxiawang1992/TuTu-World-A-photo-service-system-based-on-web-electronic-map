<?php
if( !defined('DCCMS') ) die();
?>
    </div> <!-- /container -->

<?php if( !in_array($this->act_name, $this->no_login) ){ ?>
	<div id="footer">
      <div class="container">
        <p class="text-muted"><?=_S('app_name_en').'('._S('app_name').')'?>管理端, 当前版本：<?=_S('app_version')?><!-- , 页面运行时间：<?=runTime()?>s --></p>
      </div>
    </div>
<?php } ?>


<script src="static/js/global.js?<?=_S('app_version')?>"></script>
<script src="<?=$this->view_static?>js/me.js?v=<?=_S('app_version')?>"></script>
<script src="<?=$this->view_static?>js/bootstrap.min.js"></script>
<script type="text/javascript">
$(function($){
	// 添加导航事件样式
	$('.navbar .navbar-nav a').each(function(k, v){
		if( $(this).text()==G.title ){
			$(this).parent('li').addClass('active');
			$(this).parents('ul.navbar-nav li').addClass('active');
			return false;
		}
	});
});
</script>
    <!-- Placed at the end of the document so the pages load faster -->
  </body>
</html>
