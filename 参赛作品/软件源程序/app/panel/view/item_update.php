<?php
include 'header.php';
?>

<div class="title">
    <div class="fl"><?=$PARTNAME?></div>
</div>

<blockquote>
为了提高大家录入数据的效率，后台开发哥哥开发了粘贴链接得到其他信息的功能，你<strong>只需要将商品链接粘贴到第一个输入框</strong>里，后面的大多数输入框将会为你自动补全。
<br>
（支持以<!-- http://detail.tmall.com/、 -->http://ai.taobao.com/开头的链接）
<small>爱你们的开发哥哥</small>
</blockquote>

<form class="form-horizontal" role="form" onsubmit="return goSubmit();" method="post" enctype="multipart/form-data">
   <div class="form-group">
      <label for="link" class="col-sm-2 control-label">链接</label>
      <div class="col-sm-10">
         <input type="text" class="form-control" id="link" name="link"
            placeholder="请输入商品链接，如：<?=ROOT_URL?>" value="<?=$edit_data['link']?>">
      </div>
   </div>
   <div class="form-group">
      <label for="tags" class="col-sm-2 control-label">标签</label>
      <div class="col-sm-10">
         <input type="hidden" class="form-control" id="tags" name="tags" value="<?=$edit_data['tags']?>">
         <span id="tag_box" class="tags"><?=$edit_data['tags_html']?></span>
      </div>
   </div>
   <div class="form-group">
      <label for="spread" class="col-sm-2 control-label">推广力度</label>
      <div class="col-sm-10">
         <input type="text" class="form-control" id="spread" name="spread" value="<?=$edit_data['spread']?>" maxlength="3" placeholder="0-100，值越高，商品匹配列表越靠前">
      </div>
   </div>
   <div class="form-group">
      <label for="title" class="col-sm-2 control-label">标题</label>
      <div class="col-sm-10">
         <input type="text" class="form-control" id="title" name="title"
            placeholder="请输入标题" value="<?=$edit_data['title']?>">
      </div>
   </div>
   <div class="form-group">
      <label for="price" class="col-sm-2 control-label">价格</label>
      <div class="col-sm-10">
         <input type="text" class="form-control" id="price" name="price" maxlength="10" placeholder="请输入价格，如：120.8" value="<?=$edit_data['price']?>">
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
   <?php /*
   <div class="form-group">
      <label for="description" class="col-sm-2 control-label">描述</label>
      <div class="col-sm-10">
         <input type="text" class="form-control" id="description" name="description"
            placeholder="请输入商品描述，50字以内" value="<?=$edit_data['description']?>">
      </div>
   </div>
   */
   ?>

   <div class="form-group">
      <label for="score" class="col-sm-2 control-label">第三方评分</label>
      <div class="col-sm-10">
         <input type="text" class="form-control" id="score" name="score" maxlength="3" placeholder="将作为排序参数，如：4.8" value="<?=$edit_data['score']?>">
      </div>
   </div>
   <div class="form-group">
      <label for="sales" class="col-sm-2 control-label">第三方销量</label>
      <div class="col-sm-10">
         <input type="text" class="form-control" id="sales" name="sales" maxlength="10" placeholder="将作为排序参数，如：1230" value="<?=$edit_data['sales']?>">
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
   setItemAvatar('<?=$edit_data['avatar']?>');
   <?php } ?>

   // 绑定链接输入框的onchange事件，自动抓取
   $('#link').on('change', function(){
      var url = $('#link').val();
      if( url.indexOf('http://ai.taobao.com/')!==-1 ){
         var params = {
            method : 'curl_item_detail',
            url    : url,
         };
         // 禁用其他输入框（防止异步抓取数据后，用户输入被清除）
         disableInputs($('form'));
         // 发送ajax请求到指定url并带上参数，返回json数据并填充input
         $.get('panel/ajax', params, function(res_data){
            if( typeof(res_data)=='object' && res_data.title!='' ){
               // 针对返回的数据，开始遍历其他填写输入框
               $.each(res_data, function(k, v){
                  if( k=='avatar' ){
                     setItemAvatar(v);
                     return;
                  }
                  $('#'+k).val(v);
               });
            }
            else{
               showHeadTip('出错啦：暂时无法自动获取数据');
            }
            // 填充标签，启用其他输入框，管理员可修正数据
            setRandTags();
            enableInputs($('form'));
         }, 'json');
      }
      else{
         showHeadTip('此链接未匹配自动抓取..');
      }
   });
});

function goSubmit(){
   if( $('#title').val()=='' ){
      showHeadTip("标题不能为空", 'error');
      return false;
   }
   else if( $('#link')=='' ){
      showHeadTip('链接不能为空', 'error');
      return false;
   }
   else if( $('#tags').val()=='' ){
      showHeadTip('至少添加一个标签', 'error');
      return false;
   }
   else if( $('#spread').val()==''
         || $('#spread').val()<0
         || $('#spread').val()>100 ){
      showHeadTip('推广力度应该在0-100之间', 'error');
      return false;
   }
   else if( $('#price').val()=='' ){
      showHeadTip('价格不能为空', 'error');
      return false;
   }
   return true;
}

function setItemAvatar(avatar){
   $('.js-avatar').html('<span>已上传图片：</span><a href="'+avatar+'" target="_blank"><img src="'+avatar+'" alt="已上传图片" height="30"></a>');
   $('#avatar_js').val(avatar);
}

function setRandTags(){
   var tag_list = [];
   var tag_total = $('#tag_box span').length;
   $('#tag_box span').each(function(k, v){
      if( Math.random()*tag_total>9 ){
         tag_list.push($(v).data('key'));
      }
   });
   setTags('#tag_box', '0,'+tag_list.join(','), 'key');
   $('#tags').val(getTags('#tag_box', 'key'));

}
</script>

<?php
include 'footer.php';
?>
