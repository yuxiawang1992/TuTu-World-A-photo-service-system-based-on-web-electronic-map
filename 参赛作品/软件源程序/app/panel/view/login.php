<?php
include 'header.php';
?>

<form class="form-signin" role="form" method="post" action="panel/login">
  <h2 class="form-signin-heading"><?=_S('app_name_en')?>'s Management</h2>
  <input type="text" class="form-control" name="username" placeholder="Username" required autofocus>
  <input type="password" class="form-control" name="passwd" placeholder="Password" required>
  <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
</form>

<script type="text/javascript">
var login_msg = '<?=$login_msg?>';
login_msg=='' || alert(login_msg);
</script>

<?php
include 'footer.php';
?>
