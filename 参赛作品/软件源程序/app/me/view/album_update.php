<?php
if( !defined('DCCMS') ) die();
include 'header.php';
?>

<section class="ta-column-right in">
    <div class="title">
        <div class="fl"><?=$handler_name?>相册</div>
    </div>

<form class="form-horizontal" action="<?=mod().'/'.act()?>" role="form" onsubmit="return goSubmit();" method="post" enctype="multipart/form-data">
   <?php
   if( !empty($id) ){
      echo '<input type="hidden" name="id" value="'.$id.'">';
   }
   ?>
   <div class="form-group">
      <label for="name" class="col-sm-2 control-label">名称</label>
      <div class="col-sm-10">
         <input type="text" class="form-control" id="name" name="name" value="<?=$edit_data['name']?>" maxlength="100" placeholder="相册的名称">
      </div>
   </div>
   <div class="form-group">
      <label for="description" class="col-sm-2 control-label">描述</label>
      <div class="col-sm-10">
         <input type="text" class="form-control" id="description" name="description"
            placeholder="请输入对相册的简要描述，50字以内" value="<?=$edit_data['description']?>">
      </div>
   </div>
   <div class="form-group">
      <label for="avatar" class="col-sm-2 control-label">封面</label>
      <div class="col-sm-6">
         <input type="file" class="form-control" id="avatar" name="avatar">
         <input type="hidden" id="avatar_js" name="avatar_js">
      </div>
      <div class="col-sm-4 js-avatar">

      </div>
   </div>

   <div class="form-group">
      <label for="status" class="col-sm-2 control-label">状态</label>
      <div class="col-sm-10">
         <select name="status" id="status" class="selectpicker">
            <option value="0">仅自己可见</option>
            <option value="1">好友可见</option>
            <option value="2">完全公开</option>
         </select>
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
   // 初始化选中标签
   setTags('#tag_box', $('#tags').val(), 'key');
   // 点选标签时交互
   $('#tag_box span').on('click', function(){
      var $sf = $(this);
      $sf.toggleClass('active');
      $('#tags').val(getTags('#tag_box', 'key'));
   });
   // 如果有avatar
   <?php if( $edit_data['avatar']!='' ){ ?>
   setItemAvatar('<?=transfer_img_to_static($edit_data['avatar'])?>');
   <?php } ?>
   $('#status').val('<?=$edit_data['status']?>');
});

function goSubmit(){
   if( $('#name').val()=='' ){
      showHeadTip("相册名称字数应在0-100之间", 'error');
      $('#name').focus();
      return false;
   }
   return true;
}

function setItemAvatar(avatar){
   $('.js-avatar').html('<span>已上传图片：</span><a href="'+avatar+'" target="_blank"><img src="'+avatar+'/50.jpg" alt="已上传图片" height="30"></a>');
   $('#avatar_js').val(avatar);
}
</script>

<?php
include 'footer.php';
?>
