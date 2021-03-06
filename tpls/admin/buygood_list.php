{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/inline_edit.js" ></script>
<div id="rightTop">
  <p>券管理</p>
  <ul class="subnav">
    <li><span>团购商品</span></li>
    <li><a class="btn4" href="/admin/buygood/user-shop/type:b">新增商品</a></li>
  </ul>
</div>
<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="{{$_CONF.FORM_ACTION}}">
       <div class="left">
			团购状态：
			<select name="app" class="querySelect">
            	<option value="">全部</option>
                <option value="1" {{if $request.app eq 1}}selected="selected"{{/if}}>未开始</option>
                <option value="2" {{if $request.app eq 2}}selected="selected"{{/if}}>进行中</option>
                <option value="3" {{if $request.app eq 3}}selected="selected"{{/if}}>已过期</option>
            </select>
            审核状态：
            <select name="st" class="querySelect">
            	<option value="">全部</option>
                <option value="1" {{if $request.st eq 1}}selected="selected"{{/if}}>未审核</option>
                <option value="2" {{if $request.st eq 2}}selected="selected"{{/if}}>已审核</option>
                <option value="3" {{if $request.st eq 3}}selected="selected"{{/if}}>审核不通过</option>
            </select>
            上下架：
             <select name="isa" class="querySelect">
            	<option value="">全部</option>
                <option value="1" {{if $request.isa eq 1}}selected="selected"{{/if}}>上架</option>
                <option value="2" {{if $request.isa eq 2}}selected="selected"{{/if}}>下架</option>
            </select>
            显示状态：
             <select name="iss" class="querySelect">
            	<option value="">全部</option>
                <option value="1" {{if $request.iss eq 1}}selected="selected"{{/if}}>显示</option>
                <option value="2" {{if $request.iss eq 2}}selected="selected"{{/if}}>不显示</option>
            </select>
            团购名称：
            <input class="queryInput" type="text" name="title" value="{{$request.title}}" />
            活动名称：
            <input class="queryInput" type="text" name="act_name" value="{{$request.act_name}}" />
            <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/buygood/list">撤销检索</a>
    </form>
  </div>
<div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    <tr class="tatr1">
      <td width="280">ID</td>
      <td>商品名称</td>
      <td>活动名称</td>
      <td>所属店铺</td>
	  <td width="50">面值</td>
	  <td width="50">售价</td>
      <td width="80">返利</td>
      <td width="80">团购状态</td>
	  <td width="80">审核状态</td>
      <td width="80">上下架</td>
      <td width="80">显示状态</td>
      <td width="120">排序</td>
	  <td>录入时间</td>
      <td>操作</td>
    </tr>
    {{foreach from=$data key=key item=item}}
    <tr class="tatr2">
      <td>{{$item.ticket_id}}{{if $item.ticket_uuid}}<br />{{$item.ticket_uuid}}{{/if}}</td>
      <td>{{$item.ticket_title}}</td>
      <td>{{$item.activity_name}}</td>
      <td>{{$item.shop_name}}</td>
      <td>{{$item.par_value}}</td>
      <td>{{$item.selling_price}}</td>
      <td>{{if $item.rebates gt 0}}{{$item.rebates}}{{/if}}</td>
      <td>
      		{{if $item.apply_status eq '-1'}}已过期
      		{{elseif $item.apply_status eq '0'}}未开始
			{{elseif $item.apply_status eq '1'}}进行中
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
      <td>
            {{if $item.is_show eq '0'}}不显示
      		{{elseif $item.is_show eq '1'}}显示
            {{/if}}
      </td>
      <td><span ectype="inline_edit" fieldname="sequence" fieldid="{{$item.ticket_id}}" datatype="pint" maxvalue="999999" class="editable">{{$item.sequence}}</span></td>
      <td>{{if $item.created}}{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}{{/if}}</td>
      <td>
      		<a href="/admin/buygood/add-edit/tid:{{$item.ticket_id}}/uname:{{$item.user_name}}/sid:{{$item.shop_id}}/page:{{$page}}">编辑</a> | 
            {{if $item.ticket_status eq '1' && $item.apply_status neq '-1'}}
            <a href="/admin/buygood/recommend/id:{{$item.ticket_id}}/title:{{$item.ticket_title}}/page:{{$page}}">推荐</a> | 
            {{/if}}
            {{if $item.ticket_status eq 0 && $item.apply_status neq '-1'}}
            <a href="/admin/buygood/audit/tid:{{$item.ticket_id}}/page:{{$page}}">审核</a> |{{/if}}
            <a href="javascript:jumpToLog('ticket', {{$item.ticket_id}})">记录</a>
      </td>
    </tr>
   {{foreachelse}}
    <tr class="no_data">
      <td colspan="14">暂无数据</td>
    </tr>
    {{/foreach}}
  </table>
  <div id="dataFuncs">
    <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
    <div class="clear"></div>
  </div>
</div>
{{include file='admin/footer.php'}}