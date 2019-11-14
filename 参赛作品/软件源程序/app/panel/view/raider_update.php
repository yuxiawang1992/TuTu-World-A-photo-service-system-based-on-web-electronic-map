<?php
include 'header.php';
?>

<div class="title">
    <div class="fl"><?=$PARTNAME?></div>
</div>

<form class="form-horizontal" role="form" onsubmit="return goSubmit();" method="post" enctype="multipart/form-data">
   <div class="form-group">
      <label for="title" class="col-sm-2 control-label">标题</label>
      <div class="col-sm-10">
         <input type="text" class="form-control" id="title" name="title"
            placeholder="请输入标题" value="<?=$edit_data['title']?>">
      </div>
   </div>
   <div class="form-group">
      <label for="avatar" class="col-sm-2 control-label">图片</label>
      <div class="col-sm-6">
         <input type="file" class="form-control" id="avatar" name="avatar">
         <input type="hidden" id="avatar_js" name="avatar_js">
      </div>
      <div class="col-sm-4 js-avatar">

      </div>
   </div>
   <div class="form-group">
      <label for="content" class="col-sm-2 control-label">内容</label>
      <div class="col-sm-10">
         <script id="content" name="content" type="text/plain" style="width:100%;height:300px;"><?=$edit_data['content']?></script>
      </div>
   </div>
   <div class="form-group">
      <div class="col-sm-offset-2 col-sm-10">
         <button type="submit" class="btn btn-primary">提交</button>
      </div>
   </div>
</form>


<script type="text/javascript">
$(function(){

   // 如果有avatar
   <?php if( $edit_data['avatar']!='' ){ ?>
   setItemAvatar('<?=$edit_data['avatar']?>');
   <?php } ?>
});

function goSubmit(){
    return true;
}

function setItemAvatar(avatar){
   $('.js-avatar').html('<span>已上传图片：</span><a href="'+avatar+'" target="_blank"><img src="'+avatar+'" alt="已上传图片" height="30"></a>');
   $('#avatar_js').val(avatar);
}

</script>

<!-- 样式文件 -->
<link rel="stylesheet" href="<?=$UMEDITOR_ROOT_URL?>themes/default/css/umeditor.css">
<!-- 引用jquery -->
<script src="<?=$UMEDITOR_ROOT_URL?>third-party/jquery.min.js"></script>
<!-- 配置文件 -->
<script type="text/javascript" src="<?=$UMEDITOR_ROOT_URL?>umeditor.config.js"></script>
<!-- 编辑器源码文件 -->
<script type="text/javascript" src="<?=$UMEDITOR_ROOT_URL?>umeditor.js"></script>
<!-- 语言包文件 -->
<script type="text/javascript" src="<?=$UMEDITOR_ROOT_URL?>lang/zh-cn/zh-cn.js"></script>
<!-- 实例化编辑器代码 -->
<script type="text/javascript">
    $(function(){
        window.um = UM.getEditor('content', {
            /* 传入配置参数,可配参数列表看umeditor.config.js */
            // toolbar: ['undo redo | bold italic underline']
        });
    });
</script>

<?php
include 'footer.php';
?>
