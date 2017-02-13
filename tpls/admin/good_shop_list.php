{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<div id="rightTop">
  <p>商品管理</p>
  <ul class="subnav">
    <li><span>店铺商品列表</span></li>
  </ul>
</div>
<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="{{$_CONF.FORM_ACTION}}">
       <div class="left">
			行政区：
			<select name="region_id" id="region_id" class="querySelect">
            	<option value="">全部</option>
             	{{foreach from=$regionArray key=key item=item}}
                <option value="{{$key}}">{{$item}}</option>
                {{/foreach}}
            </select>
			商圈：
			<select name="circle_id" id="circle_id" class="querySelect">
            	<option value="">全部</option>
                {{foreach from=$circleArray key=key item=item}}
                <option value="{{$item.id}}">{{$item.name}}</option>
                {{/foreach}}
            </select>
            店铺：
			<select name="shop_id" id="shop_id" class="querySelect">
            	<option value="">全部</option>
                {{foreach from=$shopArray key=key item=item}}
                <option value="{{$item.id}}">{{$item.name}}</option>
                {{/foreach}}
            </select>
            商品标题：
            <input class="queryInput" type="text" name="title" value="{{$request.title}}" />
            <input class="querySelect" type="radio" name="isd" value="1" {{if $request.isd eq 1}}checked="checked"{{/if}} /> 删除　
            <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/goodshop/list/region_id:{{$request.region_id}}/circle_id:{{$request.circle_id}}/shop_id:{{$request.shop_id}}">撤销检索</a>
    </form>
  </div>
<div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <input type="hidden" name="page_str" id="page_str" value="{{$page_str}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td>店铺名称</td>
      <td width="20%">商品标题 [<span style="color:red">原价</span> / <span style="color:green">折扣价</span>]</td>
	  <td>用户名</td>
	  <td>录入时间</td>
	  <td width="100">状态</td>
	  <td class="table-center">喜欢 / 收藏</td>
      {{if $request.isd eq 0}}
      <td>操作</td>
      {{/if}}
    </tr>
    {{foreach from=$data key=key item=item}}
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="{{$item.good_id}}" /></td>
      <td>{{$item.shop_name}}</td>
      <td>{{$item.good_name}} [<span style="color:red">{{$item.org_price}}</span> / <span style="color:green">{{$item.dis_price}}</span>]</td>
      <td>{{$item.user_name}}</td>
      <td>{{if $item.created}}{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}{{/if}}</td>
      <td>
      		{{if $item.good_status eq '-1'}}审核不通过
      		{{elseif $item.good_status eq '0'}}未审核
			{{elseif $item.good_status eq '1'}}已审核
            {{/if}}
      </td>
      <td class="table-center">{{$item.concerned_number}} / {{$item.favorite_number}} </td>
      {{if $request.isd eq 0}}
      <td>
      		<a href="/admin/goodshop/edit/{{$page_str}}/gid:{{$item.good_id}}/page:{{$page}}">编辑</a> | 
            <a href="javascript:drop_confirm('del', {{$item.good_id}}, '{{$item.good_name}}')">删除</a> |
            <a href="javascript:drop_confirm('{{if $item.is_top eq 0}}top{{elseif $item.is_top eq 1}}un-top{{/if}}', {{$item.good_id}}, '{{$item.good_name}}')">{{if $item.is_top eq 0}}置顶{{elseif $item.is_top eq 1}}取消置顶{{/if}}</a> | 
            {{if $item.good_status eq 0}}<a href="/admin/goodshop/audit/gname:{{$item.good_name}}/{{$page_str}}/gid:{{$item.good_id}}/page:{{$page}}">审核</a> |{{/if}}
            <a href="javascript:jumpToLog('good', {{$item.good_id}})">记录</a>
      </td>
      {{/if}}
    </tr>
   {{foreachelse}}
    <tr class="no_data">
      <td colspan="{{if $request.isd eq 0}}8{{else}}7{{/if}}">暂无数据</td>
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
$(function(){
	{{if $request.region_id}}$('#region_id').val({{$request.region_id}});{{/if}}
	{{if $request.circle_id}}$('#circle_id').val({{$request.circle_id}});{{/if}}
	{{if $request.shop_id}}$('#shop_id').val({{$request.shop_id}});{{/if}}
	
	$('#region_id').change(function(){
		var _this = $('#circle_id');
		_this.empty();
		_this.append($("<option>").text('全部').val(''));
		$('#shop_id').empty();
		$('#shop_id').append($("<option>").text('全部').val(''));
		$.post('/admin/good/get-circle', {id:$(this).val()}, function(obj){
			var data = eval('(' + obj + ')');
			$.each(data, function(i, s){
				_this.append($("<option>").text(s.name).val(s.id));
			});
		});
	});
	
	$('#circle_id').change(function(){
		var _this = $('#shop_id');
		_this.empty();
		_this.append($("<option>").text('全部').val(''));
		$.post('/admin/good/get-shop', {region_id:$('#region_id').val(), circle_id:$('#circle_id').val()}, function(obj){
			var data = eval('(' + obj + ')');
			$.each(data, function(i, s){
				_this.append($("<option>").text(s.name).val(s.id));
			});
		});
	});
});
function drop_confirm(act, id, gname) {
	var content;
	var page_str = $('#page_str').val();
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
		case 'top': content = '确认置顶？'; break;
		case 'un-top': content = '确认取消置顶？'; break;
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
			if(!page_str) {
				location.href  = url + '/id:' + id +　'/gname:' + gname +　'/page:' +  $('#page').val();	
			} else {
				location.href  = url + '/id:' + id +　'/gname:' + gname + '/' + page_str +　'/page:' +  $('#page').val();	
			}
		},
		cancel: true
	});
}
</script>
{{include file='admin/footer.php'}}