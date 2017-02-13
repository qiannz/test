{{include file='admin/header.php'}}
<style type="text/css">
	.choiceBox a{position:relative;}
	.choiceBox a img {position: absolute;top: -6px;right: -6px;width: 20px;cursor:pointer;}
</style>
<div id="rightTop">
  <p>券管理</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/crowdfunding/list/page:{{$page}}">一元众筹</a></li>
    {{if !$row.ticket_id}}<li><a class="btn3" href="/admin/crowdfunding/user-shop/uname:{{$uname}}">店铺选择</a></li>{{/if}}
    <li><span>{{if $row.ticket_id}}编辑众筹{{else}}新建众筹{{/if}}</span></li>
  </ul>
</div>
<div class="info">
<form method="POST" action="{{$_CONF.FORM_ACTION}}" id="form" enctype="multipart/form-data">
<input type="hidden" name="sid" id="sid" value="{{$sid}}" />
<input type="hidden" name="user_name" id="user_name" value="{{$uname}}" />
<input type="hidden" name="tid" id="tid" value="{{$row.ticket_id}}" />
<input type="hidden" name="page" id="page" value="{{$page}}" />
<input type="hidden" name="sids" id="sids" value="" />
<div id="tabs">
	<ul>
		<li><a href="#tabs-1">众筹详情</a></li>
		<li><a href="#tabs-2">众筹图片</a></li>
	</ul>
	<div id="tabs-1">
        <table class="infoTable">
            <tr>
                <th class="paddingT15">所属店铺:</th>
                <td class="paddingT15 wordSpacing5">
                 {{$row.region_name}} {{$row.circle_name}} {{$row.shop_name}}
                </td>
            </tr>
