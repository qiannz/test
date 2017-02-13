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
  <p>券管理</p>
  <ul class="subnav">
    {{if $type eq 'v'}}
    	<li><a class="btn4" href="/admin/ticket/voucher-list">现金券列表</a></li>
    {{elseif $type eq 's'}}
    	<li><a class="btn4" href="/admin/ticket/selfpay-list">自定义买单列表</a></li>
    {{else}}    
    	<li><a class="btn4" href="/admin/ticket/coupon-list">优惠券列表</a></li>
    {{/if}}    
    <li><span>店铺选择</span></li>
  </ul>
</div>
<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="{{$_CONF.FORM_ACTION}}" id="form">
    <input type="hidden" name="type" value="{{$type}}"  />
       <div class="left">
            请输入用户名：
            <input class="queryInput" type="text" name="uname" id="uname" value="{{$user_name}}" />
            店铺名称：
            <input class="queryInput" type="text" name="sname" id="sname" value="{{$shop_name}}" />
            <input type="button" class="formbtn"  value="查询" onClick="checkUser()"/>
      </div>
         {{if $type eq 'v'}}
            <a class="left formbtn1" href="/admin/ticket/user-shop/type:v">撤销检索</a> &nbsp;&nbsp;&nbsp;
        {{elseif $type eq 's'}}
        	<a class="left formbtn1" href="/admin/ticket/user-shop/type:s">撤销检索</a> &nbsp;&nbsp;&nbsp;
        {{else}}    
            <a class="left formbtn1" href="/admin/ticket/user-shop/type:c">撤销检索</a> &nbsp;&nbsp;&nbsp;
        {{/if}}  
      <label class="field_notice" >请填写用户名选择店铺</label>
      <label class="field_notice" style="color:red" id="ts"></label>
    </form>
  </div>
<div class="fontr">{{include file='admin/page.top.php'}}</div>
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
      <td>
      	{{if $type eq 'v'}}
      		<a href="/admin/ticket/add-voucher/sid:{{$item.shop_id}}/uname:{{$user_name}}">添加现金券</a> 
        {{elseif $type eq 's'}}
        	<a href="/admin/ticket/add-selfpay/sid:{{$item.shop_id}}/uname:{{$user_name}}">添加自定义买单券</a>
        {{else}}
        	<a href="/admin/ticket/add-coupon/sid:{{$item.shop_id}}/uname:{{$user_name}}">添加优惠券</a> 
        {{/if}}    
      </td>
    </tr>
   {{foreachelse}}
    <tr class="no_data">
      <td colspan="2">暂无数据</td>
    </tr>
    {{/foreach}}
  </table>
  <div id="dataFuncs">
    <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
    <div class="clear"></div>
  </div>
</div>
{{include file='admin/footer.php'}}