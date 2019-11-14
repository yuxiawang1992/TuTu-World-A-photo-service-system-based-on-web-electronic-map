<?php
include 'header.php';
?>


<div class="title">
    <div class="fl"><?=$PARTNAME?></div>
    <div class="fr">
        <a href="panel/raider?method=add" class="btn btn-primary btn-sm" style="color:#fff;">＋添加攻略</a>
    </div>
</div>
<table class="table table-bordered table-striped table-condensed table-hover label-width-limited">
  <tr>
    <th>id</th>
    <th>标题</th>
    <th>图片</th>
    <th>字数</th>
    <th>更新时间</th>
    <th>操作</th>
  </tr>

<?php
// 查询结果列表 @ 2014-07-19 15:11:26
include ROOT_PATH.'util/pagination.cls.php';
$where = "1";
$total = $this->db->val("SELECT count(*) FROM raider WHERE $where");
if( $total==0 ){
    echo '<tr><td colspan="10" align="center" style="color:#aaa;">暂无数据</td></tr>';
}
else{
    $page = $_GET['page'] ? intval($_GET['page']) : 1;
    $link = $BASE_URL.'?page={p}';
    $per = 20;
    $beg = ($page-1)*$per;
    $pagination = new Pagination($total, $page, $link, $per, 10);
    $pagination_html = $pagination->getHtml();
    $sql = "SELECT id, title, content, avatar, time_update
        FROM raider
        WHERE $where
        ORDER BY id DESC
        LIMIT $beg, $per";
    $res = $this->db->query($sql);
    while( $r=$this->db->r($res) ){
        extract($r);
        if( !empty($avatar) ){
            $avatar_html = '<a href="'.$avatar.'" target="_blank"><img src="'.$avatar.'" alt="已上传图片" height="22"></a>';
        }
        else{
            $avatar_html = '';
        }

        echo
        '<tr>
            <td>'.$id.'</td>
            <td><a title="编辑" href="panel/raider?method=edit&id='.$id.'">'.sub_str($title, 15).'</a></td>
            <td>'.$avatar_html.'</td>
            <td>约'.ceil(strlen(strip_tags($content))/3).'字</td>
            <td>'.date('Y/m/d H:i', $time_update).'</td>
            <td>
                <a title="编辑" href="panel/raider?method=edit&id='.$id.'" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-edit"></span></a>
                <a title="删除" onclick="if(confirm(\'确定删除吗？\')){window.location=\'panel/raider?method=delete&id='.$id.'\';}" href="javascript:;" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>
            </td>
        </tr>';
    }
}
?>


</table>
<?=$pagination_html?>

<script type="text/javascript">
$(function(){

});
</script>

<?php
include 'footer.php';
?>
