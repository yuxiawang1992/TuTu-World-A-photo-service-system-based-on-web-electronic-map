<!DOCTYPE html>
<html lang="zh-cn">
  <head>
    <meta charset="utf-8">
    <base href="<?=ROOT_URL?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?=$this->act_name_ch?> - <?=$this->module_name_ch?></title>
    <link rel="shortcut icon" href="<?=ROOT_URL?>favicon.ico">
    <!-- Bootstrap core CSS -->
    <link href="<?=$this->view_static?>css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?=$this->view_static?>css/global.css?v=<?=_S('app_version')?>" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.0/html5shiv.min.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <script type="text/javascript">G={module:'<?=$this->module_name?>', act:'<?=$this->act_name?>', title:'<?=$this->act_name_ch?>', ckprefix:'<?=_S('cookie_prefix')?>', ckpath:'<?=_S('cookie_path')?>', __message:'<?=getSystemMessage()?>'};</script>
    <script src="<?=ROOT_URL?>static/js/jquery-1.10.2.js"></script>
  </head>

  <body class="module-<?=$this->module_name?> <?=$this->module_name.'-'.$this->act_name?>">

    <?php if( !in_array($this->act_name, $this->no_login) ){ ?>
    <!-- Fixed navbar -->
    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
      <!-- <div class="brand"><a class="navbar-brand" href="javascript:;"><?=APP_NAME?></a></div> -->
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>

        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <?php foreach( $this->nav_list as $k=>$v ){
              if( in_array($k, $this->no_login) ){
                continue;
              }
              ?>
            <li><a href="panel/<?=strtr($k, '_', '-')?>"><?=$v?></a></li>
            <?php } ?>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="panel/logout">退出</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>
    <?php } ?>

    <div class="container">
