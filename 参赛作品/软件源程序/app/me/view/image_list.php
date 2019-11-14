<?php
if( !defined('DCCMS') ) die();
include 'header.php';
?>


<section class="ta-column-right in">
    <div class="title">
        <div class="fl"><?=$PARTNAME?></div>
        <div class="fr">
            <a href="<?=mod()?>/<?=act()?>?method=add" class="btn btn-primary btn-sm" style="color:#fff;">＋上传</a>
        </div>
    </div>
    <table class="table table-bordered table-striped table-condensed table-hover label-width-limited">
      <tr>
        <th>id</th>
        <th>名称</th>
        <th>预览</th>
        <th>标签</th>
        <th>描述（点击展开）</th>
        <th>所在相册</th>
        <th>地点（点选）</th>
        <th>更新时间</th>
        <th>操作</th>
      </tr>

    <?php
    
    $uid = $_SESSION['me']['id']+0;
    $total = MDL_Me_Image::getCount($uid);
    if( $total==0 ){
        echo '<tr><td colspan="9" align="center" style="color:#aaa;">暂无照片</td></tr>';
    }
    else{
        include ROOT_PATH.'util/pagination.cls.php';
        $page = $_GET['page'] ? intval($_GET['page']) : 1;
        $link = $BASE_URL.'?page={p}';
        $per = 10;
        $beg = ($page-1)*$per;
        $pagination = new Pagination($total, $page, $link, $per, 7);
        $pagination_html = $pagination->getHtml();
        $data_list = MDL_Me_Image::getList($uid, $beg, $per);
        $album_map = MDL_Me_Image::getAlbumMap($uid);
        $tags_map  = MDL_Me_Image::getTagsMap();
        // deb($album_map);
        foreach( $data_list as $r ){
            extract($r);
            if( !empty($avatar) ){
                $avatar = transfer_img_to_static($avatar);
                $avatar_html = '<a href="'.$avatar.'" target="_blank"><img src="'.$avatar.'/50.jpg" alt="照片封面" height="44"></a>';
            }
            else{
                $avatar_html = '';
            }

            $tags = '';
            foreach(explode(',', $tag_ids) as $v){
                $tags .= ','.$tags_map[$v];
            }
            $tags = substr($tags, 1);

            echo
            '<tr data-id="'.$id.'">
                <td>'.$id.'</td>
                <td><span title="'.$name.'" style="font-size:12px;">'.sub_str($name, 15).'</span></td>
                <td>'.$avatar_html.'</td>
                <td><span class="wrap-with-label">'.$tags.'</span></td>
                <td><span class="content_ellipsis" style="width:150px;">'.$description.'</span></td>
                <td><span class="d-ib" style="width:70px;">'.$album_map[$album_id].'</span></td>
                <td class="get_lnglat" data-lnglat="'.$longitude.','.$latitude.'">'.$position.'</td>
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

<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=PWCjaH3VM4H0lwcytimd1WWz"></script>
<script type="text/javascript">
$(function(){
    fresh_location();

    $('.get_lnglat').on('click', function(){
        var $sf = $(this);
        var city = $.trim($sf.text());
        var id = $sf.parents('tr[data-id]').data('id');
        var lnglat = $sf.data('lnglat');
        show_map_picker(id, city, lnglat, $sf);

    });

});

    

function show_map_picker(id, city, lnglat, $sf){
    $('.map_picker').remove();
    var modal_body_html = '<div id="allmap"></div>';
    $('body').append('<div class="dialog modal map_picker"><div class="modal-head"><div class="fl">取经纬度，鼠标滚轮缩放，单击完成选择</div><div class="fr">×</div></div><div class="modal-body">'+modal_body_html+'</div></div></div>');
    
    var init_zoom = {
        longitude   : 104,
        latitude    : 32,
        zoom        : 5,
    };
    if( lnglat!=',' ){
        init_zoom = {
            longitude   : lnglat.split(',').shift(),
            latitude    : lnglat.split(',').pop(),
            zoom        : 11,
        };
    }

    // 百度地图API功能
    map = new BMap.Map("allmap");                        // 创建Map实例
    // 初始化地图,设置中心点坐标和地图级别
    map.centerAndZoom(new BMap.Point(init_zoom.longitude, init_zoom.latitude), init_zoom.zoom);
    map.addControl(new BMap.NavigationControl());               // 添加平移缩放控件
    map.addControl(new BMap.ScaleControl());                    // 添加比例尺控件
    map.addControl(new BMap.OverviewMapControl());              //添加缩略地图控件
    map.enableScrollWheelZoom();                            //启用滚轮放大缩小
    map.addControl(new BMap.MapTypeControl());          //添加地图类型控件
    map.setCurrentCity(city);          // 设置地图显示的城市 此项是必须设置的

    map.addEventListener("click",function(e){
        var lnglat = e.point.lng+','+e.point.lat;
        remove_map_picker();
        showHeadTip('已取到经纬度：'+lnglat+'，正在上传..');
        setTimeout(function(){
            var params = {
                method      : 'put_image_lnglat',
                id          : id,
                lnglat      : lnglat,
            };
            $.get(G.ROOT_URL+'me/image', params, function(d){
                showHeadTip(d);
                $sf.data('lnglat', lnglat);
                fresh_location();
            });
        }, 300);
    });

    $('.map_picker .modal-head .fr').on('click', function(){
        remove_map_picker();
    });
}

function remove_map_picker(){
    $('.map_picker').remove();
    delete map;
}

function fresh_location(){
    $('.get_lnglat').each(function(k, v){
        if( $(v).data('lnglat')!=',' ){
            $(v).html('<span title="已确定经纬度：'+$(v).data('lnglat')+'"><span class="icon icon-location"></span>'+$.trim($(v).text())+'</span>');
        }
    });
}

</script>

<?php
include 'footer.php';
?>
