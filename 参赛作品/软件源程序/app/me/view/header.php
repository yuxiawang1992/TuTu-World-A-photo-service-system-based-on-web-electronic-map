<?php
if( !defined('DCCMS') ) die();
?><!DOCTYPE html>
<html slick-uniqueid="3">
<head>
    <meta charset="utf-8">
    <base href="<?=ROOT_URL?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title><?=$this->act_name_ch?> - <?=$this->module_name_ch?></title>
    <link rel="shortcut icon" href="<?=$this->view_static?>favicon.ico">
    
    <link rel="stylesheet" href="<?=$this->view_static?>css/bootstrap.min.css">
    <link rel="stylesheet" href="<?=$this->view_static?>css/teambition.css">
    <link rel="stylesheet" href="<?=$this->view_static?>css/main.css">
    <link rel="stylesheet" href="static/css/global.css?<?=_S('app_version')?>">
    <link rel="stylesheet" href="<?=$this->view_static?>css/me.css?<?=_S('app_version')?>">
    <script type="text/javascript">G={ROOT_URL:'<?=ROOT_URL?>',module:'<?=$this->module_name?>', act:'<?=$this->act_name?>', title:'<?=$this->act_name_ch?>', ckprefix:'<?=_S('cookie_prefix')?>', ckpath:'<?=_S('cookie_path')?>', __message:'<?=getSystemMessage()?>'};</script>
    <script src="static/js/jquery-1.8.3-min.js"></script>

</head>
<body class="ta-body module-<?=$this->module_name?> <?=$this->module_name.'-'.$this->act_name?>">

    <div class="ta-background" data-bg="<?=( ck('me_bg') ? ck('me_bg') : get_cdn_url('static/images/me-background/bg'.rand(0,7).'.jpg') )?>"></div>
    <div class="ta-shadow-layer"></div>
    <section class="ta-app-wrapper">
        <section class="ta-column-left in">
            <header class="ta-app-header">
                <h1 class="ta-title">Today</h1>
                <div class="ta-info">
                    <h1 class="logo"><a href="<?=ROOT_URL?>"><?=_S('app_name')?></a></h1>
                </div>
                <div class="ta-greeting">
                    <p><?=$this->H->getPartOfToday()?>好, <span data-lang-text="greetingViewTodayIs">今天是</span><?=date('m月d日', time())?>，周<?=$this->H->getWeekName()?><span data-lang-text="greetingViewPeriod">。</span></p>
                </div>
                <nav class="ta-navigator">
                <ul>
                    <li><a href="<?=ROOT_URL?>me" class="ta-show-today-content-handler push-state ui-droppable<?=$this->act_name=='index'?' active':''?>" data-gta="event" data-label="index"><span class="icon icon-home"></span><span data-lang-text="AppViewNavToday">个人主页</span></a></li>
                    <li><a href="me/album" class="ta-show-future-content-handler push-state ui-droppable" data-gta="event" data-label="album"><span class="icon icon-library"></span><span data-lang-text="AppViewNavFuture">我的相册</span></a></li>
                    <li><a href="me/image" class="ta-show-past-content-handler push-state ui-droppable" data-gta="event" data-label="image"><span class="icon icon-image"></span><span data-lang-text="AppViewNavPast">我的照片</span></a></li>
                    <hr>
                    <li><a href="me/friend" class="ta-show-discover-content-handler push-state" data-gta="event" data-label="friend"><span class="icon icon-user"></span><span data-lang-text="AppViewNavDiscover">我的好友</span></a></li>
                    <li><a href="me/life" class="ta-show-apps-content-handler push-state" data-gta="event" data-label="life"><span class="icon icon-th-stroke"></span><span data-lang-text="AppViewNavApps">快捷生活</span></a></li>
                    </ul>
                </nav>
            </header>
            <footer class="ta-footer">
                <div class="ta-user-settings pull-left">
                    <a class="ta-user-menu-toggler" data-gta="event" data-label="user menu toggler">
                        <span class="ta-user-avatar img-36 img-circle pull-left" style="background-image: url('<?=transfer_img_to_static($_SESSION['me']['avatar'])?>');"></span>
                        <span class="ta-user-name"><?=$_SESSION['me']['nickname']?></span>
                        <span class="icon icon-chevron-up"></span>
                    </a>
                </div>
                <hr class="ta-footer-divide-line">
                <a class="ta-hint-toggler fade"></a>
                <a class="ta-helper-handler" data-gta="event" data-label="feedback" data-lang-text="AppViewFeedback">开发制作：武汉大学 资源与环境科学学院 SuperGIS 超图 图图世界开发组</a>
            </footer>

            <div class="popover user-menu top hide" style="display: block;">
                <div class="arrow"></div>
                <div class="popover-content">
                    <ul class="list">
                        <li class="user-info"><h4><?=$_SESSION['me']['nickname']?></h4><p><?=$_SESSION['me']['username']?></p></li>
                        <!-- <li class="language-selector"><h4 data-lang-text="userMenuLanguage">语言</h4><div><button class="btn btn-primary change-language-trigger" data-lang="zh" data-lang-text="userMenuChinese">中文版</button><button class="btn  change-language-trigger" data-lang="en" data-lang-text="userMenuEnglish">English</button></div></li> -->
                        <li><a href="me/logout" class="user-logout" data-gta="event" data-label="user menu|user logout"><span class="icon icon-off"></span><span data-lang-text="userMenuLogout">退出</span></a></li>
                    </ul>
                </div>
            </div>
        </section>


