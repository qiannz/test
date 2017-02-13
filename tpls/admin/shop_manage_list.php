{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<div id="rightTop">
  <p>店铺管理</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/shop/list/page:{{$page}}">店铺列表</a></li>
    <li><a class="btn1" href="/admin/shop/add-staff/sid:{{$row.shop_id}}/page:{{$page}}">新增店员</a></li>
    <li><span>{{$row.shop_name}}</span></li>
  </ul>
</div>

<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="800" cellspacing="0" class="dataTable">
    <tr class="tatr1">
      <td>用户名</td>
      <td>手机号码</td>
      <td>真实姓名</td>
      <td>身份</td>
      <td>创建时间</td>
      <td>操作</td>
    </tr>
    {{foreach from=$data key=key item=item}}
    <tr class="tatr2">
      <td>{{$item.user_name}}</td>
      <td>{{$item.mobile}}</td>
      <td>{{$item.real_name}}</td>
      <td>{{if $item.user_type eq 1}}普通店员{{elseif $item.user_type eq 2}}店长{{elseif $item.user_type eq 3}}收银员{{/if}}</td>
      <td>{{if $item.created}}{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}{{/if}}</td>
      <td>
            {{if $item.user_type eq 1}}
            <a href="/admin/shop/set-shop-manager/sid:{{$item.shop_id}}/uid:{{$item.user_id}}/page:{{$page}}">设为店长</a> | 
            {{/if}}
            <a href="javascript:drop_confirm('del-shop-manager', '{{$item.shop_id}}', '{{$item.user_id}}')">删除</a> 
      </td>
    </tr>
   {{foreachelse}}
    <tr class="no_data">
      <td colspan="6">暂无数据</td>
    </tr>
    {{/foreach}}
  </table>
</div>
<script type="text/javascript">
function drop_confirm(act, sid, uid) {
	var content;
	switch(act) {
		case 'del-shop-manager': content = '确认删除该店员？'; break;
	}
	
	$.dialog({
		title:'警告',
		content: content,
		ok: function() {
			var url = '/' + _M + '/' + _C + '/' + act;
			location.href  = url + '/sid:' + sid + '/uid:' + uid +　'/page:' +  $('#page').val();	
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