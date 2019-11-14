<?php
include 'header.php';
?>

<div class="title">
    <div class="fl"><?=$this->act_name_ch?></div>
</div>
<table class="table table-bordered table-striped table-hover">
  <tr>
    <th>变量名</th>
    <th>变量值</th>
    <th>描述</th>
    <th>操作</th>
  </tr>
  <?php
  $res = $this->db->query("SELECT `meta`, `value`, `description`
    FROM `config`
    ORDER BY `meta`");
  while( extract($this->db->r($res)) ){
  	echo '<tr data-meta="'.$meta.'">';
  	echo '<td>'.$meta.'</td>';
  	echo '<td class="td-value"><span title="'.$value.'">'.sub_str($value, 80).'</span></td>';
    echo '<td>'.($description?:'<span style="color:#aaa;">暂无描述</span>').'</td>';
  	echo '<td>
  			<a title="编辑" href="javascript:editConfiguration(\''.$meta.'\');" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-edit"></span></a>
          </td>';
  	echo '</tr>';
  }
  ?>
</table>


<!-- Modal -->
<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">修改</h4>
      </div>
      <div class="modal-body">
        <textarea class="form-control dv-textarea" rows="5"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-primary dv-submit" data-loading-text="操作中..">提交</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script type="text/javascript">
$(function(){
	$('.dv-submit').on('click', function(){
		var $sf = $(this);
		var ct = $.trim($('.dv-textarea').val());
		if( ct=='' ){
			return;
		}
		$sf.button('loading');
		$.post('panel/configuration', {method:'edit_config_value', meta:G.meta, value:ct}, function(d){
			$sf.button('reset');
			if( d=='ok' ){
				$('tr[data-meta="'+G.meta+'"] td.td-value').text(ct);
				$('#myModal').modal('hide');
				showHeadTip('修改成功！');
        setTimeout(function(){
          window.location.reload();
        }, 700);
			}
			else{
				alert(d);
			}
		});
	});
});

function editConfiguration(meta){
	G.meta = meta;
	$('#myModal').modal();
	$('#myModalLabel').html('修改'+meta+'的变量值');
	$('.dv-textarea').val($('tr[data-meta="'+meta+'"] td.td-value span').attr('title'));

}
</script>

<?php
include 'footer.php';
?>
