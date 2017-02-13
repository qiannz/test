{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<script type="text/javascript">
$(function(){	
	attachAddButtonEvent('addFirst', 'moveFrom', "moveMiddle", '请选择要添加的对象!', false);
	attachDeleteButtonEvent('deleteFirst', 'moveFrom', "moveMiddle", "请选择要删除的对象");
	
	attachAddButtonEvent('addSecond', 'moveMiddle', "moveTo", '请选择要添加的对象!', true);
	attachDeleteButtonEvent('deleteSecond', 'moveMiddle', "moveTo", "请选择要删除的对象");
});

/*attachAddButtonEvent：给add按钮添加事件*/
 function attachAddButtonEvent(addButtonId, candidateListId, selectedListId, msg, isDel) {
	$(function() {
		$("#" + addButtonId).click(function() {
			if ($("#" + candidateListId + " option:selected").length > 0)
			{
				$("#" + candidateListId + " option:selected").each(function() {
					var id = $(this).val();
					var flag = false;
					$("#" + selectedListId + " option").each(function() {
						if($(this).val() == id) {
							flag = true;
						}
					});
					if(flag) {
						alert('对不起，目标重复');
					} else {
						if(isDel) {
							$(this).remove();
						}
						$("#" + selectedListId).append("<option value='" + $(this).val() + "'>" + $(this).text() + "</option");
					}
				})
			}
			else
			{
				alert(msg);
			}
		})
	})
}

/*attachDeleteButtonEvent：给delet按钮添加事件*/
function attachDeleteButtonEvent(deleteButtonId, candidateListId, selectedListId, msg) {
	$(function() {
		$("#" + deleteButtonId).click(function() {
			if ($("#" + selectedListId + " option:selected").length > 0)
			{
				$("#" + selectedListId + " option:selected").each(function() {
					$(this).remove();
				})
			}
			else
			{
				alert(msg);
			}
		})
	})
}

function search_to() {
	var sname = $("#search").val();
	if(sname == ''){
		alert('搜索条件不能为空');
		return false;
	}
	$.post("/admin/shop/search",{sname:sname},function(data){
		var data = eval('(' + data + ')');
		$("#moveFrom").loadSelect(data);
	});
}

function submit_merge() {
	var mid = $("#moveMiddle")[0].options.length;
	var to =  $("#moveTo")[0].options.length;
	var midStr = '';
	var midNameStr = '';
	var toStr = '';
	var toNameStr = '';
	
	if(mid == 0) {
		alert('请选择待合并店铺');
		return false;
	}
	
	if(to == 0) {
		alert('请选择待主店铺');
		return false;		
	}
	
	if(to > 1) {
		alert('主店铺有且只能有一个');
		return false;	
	}
	
	$("#moveMiddle option").each(function() {
		midStr += $(this).val() + ',';
		midNameStr += $(this).text() + ',';
	});
	
	$("#moveTo option").each(function() {
		toStr += $(this).val() + ',';
		toNameStr += $(this).text() + ',';
	});
	
	$.dialog({
		title: '提示',
		content: '确定要把 <span style="color:red">“' + midNameStr + '“</span> 合并到 <span style="color:red">“' + toNameStr + '“</span> ，请注意该操作不可逆转' ,
		okValue: '确定',
		ok: function () {
			location.href = '/admin/shop/fix-merge/midStr:' +  midStr + '/midNameStr:' + midNameStr + '/toStr:' + toStr + '/toNameStr:' +  toNameStr
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
<style type="text/css">
.formbtn2 {
	background-color:#FC6;
    border: 1px solid;
    color: #FFF;
	font-size:24px;
    cursor: pointer;
    height: 60px;
    margin: 0 2px 0 0px !important;
    text-align: center;
    text-decoration: none;
    width: 200px;
}
</style>
<div id="rightTop">
  <p>店铺管理</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/shop/list">店铺列表</a></li>
    <li><a class="btn1" href="/admin/shop/add">新建店铺</a></li>
    <li><span>合并店铺</span></li>
  </ul>
</div>

<div class="mrightTop">
  <div class="fontl">
       <div class="left">
        店铺名称：<input type="text" name="search" id="search" style="width:200px;"/>
      	<input class="formbtn" type="button" value="搜索" onClick="search_to()" />
      </div>
  </div>
</div>

<div class="info">
<table class="dataTable">
	<tr class="tatr1">
    	<td class="table-center">搜索结果</td>
    	<td width="100"></td>
        <td class="table-center">待合并店铺</td>
        <td width="100"></td>
        <td class="table-center">主店铺</td>
    </tr>
	<tr class="tatr2">
    	<td><select style="height:300px; width:200px;" multiple="multiple" name="moveFrom" id="moveFrom"></select></td>
        <td>
            <table border="0" cellspacing="1" cellpadding="0" width="150">
                <tr><td style="text-align:center; height:60px; line-height:60px;">
                <input type="button" value="添　加" id="addFirst"/></td></tr>
                <tr><td style="text-align:center; height:60px; line-height:60px;">
                <input type="button" value="删　除" id="deleteFirst"/></td></tr>
            </table>
        </td>
        <td>
            <select style="height:300px; width:200px;" multiple="multiple" name="moveMiddle" id="moveMiddle"></select>
        </td>
    	<td>
            <table border="0" cellspacing="1" cellpadding="0" width="150">
                <tr><td style="text-align:center; height:60px; line-height:60px;">
                <input type="button" value="添　加" id="addSecond"/></td></tr>
                <tr><td style="text-align:center; height:60px; line-height:60px;">
                <input type="button" value="删　除" id="deleteSecond"/></td></tr>
            </table>        
        </td>
        <td>
        	<select style="height:300px; width:200px;" multiple="multiple" name="moveTo" id="moveTo"></select>
        </td>
    </tr>
    <tr>
    	<td colspan="5"><input type="button" class="formbtn2" value="确定合并" onClick="submit_merge()" /></td>
    </tr>
</table>
</div>
{{include file='admin/footer.php'}}