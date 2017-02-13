{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<script type="text/javascript">
$(function(){
	if($('.radioitem:checked').length > 0){
       $('input.radioitem').attr('disabled', true);
    }	
});

function submit_merge() {
	var ck = [];
	var mNameString = cNameString = '';
	var mId = cId = '';
	if(!$('.radioitem:checked').length) {
		alert('必须选择一个主店铺');
		return false;
	}
	
	$('input.radioitem').each(function(index, element) {
    	 if($(this).attr('checked') == 'checked') {
		 	mId = $(this).attr('value');
			mNameString = $(this).attr('sname');
		 } else {
		 	cId += $(this).attr('value') + ',';
			cNameString += $(this).attr('sname') + ',';
		 }
    });
	$.dialog({
		title: '提示',
		content: '确定要把 <span style="color:red">“' + cNameString + '“</span> 合并到 <span style="color:red">“' + mNameString + '“</span> ，请注意该操作不可逆转' ,
		okValue: '确定',
		ok: function () {
			location.href = '/admin/shop/fix-merge/mNameString:' +  mNameString + '/mId:' + mId + '/cNameString:' + cNameString + '/cId:' +  cId
		},
		cancelValue: '取消',
		cancel : true
	});	
}

function dropShopSession(sid) {
	$.dialog({
		title: '提示',
		content: '确定删除？' ,
		okValue: '确定',
		ok: function () {
			$.post('/admin/shop/del-shop-session', {sid:sid}, function(data){
				if(data == 'ok') {
					$('tr#del_' + sid).remove();
				}
			});
		},
		cancelValue: '取消',
		cancel : true
	});		
}

$.fn.loadSelect = function(optionsDataArray){
	return this.emptySelect().each(function(){
		if(this.tagName == 'SELECT'){
			var selectElement = this;
			$.each(optionsDataArray,function(index,optionData){
				var option = new Option(optionData.name,optionData.id);
				if($.browser.msie){
					selectElement.add(option);
				}else{
					selectElement.add(option,null);
				}
			});
		}
	});
}

$.fn.emptySelect = function(){
	return this.each(function(){
		if(this.tagName == 'SELECT') this.options.length = 0;
	});
}
</script>
<div id="rightTop">
  <p>店铺管理</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/shop/list">店铺列表</a></li>
    <li><a class="btn1" href="/admin/shop/add">新建店铺</a></li>
    <li><span>合并店铺</span></li>
  </ul>
</div>

<div class="tdare">
<table width="600" cellspacing="0" class="dataTable">
	<tr class="tatr1">
    	<td width="20" class="firstCell"></td>
    	<td>店铺名称</td>
        <td>店铺地址</td>
        <td></td>
    </tr>
    {{foreach from=$data key=key item=item}}
	<tr class="tatr2" id="del_{{$item.shop_id}}">
    	<td><input type="radio" class="radioitem" name="ck" value="{{$item.shop_id}}" {{if $item.is_main eq '1'}}checked="checked"{{/if}} sname="{{$item.shop_name}}" /></td>
        <td>{{$item.shop_name}}</td>
        <td>{{$item.shop_address}}</td>
        <td class="table-center"><a href="javascript:dropShopSession({{$item.shop_id}})">删除</a></td>   
    </tr>
    {{/foreach}}
    <tr>
    	<td colspan="4"><input type="button" class="formbtn2" value="确定合并" onClick="submit_merge()" /></td>
    </tr>
</table>
</div>
{{include file='admin/footer.php'}}