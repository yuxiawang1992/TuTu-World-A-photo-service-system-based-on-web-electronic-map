<?php
if( !defined('DCCMS') ) die();
include 'header.php';
?>


<section class="ta-column-right in">
    <div class="title">
        <div class="fl"><?=$PARTNAME?></div>
        <div class="fr">
            <a href="<?=mod()?>/<?=act()?>?method=add" class="btn btn-primary btn-sm" style="color:#fff;">＋添加</a>
        </div>
    </div>
    <table class="table table-bordered table-striped table-condensed table-hover label-width-limited">
      <tr>
        <th>id</th>
        <th>名称</th>
        <th>描述（点击展开）</th>
        <th>封面</th>
        <th>状态</th>
        <th>更新时间</th>
        <th>操作</th>
      </tr>

    <?php
    
    $uid = $_SESSION['me']['id']+0;
    $total = MDL_Me_Album::getCount($uid);
    if( $total==0 ){
        echo '<tr><td colspan="7" align="center" style="color:#aaa;">暂无相册</td></tr>';
    }
    else{
        include ROOT_PATH.'util/pagination.cls.php';
        $page = $_GET['page'] ? intval($_GET['page']) : 1;
        $link = $BASE_URL.'?page={p}';
        $per = 10;
        $beg = ($page-1)*$per;
        $data_list = MDL_Me_Album::getList($uid, $beg, $per);
        $pagination = new Pagination($total, $page, $link, $per, 7);
        $pagination_html = $pagination->getHtml();
        $status_map = array(
            '0' => '自己可见',
            '1' => '好友可见',
            '2' => '完全公开',
        );
        
        foreach( $data_list as $r ){
            extract($r);
            if( !empty($avatar) ){
                $avatar = transfer_img_to_static($avatar);
                $avatar_html = '<a href="'.$avatar.'" target="_blank"><img src="'.$avatar.'/50.jpg" alt="相册封面" height="22"></a>';
            }
            else{
                $avatar_html = '';
            }

            echo
            '<tr>
                <td>'.$id.'</td>
                <td><span title="'.$name.'" style="font-size:12px;">'.sub_str($name, 15).'</span></td>
                <td><span class="content_ellipsis">'.$description.'</span></td>
                <td>'.$avatar_html.'</td>
                <td>'.$status_map[$status].'</td>
                <td>'.date('Y/m/d H:i', $time_update).'</td>
                <td>
                    <a title="编辑" href="'.mod().'/'.act().'?method=edit&id='.$id.'" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-edit"></span></a>
                    <a title="删除" onclick="if(confirm(\'确定删除吗？\')){window.location=\''.mod().'/'.act().'?method=delete&id='.$id.'\';}" href="javascript:;" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>
                </td>
            </tr>';
        }
    }
    ?>


    </table>
    <?=$pagination_html?>

</section>


<?php
include 'footer.php';
?>
