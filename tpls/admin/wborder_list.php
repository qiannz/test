{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/DatePicker/WdatePicker.js" ></script>
<div id="rightTop">
  <p>微商管理</p>
  <ul class="subnav">
    <li><span>订单管理</span></li>
    <li><a class="btn1" href="/admin/wborder/add">订单新增</a></li>
  </ul>
</div>
<div class="mrightTop">
	<div class="fontl">
	    <form method="post" action="{{$_CONF.FORM_ACTION}}">
	       <div class="left">
	                                   用户姓名：
	            <input class="queryInput" type="text" name="realname" value="{{$request.realname}}" />
	                                 手机号码：
	            <input class="queryInput" type="text" name="mobile" value="{{$request.mobile}}" />
	                                  用户类型：
	            <select name="ut" class="querySelect">
	            	<option value="">全部</option>
	                <option value="1" {{if $request.ut eq 1}}selected="selected"{{/if}}>微商</option>
	                <option value="2" {{if $request.ut eq 2}}selected="selected"{{/if}}>代购</option>
	                <option value="3" {{if $request.ut eq 3}}selected="selected"{{/if}}>切货</option>
	                <option value="4" {{if $request.ut eq 4}}selected="selected"{{/if}}>游客VIP</option>
	            </select>
	            <input type="submit" class="formbtn" value="查询" />
	      </div>
	      <a class="left formbtn1" href="/admin/wborder/list">撤销检索</a>
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
      <td width="15%">手机号码</td>
      <td>用户类型</td>
      <td>用户姓名</td>
      <td>总金额</td>
      <td>折扣</td>
      <td>实付金额</td>
	  <td>创建时间</td>
      <td width="250">操作</td>
    </tr>
    {{foreach from=$data key=key item=item}}
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="{{$item.order_id}}" /></td>
      <td>{{$item.order_id}}</td>
      <td>{{$item.mobile}}</td>
      <td>{{if $item.user_type eq '1'}}微商
      	  {{elseif $item.user_type eq '2'}}代购
      	  {{elseif $item.user_type eq '3'}}切货
      	  {{elseif $item.user_type eq '4'}}游客VIP
      	  {{/if}}
      </td>
      <td>{{$item.realname}}</td>
      <td>{{$item.total_price}}</td>
      <td>{{if $item.discount neq '0'}}{{$item.discount}}{{/if}}</td>
      <td>{{$item.pay_price}}</td>
      <td>{{if $item.created}}{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}{{/if}}</td>
      <td width="250">
      	<a href="/admin/wborder/add/order_id:{{$item.order_id}}/page:{{$page}}">编辑</a> |
      	<a href="javascript:drop_confirm('del', '{{$item.order_id}}')">删除</a> |
      	<a href="javascript:jumpToLog('wborder', '{{$item.order_id}}')">记录</a>
      </td>
    </tr>
   {{foreachelse}}
    <tr class="no_data">
      <td colspan="7">暂无数据</td>
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
</script>
{{include file='admin/footer.php'}}