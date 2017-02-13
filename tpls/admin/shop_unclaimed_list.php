{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css" />
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js"></script>
<div id="rightTop">
  <p>店铺管理</p>
  <ul class="subnav">
    <li><span>店铺认领</span></li>
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
                <option value = "3" {{if $request.st eq '3'}}selected="selected"{{/if}}>审核不通过</option>
            </select>
            用户名：
           <input class="queryInput" type="text" name="uname" value="{{$request.uname}}" />
           <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/shopaudit/list">撤销检索</a>
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
      <td>认领店铺</td>
      <td>店铺地址</td>
      <td>状态</td>
      <td>说明</td>
      <td>操作</td>
    </tr>
    <!--{{foreach from=$data key=key item=item}}-->
    <tr class="tatr2">
      <td>{{$item.user_name}}</td>
      <td>{{$item.full_name}}</td>
      <td>{{$item.phone_number}}</td>
      <td>{{$item.shop_name}}</td>
      <td>{{$item.shop_address}}</td>
      <td>
        {{if $item.audit_status eq '-1'}}审核不通过
        {{elseif $item.audit_status eq '0'}}未审核
        {{elseif $item.audit_status eq '1'}}已审核
        {{/if}}      
      </td>
      <td>{{$item.explan}}</td>
      <td>
      <span style="width: 100px">
      	 {{if $item.audit_status eq 0}}<a href="/admin/shopaudit/audit/aid:{{$item.audit_id}}">审核</a> 　| 　{{/if}}
     	 <a href="javascript:jumpToLog('audit', {{$item.audit_id}})">记录</a>
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