<!--        <tr>
                <th class="paddingT15">关联店铺:</th>
                <td class="paddingT15 wordSpacing5">
                    <a href="javascript:chooseRelationShop()">点击选择关联店铺</a>
                </td>
            </tr>
            <tr>
                <th class="paddingT15"></th>
                <td class="paddingT15 wordSpacing5">
                <span id="choiceBoxShop" class="choiceBox">
                {{foreach from=$ticketRelationShopArray key=key item=item}}
                <a><span>{{$item.shop_name}}</span><img class="shopDel" src="/images/delete.png" data-sid = "{{$item.shop_id}}"/></a>
                {{/foreach}}
                </span>
                </td>
            </tr>--> 
            <tr>
                <th class="paddingT15">众筹标题:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableFile" type="text" name="ticket_title" id="ticket_title" value="{{$row.ticket_title}}" />
                  <label class="field_notice">30字以内</label>
                </td>
            </tr>  
            <tr>
                <th class="paddingT15">众筹分类:</th>
                <td class="paddingT15 wordSpacing5">
                    <select name="ticket_sort" id="ticket_sort" class="querySelect">
                        <option value="">请选择众筹分类</option>    	
                        {{foreach from=$storeArray key=key item=item}}
                        <option value="{{$key}}" {{if $row.ticket_sort eq $key}}selected="selected"{{/if}}>{{$item}}</option>
                        {{/foreach}}
                    </select>
                    <label class="field_notice"></label>
                </td>    	
            </tr>
            <tr>
                <th class="paddingT15">销售平台:</th>
                <td class="paddingT15 wordSpacing5">
                    <input type="checkbox" class="querySelect" name="CanWeb" id="CanWeb" {{if $row.CanWeb eq 1}} checked="checked" {{/if}} value="1" />WEB
                    <input type="checkbox" class="querySelect" name="CanWap" id="CanWap" {{if $row.CanWap eq 1}} checked="checked" {{/if}} value="1" />WAP
                    <input type="checkbox" class="querySelect" name="CanApp" id="CanApp" {{if $row.CanApp eq 1}} checked="checked" {{/if}} value="1" />APP
                </td>    	
            </tr>    
            <tr>
                <th class="paddingT15">限购:</th>
                <td class="paddingT15 wordSpacing5">
                  <input type="checkbox" class="querySelect" name="UserNameLimit" id="UserNameLimit" {{if $row.UserNameLimit eq 1}} checked="checked" {{/if}} value="1" />每用户
                  <input type="checkbox" class="querySelect" name="MobileLimit" id="MobileLimit" {{if $row.MobileLimit eq 1}} checked="checked" {{/if}} value="1" />每手机
                  <input class="infoTableInput2" type="text" name="climit" id="climit" value="{{$row.limit_count}}" /> 件 / 
                  <select name="unit" id="unit">
                        <option value="Activity" {{if $row.limit_unit eq 'Activity'}} selected="selected" {{/if}}>场</option>
                        <option value="Hour" {{if $row.limit_unit eq 'Hour'}} selected="selected" {{/if}} >小时</option>
                        <option value="Day" {{if $row.limit_unit eq 'Day'}} selected="selected" {{/if}} >天</option>
                        <option value="Week" {{if $row.limit_unit eq 'Week'}} selected="selected" {{/if}} >周</option>
                        <option value="Weekly" {{if $row.limit_unit eq 'Weekly'}} selected="selected" {{/if}} >自然周</option>
                        <option value="Month" {{if $row.limit_unit eq 'Month'}} selected="selected" {{/if}}>月</option>
                        <option value="Monthly" {{if $row.limit_unit eq 'Monthly'}} selected="selected" {{/if}}>自然月</option>
                        <option value="Minutes" {{if $row.limit_unit eq 'Minutes'}} selected="selected" {{/if}}>分钟</option>
                    </select>  
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">销售有效期:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableFile2" style="width:140px;" type="text" name="sdate" id="sdate" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm'})" value="{{if $row.start_time}}{{$row.start_time|date_format:'%Y-%m-%d %H:%M'}}{{/if}}" /> - 
                  <input class="infoTableFile2" style="width:140px;" type="text" name="edate" id="edate" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm',minDate:'#F{$dp.$D(\'sdate\',{d:7})}'})" value="{{if $row.end_time}}{{$row.end_time|date_format:'%Y-%m-%d %H:%M'}}{{/if}}"/>
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">使用有效期:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableFile2" style="width:140px;" type="text" name="stime" id="stime" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm'})" value="{{if $row.valid_stime}}{{$row.valid_stime|date_format:'%Y-%m-%d %H:%M'}}{{/if}}" /> - 
                  <input class="infoTableFile2" style="width:140px;" type="text" name="etime" id="etime" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm',minDate:'#F{$dp.$D(\'stime\',{d:1})}'})" value="{{if $row.valid_etime}}{{$row.valid_etime|date_format:'%Y-%m-%d %H:%M'}}{{/if}}"/>
                  <label class="field_notice"></label>
                </td>
            </tr>     
            <tr>
                <th class="paddingT15">抽奖时间:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableFile2" style="width:140px;" type="text" name="lottery_time" id="lottery_time" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm'})" value="{{if $row.lottery_time}}{{$row.lottery_time|date_format:'%Y-%m-%d %H:%M'}}{{/if}}" />
                  <label class="field_notice"></label>
                </td>
            </tr>  
            <tr>
                <th class="paddingT15">支付超时时间:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="expiration_minute" id="expiration_minute" value="{{if $row.expiration_minute}}{{$row.expiration_minute}}{{else}}10{{/if}}" />
                  <label class="field_notice">单位：分钟</label>
                </td>
            </tr>     
            <tr>
                <th class="paddingT15">数量:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="total" id="total" value="{{$row.total}}" />
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">原价:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="p_value" id="p_value" value="{{$row.par_value}}" />
                  <label class="field_notice">元</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">众筹价:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="s_value" id="s_value" value="{{if $row.selling_price}}{{$row.selling_price}}{{else}}1{{/if}}" />
                  <label class="field_notice">元</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">众筹简介:</th>
                <td class="paddingT15 wordSpacing5">
                  <textarea id="ticket_summary" name="ticket_summary">{{$row.ticket_summary}}</textarea>
                  <label class="field_notice">120个字符之内(汉字算一个字符)</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">封面图片:</th>
                <td class="paddingT15 wordSpacing5">
                  <input type="file" class="file" name="file_img" id="file_img" />
                  <label class="field_notice">图片尺寸 750 * 350</label>
                </td>
            </tr>
            {{if $row.cover_img}}
            <tr>
                <th class="paddingT15"></th>
                <td class="paddingT15 wordSpacing5">
                  <img src="{{$_CONF.IMG_URL}}/buy/cover/{{$row.cover_img}}" />
                </td>
            </tr>
            {{/if}}            
            <tr>
                <th class="paddingT15">众筹详情:</th>
                <td class="paddingT15 wordSpacing5">
                  <textarea id="wap_content" name="wap_content" style="width:600px; height:300px;">{{$row.wap_content}}</textarea>
                  <label class="field_notice"></label>
                </td>
            </tr>            
            <tr>
                <th class="paddingT15">上下架:</th>
                <td class="paddingT15 wordSpacing5" >
                    
                    <input type="radio" name="is_auth" value="0" {{if $row.is_auth|_isset && $row.is_auth eq 0}}checked="checked"{{/if}} />下架
                    <input type="radio" name="is_auth" value="1" {{if $row.is_auth|_isset && $row.is_auth eq 1}}checked="checked"{{/if}} />上架
                    <label class="field_notice"></label>
                </td>
            </tr> 
            <tr>
                <th class="paddingT15">是否显示:</th>
                <td class="paddingT15 wordSpacing5" >
                    <input type="radio" name="is_show" value="0" {{if $row.is_show|_isset && $row.is_show eq 0}}checked="checked"{{/if}} />否
                    <input type="radio" name="is_show" value="1" {{if $row.is_show|_isset && $row.is_show eq 1}}checked="checked"{{/if}} />是
                    <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">关注数:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="love_number" id="love_number" value="{{if $row.love_number}}{{$row.love_number}}{{else}}0{{/if}}" />
                  <label class="field_notice">关注数伪造偏移量</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">中奖手机:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="no_winning" id="no_winning" value="{{$row.no_winning}}" />
                  <label class="field_notice">默认中奖手机</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">中奖用户名:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="winning_user_name" id="winning_user_name" value="{{$row.winning_user_name}}" />
                  <label class="field_notice">默认用户名</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">中奖倍率:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="count_times" id="count_times" value="{{$row.count_times}}" />
                  <label class="field_notice">一份发几个中奖码</label>
                </td>
            </tr>
            <tr>
            	<th class="paddingT15">购买优惠:</th>
                <td class="paddingT15 wordSpacing5">
                  <select name="buy_discount" id="buy_discount">
                    <option value="0">无</option>
				  	{{foreach from=$buyDiscount item=item key=key}}
                    <option value="{{$key}}" {{if $key eq $row.buy_discount}}selected="selected"{{/if}}>{{$item}}</option>
                    {{/foreach}}	                  
                  </select>
                  <label class="field_notice"></label>
                </td>
            </tr>
        </table>    
    </div>
    <div id="tabs-2">
    <table class="infoTable" id="gallery-table">
        <tbody>
            <tr>
                <td class="paddingT15"><a onclick="addImg(this)" href="javascript:;">[+]</a> 上传图片</td>
                <td class="paddingT15 wordSpacing5"><input type="file" class="file" name="img_url[]" /></td>
            </tr>
        </tbody>
    </table>
    {{if $wapImgData}}
    <table class="infoTable">
        <tbody>
          <tr>
              <td width="150" class="paddingT15 wordSpacing5">排序</td>
              <td class="paddingT15 wordSpacing5">图片</td>
              <td class="paddingT15 wordSpacing5">创建时间</td>
              <td class="paddingT15 wordSpacing5">操作</td>
          </tr>
          {{foreach from=$wapImgData key=key item=item}} 
          <tr id="imgCol_{{$item.id}}">
              <td class="paddingT15 wordSpacing5"><span ectype="inline_edit" fieldname="sequence" fieldid="{{$item.id}}" required="1" class="node_name editable" action="img-ajax-col">{{$item.sequence}}</span></td>
              <td class="paddingT15 wordSpacing5"><img src="{{$_CONF.IMG_URL}}/buy/crowdfunding/{{$item.img_url}}" class="makesmall" max_width="400" /></td>
              <td class="paddingT15 wordSpacing5">{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
              <td class="paddingT15 wordSpacing5"><a href="javascript:delConfirm({{$item.id}}, '确认删除','你确定要删除这个图片吗？')">删除</a></td>
          </tr>
          {{/foreach}}
   	 	</tbody>
	</table>
    {{/if}}
    </div>
    <table class="infoTable">
        <tr>
            <th class="paddingT15"> </th>
            <td class="ptb20">
                {{if $row.ticket_id}}
                <input type="button" value="编辑" name="Submit" class="formbtn1" onClick="checkSubmit()">
                <input type="reset" value="重置" name="reset" class="formbtn2">
                {{else}}
                <input type="button" value="提交" name="Submit" class="formbtn1" onClick="checkSubmit()">
                {{/if}}
            </td>
        </tr>    
    </table>
</div>
</form>
</div>

<div class="selectPopupShop" style="display:none" id="selectPopupShop">
<a class="selectPopupShop-button" onclick="document.getElementById('selectPopupShop').style.display = 'none'">关闭</a>
<table width="800" cellspacing="0" class="dataTable">
	<tbody>
      <tr>
        <td class="paddingT15 wordSpacing5">
          <p>
            <label><input type="radio" value="1" name="shop_type" checked="checked" /> 商圈</label>
            <label><input type="radio" value="2" name="shop_type" /> 商场</label>
            <label><input type="radio" value="3" name="shop_type" /> 品牌</label>
          </p>
        </td>
      </tr>    
      <tr>
        <td class="paddingT15 wordSpacing5">
        	<select name="region_id" id="region_id">
            	<option value="">请选择所在区</option>
                {{foreach from=$regionArray key=key item=item}}
                <option value="{{$key}}">{{$item}}</option>
                {{/foreach}}
            </select>
            <select name="related_id" id="related_id"></select>
            店铺: <input type="text" name="search_name" id="search_name" style="width:150px;" />
            <input type="button" class="formbtn" value="搜索店铺" onclick="search_to()" />
        </td>
      </tr>
      <tr>
          <td class="paddingT15 wordSpacing5">
          	<table class="infoTable">
            	<tr>
                    <th width="300">可选择店铺</th>
                    <th width="200"></th>
                    <th width="300">已选择店铺</th>
                </tr>
                <tr>
                    <td>
                        <select style="height:300px; width:100%" multiple="multiple" name="moveFrom" id="moveFrom"></select>
                    </td>
                    <td>
                      <table border="0" cellspacing="1" cellpadding="0" width="98%">
                            <tr><td style="text-align:center; height:60px; line-height:60px;">
                            <input type="button" value="添　加" id="add"/></td></tr>
                            <tr><td style="text-align:center; height:60px; line-height:60px;">
                            <input type="button" value="删　除" id="delete"/></td></tr>
                      </table>
                    </td>
                    <td>
                    <select style="height:300px; width:100%" multiple="multiple" name="moveTo" id="moveTo">
                    {{foreach from=$ticketRelationShopArray key=key item=item}}
                    <option value="{{$item.shop_id}}">{{$item.shop_name}}</option>
                    {{/foreach}}
                    </select>
                    </td>
            	</tr>                
            </table>          
          </td>
      </tr> 
      <tr>
        <td class="ptb20"><input type="button" value="确定" name="Submit" class="formbtn" onClick="shopTrue()"></td>
      </tr>
	</tbody> 
</table>
</div>

<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<link rel="stylesheet" type="text/css" href="/css/admin/upload.css" />
<link rel="stylesheet" type="text/css" href="/css/admin/popup.css" />
<link rel="stylesheet" type="text/css" href="/css/JqueryDialog/jquery-ui-1.9.2.custom.min.css" />
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" charset="utf-8" ></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" charset="utf-8" ></script>
<script type="text/javascript" src="/js/DatePicker/WdatePicker.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/admin/addImage.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/admin/swfupload/swfupload.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/admin/swfupload/swfupload.queue.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/admin/swfupload/handlers.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/kindeditor/kindeditor-min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/kindeditor/zh_CN.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/select.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/JqueryDialog/jquery-ui-1.9.2.custom.min.js" charset="utf-8" ></script>
<script type="text/javascript" src="/js/admin/inline_edit.js" charset="utf-8"></script>
<script type="text/javascript">
var validArray = ['ticket_title', 'ticket_sort', 'p_value', 's_value', 'sdate', 'edate', 'ticket_summary'];
$(function(){	
	for(var i=0; i<validArray.length; i++) {
		$('#' + validArray[i]).bind('blur',function(){
			validInput($(this).attr("id"));
		});
	}	

	$("#choiceBoxShop").on('click','.shopDel',function(){
		var _this = $(this);
		$.dialog({
			title:'警告',
			content: '是否确认取消该店铺与众筹的关联？',
			ok: function() {
				$.ajax({
					url:"/admin/ticket/shop-del",
					dataType:"json",
					data:{"tid" : $("#tid").val(), "sid": _this.attr("data-sid")},
					success:function(data){
						if(data.status == 'ok') {
							_this.parent("a").remove();
						}
					},
					error:function(){
					}
				});	
			},
			cancel: true
		});
	});	
	
	$( "#tabs" ).tabs();
});

$('.formbtn1,.formbtn2').attr("disabled", false);
function checkSubmit()
{
	var len = 0;
	for(var i=0; i<validArray.length; i++) {
		if(validInput(validArray[i])) {
			len++;
		}
	}	
	
	if(len == validArray.length) {
		$('.formbtn1').attr("value", "提交中...").attr("disabled", true);
		$("#form").submit();
	}
	
}

function chooseRelationShop() {
	$('#selectPopupShop').show();
}

function resetRelated(obj, stype) {
	if(stype == 1) {
		obj.append($("<option>").text('请选择商圈').val(''));
	} else if(stype == 2) {
		obj.append($("<option>").text('请选择商场').val(''));
	} else if(stype == 3) {
		obj.append($("<option>").text('请选择品牌').val(''));
	}			
}

$("input[name=shop_type]").click(function(){
	var _this;
	$('#region_id').val('');
	_this = $('#related_id');
	_this.empty();			
	resetRelated(_this, this.value);
});

$(function(){
	$('#region_id').val('');
	var stype = $("input[name=shop_type]:checked").val();
	resetRelated($('#related_id'), stype);
	
	attachAddButtonEvent('add', 'moveFrom', "moveTo", '请选择可选店铺!');
	attachDeleteButtonEvent('delete', 'moveFrom', "moveTo", "请选择要删除的店铺");
	
	$('#region_id').change(function(){
			var stype = $("input[name=shop_type]:checked").val();
			var _this = $('#related_id');
			_this.attr("disabled", false);
			_this.empty();
			resetRelated(_this, stype);
			if(this.value) {
				$.post('/admin/user/get-sel-list', {region_id:$(this).val(), stype : stype}, function(obj){
					var data = eval('(' + obj + ')');
					$.each(data, function(i, s){
						_this.append($("<option>").text(s.name).val(s.id));
					});
				});	
			}
	});			
});


function search_to() {
	var stype = $("input[name=shop_type]:checked").val();
	var related_id = $('#related_id').val();
	var region_id = $('#region_id').val();
	var sname = $('#search_name').val();
	var _this = $('#moveFrom');
	_this.empty();
	$.ajax({
		url:"/admin/user/get-shop-list",
		dataType:"json",
		data:{"stype":stype,"related_id":related_id, "region_id":region_id, "sname":sname},
		success:function(data){
			$.each(data, function(i, s){
				_this.append($("<option>").text(s.name).val(s.id));
			});											
		},
		error:function(){
		}
	})
}

/*attachAddButtonEvent：给add按钮添加事件*/
 function attachAddButtonEvent(addButtonId, candidateListId, selectedListId, msg) {
	$(function() {
		$("#" + addButtonId).click(function() {
			if ($("#" + candidateListId + " option:selected").length > 0)
			{
				$("#" + candidateListId + " option:selected").each(function() {
					$("#" + selectedListId).append("<option value='" + $(this).val() + "'>" + $(this).text() + "</option");
					//$(this).remove();
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
					//$("#" + candidateListId).append("<option value='" + $(this).val() + "'>" + $(this).text() + "</option");
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

function shopTrue(){	
	var shopIdStr = "";
	var shopTextStr = "";
	
	$("#moveTo option").each(function(){
		shopIdStr += $(this).val() + ",";
		shopTextStr += '<a>' + $(this).text() + "</a>";
	});
	
	$('#sids').val(shopIdStr);
	$("#choiceBoxShop").html(shopTextStr);
	$('#selectPopupShop').hide();
}

function validInput(id) {
	var _msg = '';
	switch(id) {
		case 'activity_id':
				if($('#' + id).val().length == 0) {
					_msg = '选择活动名称';	
				}
			break;
		case 'ticket_class':
				if($('#' + id).val().length == 0) {
					_msg = '请选择商品分类';	
				}
			break;
		case 'ticket_title':
				if($('#' + id).val().length == 0) {
					_msg = '请输入商品名称';	
				} else if($('#' + id).val().length > 30) {
					_msg = '商品名称最多30个字符，汉字算一个字符';
				}
			break;
		case 'p_value':
				if($('#' + id).val().length == 0) {
					_msg = '请输入商品原价';	
				} else if(!/^[1-9][0-9]*(\.[0-9]{1,2})?$/.test($('#' + id).val())) {
					_msg = '商品原价错误';
				}
			break;			
		case 's_value':
				if($('#' + id).val().length == 0) {
					_msg = '请输入商品现价';	
				} else if(!/^[1-9]?[0-9]*(\.[0-9]{1,2})?$/.test($('#' + id).val())) {
					_msg = '商品现价错误';
				} else if(Number($("#MaxNumber").val()) > 0 && Number($("#MinNumber").val()) > 0) {
					if(Number($('#' + id).val()) < Number($("#MinNumber").val()) || Number($('#' + id).val()) > Number($("#MaxNumber").val())) {
						_msg = '商品现价只能在：' + $("#MinNumber").val() + '-' + $("#MaxNumber").val() + '之间';
					}
				}
			break;
		case 'a_price':
				if(Number($('#' + id).val()) > 0 && !/^[1-9]?[0-9]*(\.[0-9]{1,2})?$/.test($('#' + id).val())) {
					_msg = 'APP售价错误';
				} else if(Number($("#MaxAppNumber").val()) > 0 && Number($("#MinAppNumber").val()) > 0) {
					if(Number($('#' + id).val()) < Number($("#MinAppNumber").val()) || Number($('#' + id).val()) > Number($("#MaxAppNumber").val())) {
						_msg = 'APP售价只能在：' + $("#MinAppNumber").val() + '-' + $("#MaxAppNumber").val() + '之间';
					}
				}
			break;	
		case 'total':
				if($('#' + id).val().length == 0) {
					_msg = '请输入商品数量';	
				} else if(!/[1-9][0-9]*$/.test($('#' + id).val())) {
					_msg = '商品数量为正整数';
				}			
			break;
		case 'sdate':
				if($('#' + id).val().length == 0) {
					_msg = '请输入销售有效期';	
				}
			break;
		case 'edate':
				if($('#' + id).val().length == 0) {
					_msg = '请输入销售有效期';	
				}
			break;
		case 'stime':
				if($('#' + id).val().length == 0) {
					_msg = '请输入使用有效期';	
				}
			break;
		case 'etime':
				if($('#' + id).val().length == 0) {
					_msg = '请输入使用有效期';	
				}
			break;
		case 'ticket_summary':
				if($('#' + id).val().length == 0) {
					_msg = '请输入商品简介';	
				} else if($('#' + id).val().length > 120) {
					_msg = '商品简介最多120个字符，汉字算一个字符';
				}
			break;
		case 'wap_content':
				if($('#' + id).val().length == 0) {
					_msg = '请输入商品详情（APP）';	
				}
			break;
		case 'file_img_large':
				if($("#tid").val().length == 0) {
					if($('#' + id).val().length == 0) {
						_msg = '上传商品大图';	
					} else {
						var file = $('#' + id).val();
						if(!/.(gif|jpg|jpeg|png|gif|jpg|png)$/i.test(file)){
							_msg = '图片类型必须是.gif,jpeg,jpg,png中的一种';
						}					
					}
				}
			break;	
		case 'file_img_small':
				if($("#tid").val().length == 0) {
					if($('#' + id).val().length == 0) {
						_msg = '上传商品小图';	
					} else {
						var file = $('#' + id).val();
						if(!/.(gif|jpg|jpeg|png|gif|jpg|png)$/i.test(file)){
							_msg = '图片类型必须是.gif,jpeg,jpg,png中的一种';
						}					
					}
				}
			break;	
		case 'postTextarea':
			if(editor.html().length == 0) {
				_msg = '请输入商品使用说明';	
			}
			break;
	}
	if(_msg == '') {
		$('#' + id).closest("td").children("label").attr('class', 'field_notice').html('');
	} else {
		$('#' + id).closest("td").children("label").attr('class', 'error').html(_msg);
		return false;
	}
	return true;	
}

var Browser = new Object();
Browser.isIE = window.ActiveXObject ? true : false;

function addImg(obj)
{
    var _html = $("#gallery-table tr:first").html();
    _html = "<tr>" + _html.replace(/(.*)(addImg)(.*)(\[)(\+)/i, "$1removeImg$3$4-") + "</tr>";
    $("#gallery-table tbody").append(_html);

}

function removeImg(obj)
{
    var row = rowindex(obj.parentNode.parentNode);
    var tbl = document.getElementById('gallery-table');

    tbl.deleteRow(row);
}

function rowindex(tr)
{
    if (Browser.isIE)
    {
        return tr.rowIndex;
    }
    else
    {
        table = tr.parentNode.parentNode;
        for (i = 0; i < table.rows.length; i ++ )
        {
            if (table.rows[i] == tr)
            {
                return i;
            }
        }
    }
}

//单项删除确认框
function delConfirm(id,title,message){
        message = message?message:'你确定要删除这条数据吗？';
        $.dialog({
            title: title,
            okValue:'确认',
            cancelValue:'取消',
            width: 230,
            height: 100,
            fixed: true,
            content: message,
            ok: function () {
                $.ajax({
                    type:'GET',
                    url:'/admin/commodity/wap-img-del',
                    dataType:'json',
                    data:'id=' + id,
                    success : function(data) {
                        if(data.status == 'ok') {
                            $("#imgCol_" + id).remove();
                        }
                    }
                })
            },
            cancel: function () {
                return true;
            }
        });
} 
</script>
{{include file='admin/footer.php'}}