<?php
include 'header.php';
?>


<div class="title">
    <div class="fl"><?=$PARTNAME?></div>
</div>
<table class="table table-bordered table-striped table-condensed table-hover label-width-limited">
  <tr>
    <th>id</th>
    <th>姓名</th>
    <th>邮箱</th>
    <th>手机</th>
    <th>内容（点击展开）</th>
    <th>IP</th>
    <th>发布时间</th>
    <th>操作</th>
  </tr>

<?php
// 查询结果列表 @ 2014-07-19 15:11:26
include ROOT_PATH.'util/pagination.cls.php';
$where = "1";
$total = $this->db->val("SELECT count(*) FROM user_message WHERE $where");
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
    $sql = "SELECT *
        FROM user_message
        WHERE $where
        ORDER BY id DESC
        LIMIT $beg, $per";
    $res = $this->db->query($sql);
    while( $r=$this->db->r($res) ){
        extract($r);
        echo
        '<tr>
            <td>'.$id.'</td>
            <td>'.htmlspecialchars($name).'</td>
            <td><a href="mailto:'.htmlspecialchars($email).'" target="_blank">'.htmlspecialchars($email).'</a></td>
            <td>'.htmlspecialchars($phone).'</td>
            <td><span class="message_content">'.htmlspecialchars($content).'</span></td>
            <td>'.$ip.'</td>
            <td>'.dt($time_insert).'</td>
            <td>
                <a title="删除" onclick="if(confirm(\'确定删除吗？\')){window.location=\'panel/user-message?method=delete&id='.$id.'\';}" href="javascript:;" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>
            </td>
        </tr>';
    }
}
?>


</table>
<?=$pagination_html?>

<script type="text/javascript">
$(function(){
    $('.message_content').on('click', function(){
        $(this).toggleClass('expand');
    });
});
</script>

<?php
include 'footer.php';
?>
