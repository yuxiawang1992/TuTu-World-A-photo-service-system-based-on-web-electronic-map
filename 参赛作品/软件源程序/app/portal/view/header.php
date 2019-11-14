<?php
if( !defined('DCCMS') ) die();
?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <base href="<?=ROOT_URL?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title><?=$this->act_name_ch?> - <?=$this->module_name_ch?></title>
    <link rel="shortcut icon" href="<?=$this->view_static?>favicon.ico">
    
    <link rel="stylesheet" href="static/css/bootstrap.min.css">
    <link rel="stylesheet" href="static/css/global.css?<?=_S('app_version')?>">
    <link rel="stylesheet" href="<?=$this->view_static?>css/portal.css?<?=_S('app_version')?>">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.0/html5shiv.min.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <script type="text/javascript">G={ROOT_URL:'<?=ROOT_URL?>',module:'<?=$this->module_name?>', act:'<?=$this->act_name?>', title:'<?=$this->act_name_ch?>', ckprefix:'<?=_S('cookie_prefix')?>', ckpath:'<?=_S('cookie_path')?>', __message:'<?=getSystemMessage()?>'};</script>
    <script src="static/js/jquery-1.8.3-min.js"></script>

</head>
<body class="ta-body module-<?=$this->module_name?> <?=$this->module_name.'-'.$this->act_name?>">


<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <ul class="nav nav-tabs">
                <li><img src="<?=$this->view_static?>images/logo.png" height="42"></li>
                <li data-label="share">
                    <a href="portal/share">照片共享</a>
                </li>
                <li data-label="retrieval">
                    <a href="portal/retrieval">信息检索</a>
                </li>
                <li data-label="path">
                    <a href="portal/path">路径查询</a>
                </li>
                <li data-label="map">
                    <a href="portal/map">地图管理</a>
                </li>
                <li>
                     <a href="me"><strong><span class="glyphicon glyphicon-user"></span> 个人中心</strong></a>
                </li>
            </ul>
        </div>
    </div>
</div>
