{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<div id="rightTop">
  <p>店铺管理</p>
  <ul class="subnav">
    <li><span>店铺列表</span></li>
    <li><a class="btn1" href="/admin/shop/add">新建店铺</a></li>
    <!--<li><a class="btn1" href="/admin/shop/merge">合并店铺</a></li>-->
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
<!--            用户名：
            <input class="queryInput" type="text" name="uname" value="{{$request.uname}}" />-->
            店铺名称：
            <input class="queryInput" type="text" name="title" value="{{$request.title}}" />
            <!--<input class="querySelect" type="radio" name="isd" value="1" {{if $request.isd eq 1}}checked="checked"{{/if}} /> 删除-->　
            <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/shop/list">撤销检索</a>
    </form>
  </div>
<div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    <tr class="tatr1">
      <td width="20" class="firstCell"><input type="checkbox" class="checkall" /></td>
      <td width="50">店铺ID</td>
      <td>店铺名称</td>
      <td>地址</td>
      <td>经纬度</td>
      <td>联系电话</td>
	  <td>录入时间</td>
	  <td>状态</td>
	  <td class="table-center" title="已审核的商品/未审核的商品">商品数量</td>
      {{if $request.isd eq 0}}
      <td>操作</td>
      {{/if}}
    </tr>
    {{foreach from=$data key=key item=item}}
    <tr class="tatr2">
      <td class="firstCell"><input type="checkbox" class="checkitem" value="{{$item.shop_id}}" /></td>
      <td>{{$item.shop_id}}</td>
      <td>{{$item.shop_name}}</td>
      <td>{{$item.shop_address}}</td>
      <td>{{if $item.lng > 0 && $item.lat > 0}}{{$item.lng}}， {{$item.lat}}{{/if}}</td>
      <td>{{$item.phone}}</td>
      <td>{{if $item.created}}{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}{{/if}}</td>
      <td>
      		{{if $item.shop_status eq '-1'}}审核不通过
      		{{elseif $item.shop_status eq '0'}}未审核
			{{elseif $item.shop_status eq '1'}}已审核
            {{/if}}
      </td>
      <td class="table-center">{{$item.through}} / {{$item.total}} </td>
      {{if $request.isd eq 0}}
      <td>
      		<a href="/admin/shop/edit/sid:{{$item.shop_id}}/page:{{$page}}">编辑</a> | 
            <!--<a href="javascript:drop_confirm('del', {{$item.shop_id}}, '{{$item.shop_name}}')">删除</a> |--> 
            {{if $item.shop_status eq 0}}<a href="/admin/shop/audit/sid:{{$item.shop_id}}/page:{{$page}}">审核</a> |{{/if}}
            <a href="/admin/shop/staff-management/sid:{{$item.shop_id}}/page:{{$page}}">店员管理</a> |
            <a href="javascript:jumpToLog('shop', {{$item.shop_id}})">记录</a>
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
    	<!--{{if $request.isd eq 1}}
        <input class="formbtn" type="button" value="取消删除" onClick="drop_confirm('un-del')" />
        {{else}}
        <input class="formbtn" type="button" value="删除" onClick="drop_confirm('del-all')" />
        <input class="formbtn2" type="button" value="加入合并序列" onClick="addMerge()" />
        <input class="formbtn2" type="button" value="清空合并序列" onClick="moveMerge()" />
        {{/if}}-->
    </div>
    <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
    <div class="clear"></div>
  </div>
</div>
<script type="text/javascript">
function drop_confirm(act, id, sname) {
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
		case 'recommend': content = '确认推荐？'; break;
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
			location.href  = url + '/id:' + id + '/sname:' + sname +　'/page:' +  $('#page').val();	
		},
		cancel: true
	});
}

function addMerge() {
	if($('.checkitem:checked').length == 0){
		alert('请选择合并对象');
		return false;
	}
   
	var items = '';
	$('.checkitem:checked:enabled').each(function(){
		items += this.value + ',';
	});
	items = items.substr(0, (items.length - 1));
	$.post('/admin/shop/set-shop-seq', {sid:items}, function(data){
		if(data == 'ok'){
			$.dialog({
				title : '结果',
				content : '加入合并序列成功!', 
				ok : function () {location.href = '/admin/shop/list/page:' + $('#page').val();},
				cancel : false
			});
		}
	});
}

function moveMerge() {
	$.post('/admin/shop/uset-shop-seq', {}, function(data){
		if(data == 'ok'){
			$.dialog({
				title : '结果',
				content : '清空序列成功!', 
				ok : function () {location.href = '/admin/shop/list/page:' + $('#page').val();},
				cancel : false
			});
		}
	});
}
</script>
{{include file='admin/footer.php'}}