// 全局js
$(function(){
  // 
  $('.ta-background').css({'background-image':'url('+$('.ta-background').data('bg')+')', opacity:1});

  // 绑定popout 
  $('.ta-user-settings').on('click', function(){
    $('.popover.user-menu').toggleClass('hide');

  });

  // 修改nav
  if( G.act!='' ){
    $('.ta-navigator>ul>li>a[data-label]').removeClass('active');
    $('.ta-navigator>ul>li>a[data-label="'+G.act+'"]').addClass('active');
  }

  // 点击展开
  $('.content_ellipsis').on('click', function(){
      $(this).toggleClass('expand');
  });

  // 包裹以逗号分隔的元素
  $('.wrap-with-label').each(function(k, v){
      var ct = $(v).html(), res = '';
      $.each(ct.split(','), function(kk, vv){
          res += '<span class="badge">'+vv+'</span> ';
      });
      $(v).html(res);
  });


  // 有消息通知时，就显示出来
  G.__message && showHeadTip(G.__message);
});

