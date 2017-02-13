{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css" />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js"></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js"></script>
<div id="rightTop">
	<p>数据配置</p>
	<ul class="subnav">
		<li><span>宣传标语管理</span></li>
		<li><a class="btn1" href="/admin/slogan/add">新增标语</a></li>
	</ul>
</div>

<div class="mrightTop">
	<div class="fontl">
		<form method="post" action="{{$_CONF.FORM_ACTION}}">
			<div class="left">
				分类： 
					<select name="category" id="category">
						<option value="">全部</option>
						<option value="1" {{if $request.category eq 1 }}selected="selected"{{/if}}>商品</option>
						<option value="2" {{if $request.category eq 2 }}selected="selected"{{/if}}>店铺</option>
						<option value="3" {{if $request.category eq 3 }}selected="selected"{{/if}}>商场</option>
						<option value="4" {{if $request.category eq 4 }}selected="selected"{{/if}}>品牌</option>
						<option value="5" {{if $request.category eq 5 }}selected="selected"{{/if}}>收藏折扣</option>
						<option value="6" {{if $request.category eq 6 }}selected="selected"{{/if}}>发布折扣</option>
						<option value="7" {{if $request.category eq 7 }}selected="selected"{{/if}}>浏览折扣</option>
					</select>&nbsp;&nbsp;
				 宣传标语：
				  	<input class="queryInput" type="text" name="name" value="{{$smarty.request.name}}" /> 
					<!-- <input class="querySelect" type="checkbox" name="isd" value="1" {{if $request.isd eq 1}}checked="checked" {{/if}} /> 删除 --> 
					<input type="submit" class="formbtn" value="查询" />
			</div>
			<a class="left formbtn1" href="/admin/slogan/list">撤销检索</a>
		</form>
	</div>
	<div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
	<input type="hidden" name="page" id="page" value="{{$page}}" />
	<table width="100%" cellspacing="0" class="dataTable">
		{{if $slogans}}
		<tr class="tatr1">
			<td width="20" class="firstCell">
				<input type="checkbox" class="checkall" />
			</td>
			<td>ID</td>
			<td>分类</td> 
			<td>宣传标语</td>
			{{if $request.isd eq 0}}
			<td>操作</td> 
			{{/if}}
		</tr>
		{{/if}} 
		{{foreach from=$slogans item=item}}
		<tr class="tatr2">
			<td class="firstCell"><input type="checkbox" class="checkitem"
				value="{{$item.slogan_id}}" /></td>
			<td>{{$item.slogan_id}}</td>
			<td>{{if $item.category eq 1}}商品 
				{{elseif $item.category eq 2}}店铺
				{{elseif $item.category eq 3}}商场 
				{{elseif $item.category eq 4}}品牌
				{{elseif $item.category eq 5}}收藏折扣 
				{{elseif $item.category eq 6}}发布折扣 
				{{elseif $item.category eq 7}}浏览折扣 
				{{/if}}
			</td> 
			<td>{{$item.name}}</td>
			{{if $request.isd eq 0}}
			<td><a href="/admin/slogan/edit/id:{{$item.slogan_id}}/page:{{$page}}">编辑</a> | 
				<a href="javascript:drop_confirm('del', {{$item.slogan_id}})">删除</a>
			</td> 
			{{/if}}
		</tr>
		{{foreachelse}}
		<tr class="no_data">
			<td colspan="6">暂无宣传标语记录</td>
		</tr>
		{{/foreach}}
	</table>
	<div id="dataFuncs">
		<div class="left paddingT15">&nbsp;&nbsp; 
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
$(".dataTable tr.tatr2").mouseover(function(){  
	$(this).addClass("over");
})

$(".dataTable tr.tatr2").mouseout(function(){
	$(this).removeClass("over");
})

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
			act = 'del';
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
