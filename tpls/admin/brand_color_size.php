{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/css/JqueryDialog/jquery-ui-1.10.4.custom.css" />
<script type="text/javascript" src="/js/JqueryDialog/jquery-ui-1.10.4.custom.js"></script>
<script type="text/javascript">
var validArray = ['type' , 'name', 'number'];
$(function(){	
	
	for(var i=0; i<validArray.length; i++) {
		var _id = validArray[i];
		if(_id == 'type') {
			$('input[name=type]').bind('click',function(){
				validInput($(this).attr("name"));
			});			
		} else {
			$('#' + _id).bind('blur',function(){
				validInput($(this).attr("id"));
			});
		}
	}
	
	function validInput(id) {
		var _msg = '';
		var _is_radio = 0;
		switch(id) {
			case 'type':
					if(!$('input[name=' + id + ']:checked').val()) {
						_msg = '请选择分类';	
					}
					_is_radio = 1;
				break;
			case 'name':
					if($('#' + id).val().length == 0) {
						_msg = '请输入名称';	
					}
				break;
			case 'number':
					if($('#' + id).val().length == 0) {
						_msg = '请输入编号';	
					}
				break;			
		}
		if(_is_radio == 1) {
			if(_msg == '') {
				$('input[name=' + id + ']').closest("td").children("label").attr('class', 'field_notice').html('');
			} else {
				$('input[name=' + id + ']').closest("td").children("label").attr('class', 'error').html(_msg);
				return false;
			}
			return true;			
		} else {
			if(_msg == '') {
				$('#' + id).closest("td").children("label").attr('class', 'field_notice').html('');
			} else {
				$('#' + id).closest("td").children("label").attr('class', 'error').html(_msg);
				return false;
			}
			return true;
		}
	}

	$(".btn2").click(function(){
		$('.info').dialog({
			title : '新增',
			width : 600,
			height : 250,
			buttons: {
				'确定': function() {
					var len = 0;
					for(var i=0; i<validArray.length; i++) {
						if(validInput(validArray[i])) {
							len++;
						}
					}
					var _mid = $("#mid").val();
					var _brand_id = $("#brand_id").val();
					var _type = $("input[name=type]:checked").val();
					var _name = $("#name").val();
					var _number = $("#number").val();
					var _page = $("#page").val();
					
					if(len == validArray.length) {
						$.ajax({
							type:'POST',
							url:'/admin/brand/color-size',
							data:{bid : _brand_id, type:_type, name:_name, number:_number},
							dataType:'json',
							success:function(data){
								if(data.res == 100){
									window.location = '/admin/brand/color-size/bid:' + _brand_id + '/page:' + _page;
								}
							}
						});
					}
				}
			}	
		});
	});	
	

 $('#ConfirmMessage').dialog({
	   autoOpen: false,
	   width: 300,
	   //modal: true,
	   buttons: {
		   "确定": function() {
				$(this).dialog('close');
				mDialogCallback();
		   }
	   }
   });
}); 

function mDialogCallback() {
	$.ajax({
		type:'POST',
		url:'/admin/brand/ajax-color-size-del',
		data:{mid : mid},
		dataType:'json',
		success:function(data){
			if(data.res == 100){
				$("#color_size_" + mid).closest("tr").remove();
			}
		}
	});	
}
var mid;
function ShowMsg(id) {
	mid = id;
	$('#ConfirmMessageBody').html('确认删除？');
	$('#ConfirmMessage').dialog('open');
};
</script>

<div id="rightTop">
    <p>品牌管理</p>
    <ul class="subnav">
        <li><a class="btn1" href="/admin/brand/list/page:{{$page}}">品牌管理</a></li>
        <li><span>颜色尺码</span></li>
        <li><a class="btn2" href="javascript:;">新增</a></li>
    </ul>
</div>
<div class="info" style="display:none">
<form method="POST" id="brand_sku_form">
	<input type="hidden" name="mid" id="mid" value="{{$row.id}}" />
    <input type="hidden" name="brand_id" id="brand_id" value="{{$brand_id}}" />
    <input type="hidden" name="page" id="page" value="{{$page}}" />
    <table class="infoTable">
        <tr>
            <th class="paddingT15"> 分类:</th>
            <td class="paddingT15 wordSpacing5" >
                <input  type="radio" name="type" value="1" {{if $row.type eq 1}}checked="checked"{{/if}} /> 颜色
                <input  type="radio" name="type" value="2" {{if $row.type eq 2}}checked="checked"{{/if}} /> 尺码
                <label class="field_notice"></label>
            </td>
            <td></td>
        </tr>
        <tr>
            <th class="paddingT15" width="200"> 名称:</th>
            <td class="paddingT15 wordSpacing5" width="40%">
                <input class="infoTableInput2" id="name" type="text" name="name" value="{{$row.name}}" />
                <label class="field_notice"></label>
            </td>
            <td></td>
        </tr>
        <tr>
            <th class="paddingT15"> 编号:</th>
            <td class="paddingT15 wordSpacing5">
                <input class="infoTableInput2" id="number" type="text" name="number" value="{{$row.number}}" />
                <label class="field_notice"></label>
            </td>
            <td></td>
        </tr>
       <tr>
            <th class="paddingT15"> </th>
            <td class="ptb20"></td>
        </tr>
    </table>
</form>
</div>

<div class="tdare">
  <table width="600" cellspacing="0" class="dataTable">
    <tr class="tatr1">
      <td>分类</td>
      <td>名称</td>
      <td>编号</td>
      <td>操作</td>
    </tr>
    {{foreach from=$data item=item}}
    <tr class="tatr2">
      <td>{{if $item.type eq 1}}颜色{{elseif $item.type eq 2}}尺寸{{/if}}</td>
      <td>{{$item.name}}</td>
      <td>{{$item.number}}</td>
      <td>      	
      	<a href="javascript:ShowMsg({{$item.id}})" id="color_size_{{$item.id}}">删除</a>
      </td>  
    </tr>
    {{foreachelse}}
    <tr class="no_data">
      <td colspan="4">暂无记录</td>
    </tr>
  {{/foreach}}
  </table>
</div>

<div id="AlertMessage" title="信息确认" style="display:none">
	<p id="AlertMessageBody"  class="msgbody"></p>
</div>

<div id="ConfirmMessage" title="信息提问" style="display:none">
	<p id="ConfirmMessageBody" class="msgbody"></p>
</div>
                   
{{include file='admin/footer.php'}}