{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/DatePicker/WdatePicker.js" ></script>
<style>
<!--
.btndays {background: url(/css/admin/images/btn1.gif); display: block; width: 69px; height: 20px; line-height: 20px; color: #fff; text-align: center; text-decoration: none}
-->
</style>
<div id="rightTop">
  <p>商品管理</p>
  <ul class="subnav">
    <li><span>商品列表</span></li>
    <li><a class="btn1" href="/admin/good/add">新建商品</a></li>
  </ul>
</div>
<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="{{$_CONF.FORM_ACTION}}">
       <div class="left">
			审核状态：
			<select name="st" class="querySelect">
            	<option value="">全部</option>
                <option value="1" {{if $request.st eq 1}}selected="selected"{{/if}}>未审核</option>
                <option value="2" {{if $request.st eq 2}}selected="selected"{{/if}}>已审核</option>
                <option value="3" {{if $request.st eq 3}}selected="selected"{{/if}}>审核不通过</option>
            </select>
			时间：
			<select name="tt" class="querySelect">
            	<option value="">全部</option>
                <option value="1" {{if $request.tt eq 1}}selected="selected"{{/if}}>1周内</option>
                <option value="2" {{if $request.tt eq 2}}selected="selected"{{/if}}>1个月内</option>
                <option value="3" {{if $request.tt eq 3}}selected="selected"{{/if}}>3个月内</option>
                <option value="4" {{if $request.tt eq 4}}selected="selected"{{/if}}>1年内</option>
            </select>
            用户名：
            <input class="queryInput" type="text" name="uname" value="{{$request.uname}}" />
            商品标题：
            <input class="queryInput" type="text" name="title" value="{{$request.title}}" />
            <input class="querySelect" type="radio" name="isd" value="1" {{if $request.isd eq 1}}checked="checked"{{/if}} /> 删除　
            <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/good/list">撤销检索</a>
    </form>
  </div>
<div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="mrightTop">
  <div class="fontl">
  		<form method="post" action="{{$_CONF.FORM_ACTION}}">
       <div class="left">
       		<span style="float:left">选择日期：</span>
       		<input class="queryInput" style="float:left;width:75px;" id="days" type="text" name="days" onFocus="WdatePicker({isShowClear:false,readOnly:false,maxDate:'now()'})"  value="{{$request.days}}"  />
            <span style="float:left;"><input type="checkbox" name="state" value="1" {{if $request.state eq 1}}checked="checked"{{/if}} /></span>
           <span style="float:left"> <input type="submit" class="formbtn2" value="日期跳转" />&nbsp;&nbsp;&nbsp;&nbsp;</span>
           
            {{foreach from=$7days key=key item=item}}
            	{{if $request.days eq $item}}
            		<span class="querySelect" style="float:left">{{$item}}</span>
            	{{else}}
            		<a class="querySelect btndays" href="/admin/good/list/days:{{$item}}" style="float:left">{{$item}}</a>
            	{{/if}}
            {{/foreach}}
            
      </div>
      </form>
  </div>
</div>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <input type="hidden" name="days" id="days" value="{{$auditDay}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td width="20%">商品标题 [<span style="color:red">原价</span> / <span style="color:green">折扣价</span>]</td>
      <td>店铺名称</td>
	  <td>用户名</td>
	  <td>录入时间</td>
	  <td width="100">状态</td>
	  <td class="table-center">喜欢 / 收藏</td>
      <td class="table-center">点击数</td>
      {{if $request.isd eq 0}}
      <td>操作</td>
      {{/if}}
    </tr>
    {{foreach from=$data key=key item=item}}
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="{{$item.good_id}}" /></td>
      <td>{{$item.good_name}} [<span style="color:red">{{$item.org_price}}</span> / <span style="color:green">{{$item.dis_price}}</span>]</td>
      <td><a href="javascript:jumpToGoodShopList({{$item.shop_id}}, {{$item.region_id}}, {{$item.circle_id}})">{{$item.shop_name}}</a></td>
      <td>{{$item.user_name}}</td>
      <td>{{if $item.created}}{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}{{/if}}</td>
      <td>
      		{{if $item.good_status eq '-1'}}审核不通过
      		{{elseif $item.good_status eq '0'}}未审核
			{{elseif $item.good_status eq '1'}}已审核
            {{/if}}
      </td>
      <td class="table-center">{{$item.concerned_number}} / {{$item.favorite_number}} </td>
      <td class="table-center">{{$item.clicks}}</td>
      {{if $request.isd eq 0}}
      <td>
      		<a href="/admin/good/edit/gid:{{$item.good_id}}">编辑</a> | 
            <a href="javascript:drop_confirm('del', {{$item.good_id}}, '{{$item.good_name}}')">删除</a> |
            {{if $item.good_status neq '-1'}}
            <a href="/admin/good/recommend/id:{{$item.good_id}}/page:{{$page}}">推荐</a> | 
            {{/if}}
            {{if $item.good_status eq 0}}<a href="/admin/good/audit/gid:{{$item.good_id}}/audit_day:{{if $auditDay}}{{$auditDay}}{{else}}{{$item.created|date_format:'%Y-%m-%d'}}{{/if}}/page:{{$page}}">审核</a> |{{/if}}
            <a href="javascript:jumpToLog('good', {{$item.good_id}})">记录</a>
      </td>
      {{/if}}
    </tr>
   {{foreachelse}}
    <tr class="no_data">
      <td colspan="{{if $request.isd eq 0}}9{{else}}8{{/if}}">暂无数据</td>
    </tr>
    {{/foreach}}
  </table>
    <div id="dataFuncs">
    <div class="left paddingT15"> &nbsp;&nbsp;
    	{{if $request.isd eq 1}}
        <input class="formbtn" type="button" value="取消删除" onClick="drop_confirm('un-del')" />
        {{else}}
        <input class="formbtn" type="button" value="删除" onClick="drop_confirm('del-all')" />
        {{/if}}
                
        <input class="formbtn" type="button" value="批量审核" onClick="dropConfirm('{{$auditDay}}')" />
    </div>
    <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
    <div class="clear"></div>
  </div>
</div>
<script type="text/javascript">
function drop_confirm(act, id, gname, days) {
	var content;
	switch(act) {
		case 'del': content = '确认删除？'; break;
		case 'del-all': 
            if($('.checkitem:checked').length == 0){
                alert('请选择删除对象');
				return false;
            }
           
            var items = '';
            $('.checkitem:checked:enabled').each(function(){
                items += this.value + ',';
            });
            items = items.substr(0, (items.length - 1));
			id = items;	
			content = '确认删除他们？'; 
			break;
		case 'un-del': content = '确认取消删除它们吗？'; 
            if($('.checkitem:checked').length == 0){
                alert('请选择恢复删除对象');
				return false;
            }
           
            var items = '';
            $('.checkitem:checked:enabled').each(function(){
                items += this.value + ',';
            });
            items = items.substr(0, (items.length - 1));
			id = items;				
		break;
		
		case 'batch-audit' :
			$.post('/admin/good/check-audit', {audit_day: days}, function(data){
				if (data == 'audit') {
					$.dialog.alert('之前还有商品没有被审核！');
					return false;
				}
			});
			content = '确认审核通过它们吗？'; 
            if($('.checkitem:checked').length == 0){
                alert('请选审核对象');
				return false;
            }
           
            var items = '';
            $('.checkitem:checked:enabled').each(function(){
                items += this.value + ',';
            });
            items = items.substr(0, (items.length - 1));
			id = items;				
		break;
	}
	
	$.dialog({
		title:'警告',
		content: content,
		ok: function() {
			var url = '/' + _M + '/' + _C + '/' + act;
			location.href  = url + '/id:' + id + '/gname:' + gname + '/days:' + days + '/page:' +  $('#page').val();	
		},
		cancel: true
	});
}

function jumpToGoodShopList(sid, rid, cid)
{
	window.parent.pickTab('good');
	window.parent.openItem('good_shop_list');
	location.href="/admin/goodshop/list/shop_id:" + sid + "/region_id:" + rid + "/circle_id:" + cid;
}

function dropConfirm(days) {
	var content;
/*	$.post('/admin/good/check-audit', {audit_day: days}, function(data){
		if (data == 'audit') {
			$.dialog.alert('之前还有商品没有被审核！');
			return false;
		}
	});*/
	content = '确认审核通过它们吗？'; 
	if($('.checkitem:checked').length == 0){
		alert('请选审核对象');
		return false;
	}
   
	var items = '';
	$('.checkitem:checked:enabled').each(function(){
		items += this.value + ',';
	});
	items = items.substr(0, (items.length - 1));	
	
	$.dialog({
		title:'警告',
		content: content,
		button : [
			{
				value : '审核通过',
				callback:function(){
					var url = '/' + _M + '/' + _C + '/batch-upload';
					location.href  = url + '/id:' + items + '/days:' + days + '/page:' +  $('#page').val();				
				}
			},
			{
				value : '审核拒绝',
				callback:function(){
					var url = '/' + _M + '/' + _C + '/not-batch-audit';
					location.href  = url + '/id:' + items + '/days:' + days + '/page:' +  $('#page').val();
				}
			},
			{
				value: '关闭'
			}
		]
	});	
}
</script>
{{include file='admin/footer.php'}}