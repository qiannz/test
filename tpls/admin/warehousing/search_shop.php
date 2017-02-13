{{include file='admin/header.php'}}
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript">

function checkUser()
{
	var user_name = $('#uname').val();
	if (user_name == '') {
		$('#ts').html('请输入用户名！');
    	return false;
	}
	$.post('/admin/ticket/check-user-shop', {user_name:user_name}, function(data){
		if (data == 'ok') {
			$('#ts').html('此用户不是商家认证用户，不能添加券');
			$("#table").remove();
			return false;
		}else {
			$("#form").submit();
		}
	});
	return true;
}


</script>

<div id="rightTop">
  <p>仓储管理</p>
  <ul class="subnav">
    <li><a class="btn4" href="/admin/batch/list">入库记录</a></li>   
    <li><span>店铺选择</span></li>
  </ul>
</div>
<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="{{$_CONF.FORM_ACTION}}">
    <input type="hidden" name="type" value="{{$type}}"  />
       <div class="left">
            店铺名称：
            <input class="queryInput" type="text" name="sname" id="sname" value="{{$shop_name}}" />
            <input type="submit" class="formbtn"  value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/batch/choose-shop">撤销检索</a>
    </form>
  </div>
</div>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="100%" cellspacing="0" class="dataTable" id="table">
    {{if $data}}
    <tr class="tatr1">
      <td>所属店铺</td>
      <td>操作</td>
    </tr>
    {{/if}}
    {{foreach from=$data key=key item=item}}
    <tr class="tatr2">
      <td>{{$item.shop_name}}</td>
      <td><a href="/admin/batch/add/sid:{{$item.shop_id}}/sname:{{$item.shop_name}}">选择</a></td>
    </tr>
   {{foreachelse}}
    <tr class="no_data">
      <td colspan="2">暂无数据</td>
    </tr>
    {{/foreach}}
  </table>
</div>
{{include file='admin/footer.php'}}