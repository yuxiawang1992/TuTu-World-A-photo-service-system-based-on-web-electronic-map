<?php
if( !defined('DCCMS') ) die();
include 'header.php';
?>

<section class="ta-column-right in">
    <div class="title">
        <div class="fl"><?=$handler_name?>照片</div>
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
         <input type="text" class="form-control" id="name" name="name" value="<?=$edit_data['name']?>" maxlength="100" placeholder="照片的名称">
      </div>
   </div>
   <div class="form-group">
      <label for="description" class="col-sm-2 control-label">描述</label>
      <div class="col-sm-10">
         <input type="text" class="form-control" id="description" name="description"
            placeholder="请输入对照片的简要描述，50字以内" value="<?=$edit_data['description']?>">
      </div>
   </div>
   <div class="form-group">
      <label for="avatar" class="col-sm-2 control-label">照片</label>
      <div class="col-sm-6">
         <input type="file" class="form-control" id="avatar" name="avatar">
         <input type="hidden" id="avatar_js" name="avatar_js">
      </div>
      <div class="col-sm-4 js-avatar">

      </div>
   </div>

   <div class="form-group">
      <label for="position" class="col-sm-2 control-label">地点</label>
      <div class="col-sm-10">
         <input type="text" class="form-control" id="position" name="position" value="<?=$edit_data['position']?>" maxlength="100" placeholder="照片的拍摄地点">
      </div>
   </div>
   <div class="form-group">
      <label for="album_id" class="col-sm-2 control-label">相册</label>
      <div class="col-sm-10">
         <select name="album_id" id="album_id" class="selectpicker">
         <?php
            $album_list = MDL_Me_Image::getAllAlbumList($uid);
            $str = '';
            foreach( $album_list as $v ){
               $str .= '<option value="'.$v['id'].'">'.$v['name'].'</option>';
            }
            echo $str;
         ?>
         </select>
      </div>
   </div>

   <div class="form-group">
      <label for="tag_ids" class="col-sm-2 control-label">标签</label>
      <div class="col-sm-10">
         <input type="hidden" class="form-control" id="tag_ids" name="tag_ids" value="<?=$edit_data['tag_ids']?>">
         <span id="tag_box" class="tags"><?=$edit_data['tags_html']?></span>
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
   setTags('#tag_box', $('#tag_ids').val(), 'key');
   // 点选标签时交互
   $('#tag_box span').on('click', function(){
      var $sf = $(this);
      $sf.toggleClass('active');
      $('#tag_ids').val(getTags('#tag_box', 'key'));
   });
   
   // 如果有avatar
   <?php if( $edit_data['avatar']!='' ){ ?>
   setItemAvatar('<?=transfer_img_to_static($edit_data['avatar'])?>');
   <?php } ?>
   $('#album_id').val('<?=$edit_data['album_id']?>');
});

function goSubmit(){
   if( $('#name').val()=='' ){
      showHeadTip("照片名称字数应在0-100之间", 'error');
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
