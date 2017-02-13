{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css" />
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js"></script>
<div id="rightTop">
  <p>店铺管理</p>
  <ul class="subnav">
    <li><span>商户入驻</span></li>
  </ul>
</div>

<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="{{$_CONF.FORM_ACTION}}">
       <div class="left">
            审核状态：	
          	<select class="querySelect" name="st">
          		<option value = "">全部</option>
            	<option value = "1" {{if $request.st eq '1'}}selected="selected"{{/if}}>未审核</option>
				<option value = "2" {{if $request.st eq '2'}}selected="selected"{{/if}}>审核通过</option>
                <option value = "3" {{if $request.st eq '3'}}selected="selected"{{/if}}>资料补全（等待付款）</option>
                <option value = "4" {{if $request.st eq '4'}}selected="selected"{{/if}}>已付款（入驻成功</option>
                <option value = "5" {{if $request.st eq '5'}}selected="selected"{{/if}}>审核不通过</option>
            </select>
            用户名：
           <input class="queryInput" type="text" name="uname" value="{{$request.uname}}" />
           <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/merchant/list">撤销检索</a>
    </form>
  </div>
  <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    <tr class="tatr1">
      <td>用户名</td>
      <td>姓名</td>
      <td>手机号码</td>
      <td>店铺名称</td>
      <td>店铺地址</td>
      <td>状态</td>
      <td>操作</td>
    </tr>
    <!--{{foreach from=$data key=key item=item}}-->
    <tr class="tatr2">
      <td>{{$item.user_name}}</td>
      <td>{{$item.real_name}}</td>
      <td>{{$item.mobile}}</td>
      <td>{{$item.shop_name}}</td>
      <td>{{$item.shop_address}}</td>
      <td>
        {{if $item.auth_status eq '-1'}}审核不通过
        {{elseif $item.auth_status eq '0'}}待申请
        {{elseif $item.auth_status eq '1'}}待审核
        {{elseif $item.auth_status eq '2'}}审核通过
        {{elseif $item.auth_status eq '3'}}资料补全（等待付款）
        {{elseif $item.auth_status eq '4'}}已付款（入驻成功）
        {{/if}}      
      </td>
      <td>
      <span style="width: 100px">
      	 {{if $item.auth_status eq 1}}<a href="/admin/merchant/audit/uid:{{$item.user_id}}/shop_id:{{$item.shop_id}}">商户审核</a> | {{/if}}
      	 {{if $item.auth_status eq 3}}<a href="/admin/merchant/pay/uid:{{$item.user_id}}/pack_id:{{$item.pack_id}}/shop_id:{{$item.shop_id}}">入驻审核</a> | {{/if}}
     	 <a href="javascript:jumpToLog('audit', {{$item.user_id}})">记录</a>
      </span>
      </td>
    </tr>
    <!--{{foreachelse}}-->
    <tr class="no_data">
      <td colspan="8">暂无申请记录</td>
    </tr>
    <!--{{/foreach}}-->
  </table>
  <div id="dataFuncs">
    <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
    <div class="clear"></div>
  </div>
</div>
{{include file='admin/footer.php'}}