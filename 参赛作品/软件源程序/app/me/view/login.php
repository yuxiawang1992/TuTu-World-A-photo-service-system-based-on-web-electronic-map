<?php
include 'header_v1.php';
?>


<form class="form-signin" role="form" method="post" action="me/login">
  <h2 class="form-signin-heading"><a href="<?=ROOT_URL?>"><?=_S('app_name')?></a> <small>个人中心</small></h2>
  <input type="text" class="form-control" name="username" placeholder="用户名" required autofocus>
  <input type="password" class="form-control" name="passwd" placeholder="密码" required>
  <div class="mb-10 ov-h">
	<div class="fl"><button class="btn btn-xs btn-success" onclick="return false;">注册新用户</button></div>
	<div class="fr"><button class="btn btn-xs btn-info" onclick="return false;">忘记密码？</button></div>
  </div>
  <button class="btn btn-lg btn-primary btn-block" type="submit">登录</button>
</form>


<?php
include 'footer_v1.php';
?>
