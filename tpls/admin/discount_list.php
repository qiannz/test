{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/DatePicker/WdatePicker.js" ></script>
<div id="rightTop">
  <p>用户管理</p>
  <ul class="subnav">
    <li><span>折扣管理</span></li>
    <li><a class="btn1" href="/admin/discount/add">新建折扣</a></li>
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
            用户名：
            <input class="queryInput" type="text" name="uname" value="{{$request.uname}}" />
            折扣标题：
            <input class="queryInput" type="text" name="title" value="{{$request.title}}" />
            <input class="querySelect" type="checkbox" name="isd" value="1" {{if $request.isd eq 1}}checked="checked"{{/if}} /> 删除　
            <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/discount/list">撤销检索</a>
    </form>
  </div>
<div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>

<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td>ID</td>
      <td width="15%">折扣标题</td>
      <td>开始时间</td>
      <td>结束时间</td>
	  <td>活动地点</td>
	  <td>折扣力度</td>
	  <td>促销信息</td>
	  <td>创建时间</td>
	  <td width="100">状态</td>
	  <td class="table-center">收藏数/点击数</td>
      {{if $request.isd eq 0}}
      <td width="250">操作</td>
      {{/if}}
    </tr>
    {{foreach from=$data key=key item=item}}
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="{{$item.discount_id}}" /></td>
      <td>{{$item.discount_id}}</td>
      <td>{{$item.title}}</td>
      <td>{{$item.stime|date_format:'%Y-%m-%d %H:%I'}}</td>
      <td>{{$item.etime|date_format:'%Y-%m-%d %H:%I'}}</td>
      <td>{{$item.address}}</td>
      <td>{{if $item.discount_start && $item.discount_end}} {{$item.discount_start}} - {{$item.discount_end}} 折 {{/if}}</td>
      <td>{{$item.promotion}}</td>
      <td>{{if $item.created}}{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}{{/if}}</td>
      <td>
      		{{if $item.discount_status eq '-1'}}审核不通过
      		{{elseif $item.discount_status eq '0'}}未审核
			{{elseif $item.discount_status eq '1'}}已审核
            {{/if}}
      </td>
      <td class="table-center">{{$item.collection_number}}/{{$item.view_quantity}} </td>
      {{if $request.isd eq 0}}
      <td width="250">
      		<a href="/admin/discount/edit/did:{{$item.discount_id}}/page:{{$page}}">编辑</a> | 
      		<a href="javascript:drop_confirm('del', {{$item.discount_id}})">删除</a> |
            {{if $item.discount_status eq 0}}<a href="/admin/discount/audit/did:{{$item.discount_id}}/page:{{$page}}">审核</a> |{{/if}}
            <a href="/admin/discount/recommend/did:{{$item.discount_id}}/page:{{$page}}">推荐</a> |
            <a href="/admin/discount/consultation/did:{{$item.discount_id}}">咨询</a> |
            <a href="/admin/discount/group-chat/did:{{$item.discount_id}}/page:{{$page}}/type:discount">群聊</a> |
            <a href="javascript:jumpToLog('discount', '{{$item.discount_id}}')">记录</a>
      </td>
      {{/if}}
    </tr>
   {{foreachelse}}
    <tr class="no_data">
      <td colspan="{{if $request.isd eq 0}}12{{else}}11{{/if}}">暂无数据</td>
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
    </div>
    <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
    <div class="clear"></div>
  </div>
</div>
<script type="text/javascript">
function drop_confirm(act, id) {
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
	}
	
	$.dialog({
		title:'警告',
		content: content,
		ok: function() {
			var url = '/' + _M + '/' + _C + '/' + act;
			location.href  = url + '/id:' + id + '/page:' +  $('#page').val();	
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
					var url = '/' + _M + '/' + _C + '/do-audit';
					location.href  = url + '/did:' + items + '/days:' + days + '/audit_type:1/page:' +  $('#page').val();				
				}
			},
			{
				value : '审核拒绝',
				callback:function(){
					var url = '/' + _M + '/' + _C + '/do-audit';
					location.href  = url + '/did:' + items + '/days:' + days + '/audit_type:2/page:' +  $('#page').val();
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