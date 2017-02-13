{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/inline_edit.js" ></script>
<div id="rightTop">
  <p>券管理</p>
  <ul class="subnav">
    <li><span>优惠券列表</span></li>
    <li><a class="btn4" href="/admin/ticket/user-shop/type:c">新增优惠券</a></li>
  </ul>
</div>
<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="{{$_CONF.FORM_ACTION}}">
       <div class="left">
			申领状态：
			<select name="app" class="querySelect">
            	<option value="">全部</option>
                <option value="1" {{if $request.app eq 1}}selected="selected"{{/if}}>未开始</option>
                <option value="2" {{if $request.app eq 2}}selected="selected"{{/if}}>申领中</option>
                <option value="3" {{if $request.app eq 3}}selected="selected"{{/if}}>已过期</option>
            </select>
            审核状态：
            <select name="st" class="querySelect">
            	<option value="">全部</option>
                <option value="1" {{if $request.st eq 1}}selected="selected"{{/if}}>未审核</option>
                <option value="2" {{if $request.st eq 2}}selected="selected"{{/if}}>已审核</option>
                <option value="3" {{if $request.st eq 3}}selected="selected"{{/if}}>审核不通过</option>
            </select>
            商户状态：
             <select name="isa" class="querySelect">
            	<option value="">全部</option>
                <option value="1" {{if $request.isa eq 1}}selected="selected"{{/if}}>上架</option>
                <option value="2" {{if $request.isa eq 2}}selected="selected"{{/if}}>下架</option>
            </select>
            券名称：
            <input class="queryInput" type="text" name="title" value="{{$request.title}}" />
            <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/ticket/coupon-list">撤销检索</a>
    </form>
  </div>
<div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    <tr class="tatr1">
      <td width="280">券UID</td>
      <td>券名称</td>
      <td>所属店铺</td>
	  <td>面值</td>
      <td>数量（总量/已领/已用）</td>
      <td>申领状态</td>
	  <td>审核状态</td>
      <td>券状态</td>
      <td>排序</td>
	  <td>录入时间</td>
      <td>操作</td>
    </tr>
    {{foreach from=$data key=key item=item}}
    <tr class="tatr2">
      <td>{{if $item.ticket_uuid}}【{{$item.ticket_uuid}}】{{/if}}</td>
      <td>{{$item.ticket_title}}</td>
      <td>{{$item.shop_name}}</td>
      <td>{{$item.par_value}}</td>
      <td>{{$item.total}} / {{$item.has_led}} / {{$item.used}}</td>
      <td>
      		{{if $item.apply_status eq '-1'}}已过期
      		{{elseif $item.apply_status eq '0'}}未开始
			{{elseif $item.apply_status eq '1'}}申领中
            {{/if}}
      </td>
      <td>
      		{{if $item.ticket_status eq '-1'}}审核不通过
      		{{elseif $item.ticket_status eq '0'}}未审核
			{{elseif $item.ticket_status eq '1'}}已审核
            {{/if}}
      </td>
	  <td>
      		{{if $item.is_auth eq '0'}}下架
      		{{elseif $item.is_auth eq '1'}}上架
            {{/if}}
      </td>
      <td><span ectype="inline_edit" fieldname="sequence" fieldid="{{$item.ticket_id}}" datatype="pint" maxvalue="999999" class="editable">{{$item.sequence}}</span></td>    
      <td>{{if $item.created}}{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}{{/if}}</td>
      <td>
      		<a href="/admin/ticket/add-coupon/tid:{{$item.ticket_id}}/uname:{{$item.user_name}}/sid:{{$item.shop_id}}">编辑</a> | 
            {{if $item.ticket_status eq '1' && $item.apply_status neq '-1'}}
            <a href="/admin/ticket/recommend/id:{{$item.ticket_id}}/title:{{$item.ticket_title}}/type:{{$item.ticket_type}}/page:{{$page}}">推荐</a> | 
            {{/if}}
            {{if $item.ticket_status eq 0 && $item.apply_status neq '-1'}}
            <a href="/admin/ticket/audit/title:{{$item.ticket_title}}/tid:{{$item.ticket_id}}/type:{{$item.ticket_type}}/page:{{$page}}">审核</a> |{{/if}}
            <a href="/admin/ticket/export/title:{{$item.ticket_title}}/sname:{{$item.shop_name}}/tid:{{$item.ticket_id}}">导出</a> | 
            <a href="javascript:jumpToLog('ticket', {{$item.ticket_id}})">记录</a>
      </td>
    </tr>
   {{foreachelse}}
    <tr class="no_data">
      <td colspan="11">暂无数据</td>
    </tr>
    {{/foreach}}
  </table>
  <div id="dataFuncs">
    <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
    <div class="clear"></div>
  </div>
</div>
{{include file='admin/footer.php'}}