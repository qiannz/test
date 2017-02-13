{{include file='admin/header.php'}}
<script type="text/javascript" src="/js/jquery.form.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript">
$(function(){	
	$("#user_info").focus(function(){
		$('.field_notice').removeClass('error').html('');
	});
	
	$(".formbtn").click(function(){
		var _this = $(this);
		_this.val('查询中。。。').attr('disabled', true);
		$(".info").html('');
		$.ajax({
			type: "POST",
			url: "/admin/shop/check-staff-name",
			data: "sid=" + $('#sid').val() + "&user_info=" + $('#user_info').val() + "&uid=" + $('#uid').val(),
			dataType : 'json',
			success: function(obj) {
				_this.val('查询').attr('disabled', false);
				if(obj.res == 100) {
					$(".info").html(obj.extra);
				} else if(obj.res == 101) {
					$('.field_notice').addClass('error').html('请输入用户名或手机号码');
					return false;
				}else if(obj.res == 200) {
					$('.field_notice').addClass('error').html('请输入正确的用户名或手机号码');
					return false;
				} else if(obj.res == 300) {
					$('.field_notice').addClass('error').html('此用户已经是他店店员了，请换一个用户名或手机号码');
					return false;
				} else if(obj.res == 400) {
					$('.field_notice').addClass('error').html('此用户已经是本店店员了，请换一个用户名或手机号码');
					return false;
				}
			}
		});		
	});
	
});
</script>

<div id="rightTop">
  <p>店铺管理</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/shop/list/page:{{$page}}">店铺列表</a></li>
    <li><a class="btn1" href="/admin/shop/staff-management/sid:{{$sid}}/page:{{$page}}">店员管理</a></li>
    <li><span>{{if $row}}编辑{{else}}新建{{/if}}店员</span></li>
  </ul>
</div>
<div class="mrightTop">
<form method="POST" action="{{$_CONF.FORM_ACTION}}" id="form">
  <div class="fontl">
    <input type="hidden" name="page" id="page" value="{{$page}}" />
    <input type="hidden" name="sid" id="sid" value="{{$sid}}" />
    <div class="left">
        用户名 / 手机号码：
        <input class="queryInput" type="text" name="user_info" id="user_info" value="" style="width:200px" />
        <input type="button" class="formbtn" value="查询" />
        <label class="field_notice"></label>
    </div>
  </div>
</form>
</div>

<div class="info"></div>
{{include file='admin/footer.php'}}