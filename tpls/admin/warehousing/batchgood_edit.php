{{include file='admin/header.php'}}
<style type="text/css">
table.sku{ 
    border-bottom:1px solid grey; 
	border-right:1px solid grey; 
    border-top-width:0px; 
} 
table.sku tr td,table.sku tr th{ 
    border-top:1px solid grey; 
	border-left:1px solid grey;
	padding:5px 0px;
	text-align:center;
} 
</style>
<div id="rightTop">
  <p>仓储管理</p>
  <ul class="subnav">
    <li>
    	{{if $type eq 2}}
    	<a class="btn1" href="/admin/marketgood/list/page:{{$page}}">卖场商品</a>
    	{{else}}
    	<a class="btn1" href="/admin/batchgood/list/page:{{$page}}">后仓商品</a>
    	{{/if}}
    </li>
    <li><span>编辑商品</span></li>
  </ul>
</div>
<div class="info">
<form method="POST" action="{{$_CONF.FORM_ACTION}}" id="form" enctype="multipart/form-data">
<input type="hidden" name="sid" id="sid" value="{{$sid}}" />
<input type="hidden" name="user_name" id="user_name" value="{{$uname}}" />
<input type="hidden" name="tid" id="tid" value="{{$row.ticket_id}}" />
<input type="hidden" name="type" id="type" value="{{$type}}" />
<input type="hidden" name="page" id="page" value="{{$page}}" />
<input type="hidden" name="dataStr" id="dataStr" value="" />
<input type="hidden" name="dataRetStr" id="dataRetStr" value="" />
<input type="hidden" name="dataSkuStr" id="dataSkuStr" value='{{$row.SkuStr}}' />
<div id="tabs">
	<ul>
		<li><a href="#tabs-1">商品详情</a></li>
		<li><a href="#tabs-2">商品图片</a></li>
	</ul>
	<div id="tabs-1">
        <table class="infoTable">
            <tr>
                <th class="paddingT15">所属店铺:</th>
                <td class="paddingT15 wordSpacing5">
                 {{$row.shop_name}}
                </td>
            </tr>  
            <tr>
                <th class="paddingT15">商品名称:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableFile" type="text" name="ticket_title" id="ticket_title" value="{{$row.ticket_title}}" />
                  <label class="field_notice">100字以内</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">商品卖点:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableFile" type="text" name="selling_points" id="selling_points" value="{{$row.selling_points}}" />
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">商品分类:</th>
                <td class="paddingT15 wordSpacing5">
                    <select name="ticket_sort" id="ticket_sort" class="querySelect" autocomplete="off">
                        <option value="">请选择商品分类</option>    	
                        {{foreach from=$storeArray key=key item=item}}
                        <option value="{{$key}}" {{if $row.ticket_sort eq $key}}selected="selected"{{/if}}>{{$item}}</option>
                        {{/foreach}}
                    </select>
                    <label class="field_notice"></label>
                </td>    	
            </tr>
            <tr>
                <th class="paddingT15">商品小分类:</th>
                <td class="paddingT15 wordSpacing5">
                    <input type="radio" name="ticket_class" value="1" {{if $row.ticket_class|_isset && $row.ticket_class eq 1}}checked="checked"{{/if}} /> 店铺商品
                    <input type="radio" name="ticket_class" value="2" {{if $row.ticket_class|_isset && $row.ticket_class eq 2}}checked="checked"{{/if}} /> 特卖商品
                    <label class="field_notice"></label>
                </td>    	
            </tr>  
            <tr>
                <th class="paddingT15">限购:</th>
                <td class="paddingT15 wordSpacing5">
                  <input type="checkbox" class="querySelect" name="user_name_limit" id="user_name_limit" {{if $row.batch.user_name_limit eq 1}} checked="checked" {{/if}} value="1" />每用户
                  <input type="checkbox" class="querySelect" name="mobile_limit" id="mobile_limit" {{if $row.batch.mobile_limit eq 1}} checked="checked" {{/if}} value="1" />每手机
                  <input class="infoTableInput2" type="text" name="limit_count" id="limit_count" value="{{$row.limit_count}}" /> 件 / 
                  <select name="unit" id="unit">
                        <option value="Activity" {{if $row.batch.limit_unit eq 'Activity'}} selected="selected" {{/if}}>场</option>
                        <option value="Hour" {{if $row.batch.limit_unit eq 'Hour'}} selected="selected" {{/if}} >小时</option>
                        <option value="Day" {{if $row.batch.limit_unit eq 'Day'}} selected="selected" {{/if}} >天</option>
                        <option value="Week" {{if $row.batch.limit_unit eq 'Week'}} selected="selected" {{/if}} >周</option>
                        <option value="Weekly" {{if $row.batch.limit_unit eq 'Weekly'}} selected="selected" {{/if}} >自然周</option>
                        <option value="Month" {{if $row.batch.limit_unit eq 'Month'}} selected="selected" {{/if}}>月</option>
                        <option value="Monthly" {{if $row.batch.limit_unit eq 'Monthly'}} selected="selected" {{/if}}>自然月</option>
                        <option value="Minutes" {{if $row.batch.limit_unit eq 'Minutes'}} selected="selected" {{/if}}>分钟</option>
                    </select>  
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">销售有效日期:</th>
                <td class="paddingT15 wordSpacing5">
                	<input class="infoTableFile2" style="width:140px;" type="text" name="start_time" id="sdate" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" value="{{if $row.start_time}}{{$row.start_time|date_format:'%Y-%m-%d %H:%M:%S'}}{{/if}}" /> - 
 					<input class="infoTableFile2" style="width:140px;" type="text" name="end_time" id="edate" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'#F{$dp.$D(\'sdate\')}'})" value="{{if $row.end_time}}{{$row.end_time|date_format:'%Y-%m-%d %H:%M:%S'}}{{/if}}"/>
                  	<label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">原价:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="p_value" id="p_value" value="{{$row.par_value}}" />
                  <label class="field_notice">吊牌价</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">现价:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="s_value" id="s_value" value="{{$row.selling_price}}" />
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">APP售价:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="a_price" id="a_price" value="{{if $row.app_price gt 0}}{{$row.app_price}}{{/if}}" />
                  <input type="checkbox" name="is_free" value="1" {{if $row.is_free eq 1}}checked="checked"{{/if}} />APP端免单
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">货号:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableFile" type="text" name="good_number" id="good_number" value="{{$row.batch.good_number}}" />
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">条形码:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableFile" type="text" name="good_barcode" id="good_barcode" value="{{$row.batch.good_barcode}}" />
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">质地:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableFile" type="text" name="good_texture" id="good_texture" value="{{$row.batch.good_texture}}" />
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">适合人群:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableFile" type="text" name="good_match_crowd" id="good_match_crowd" value="{{$row.batch.good_match_crowd}}" />
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">年份:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableFile" type="text" name="good_years" id="good_years" value="{{$row.batch.good_years}}" />
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">季节:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableFile" type="text" name="good_season" id="good_season" value="{{$row.batch.good_season}}" />
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">总数量:</th>
                <td class="paddingT15 wordSpacing5">
                  <span>{{$row.total}}</span>
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">SKU信息:</th>
                <td class="paddingT15 wordSpacing5">
                  <table class="sku" cellspacing="0" cellpadding="0" width="600" style="text-align:center;">
                  	<tr>
                  		<th>颜色</th>
                  		<th>尺码</th>
                  		<th>仓库数量</th>
                  		<th>回退厂家数量</th>
                  		<th>入场数量</th>
                  		<th>已售数量</th>
                  	</tr>
                  	{{foreach from=$row.sku key=skuKey item=skuArr}}
                  		<tr>
                  		<td rowspan="{{$skuArr|@count}}">{{$skuKey}}</td>
                  		{{foreach from=$skuArr key=sKey item=sItem}}
                  		<td>{{$sItem.size}}</td>
                  		<td>{{$sItem.good_warehouse_num}}</td>
                  		<td>{{$sItem.good_rollback_num}}</td>
                  		<td>{{$sItem.good_market_num}}</td>
                  		<td>{{$sItem.good_sold_num}}</td>
                  		</tr>
                  		{{/foreach}}
                  	{{/foreach}}
                  </table>
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">商品简介:</th>
                <td class="paddingT15 wordSpacing5">
                  <textarea id="ticket_summary" name="ticket_summary">{{$row.ticket_summary}}</textarea>
                  <label class="field_notice">120个字符之内(汉字算一个字符)</label>
                </td>
            </tr>            
            <tr>
                <th class="paddingT15">商品详情:</th>
                <td class="paddingT15 wordSpacing5">
                  <textarea id="wap_content" name="wap_content" style="width:600px; height:300px;">{{$row.wap_content}}</textarea>
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">是否包邮:</th>
                <td class="paddingT15 wordSpacing5" >
                    <input type="radio" name="free_shipping" value="0" {{if $row.free_shipping|_isset && $row.free_shipping eq 0}}checked="checked"{{/if}} />否
                    <input type="radio" name="free_shipping" value="1" {{if $row.free_shipping|_isset && $row.free_shipping eq 1}}checked="checked"{{/if}} />是
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
            	<th></th>
            	<td></td>
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
              <td class="paddingT15 wordSpacing5"><img src="{{$_CONF.IMG_URL}}/buy/ticketwap/{{$item.img_url}}" class="makesmall" max_width="400" /></td>
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
<link rel="stylesheet" type="text/css" href="/css/admin/popup.css" />
<link rel="stylesheet" type="text/css" href="/css/JqueryDialog/jquery-ui-1.9.2.custom.min.css" />
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" charset="utf-8" ></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" charset="utf-8" ></script>
<script type="text/javascript" src="/js/DatePicker/WdatePicker.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/select.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/sku20141120.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/JqueryDialog/jquery-ui-1.9.2.custom.min.js" charset="utf-8" ></script>
<script type="text/javascript" src="/js/admin/inline_edit.js" charset="utf-8"></script>
<script type="text/javascript">
var validArray = ['ticket_sort', 'ticket_title', 'good_number', 'good_barcode', 'p_value', 's_value', 'ticket_summary'];
$(function(){	
	for(var i=0; i<validArray.length; i++) {
		$('#' + validArray[i]).bind('blur',function(){
			validInput($(this).attr("id"));
		});
	}
});

$(function() {
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

function validInput(id) {
	var _msg = '';
	var _input_type = 0;
	switch(id) {
		case 'ticket_sort':
				if($('#' + id).val().length == 0) {
					_msg = '请选择商品分类';	
				}
			break;
		case 'ticket_class':
				if( !$('input[name=' + id + ']:checked').val()) {
					_msg = '请选择商品小分类';	
					_input_type = 1;
				}
			break;
		case 'ticket_title':
				if($('#' + id).val().length == 0) {
					_msg = '请输入商品名称';	
				} else if($('#' + id).val().length > 100) {
					_msg = '商品名称最多100个字符，汉字算一个字符';
				}
			break;
		case 'p_value':
				if($('#' + id).val().length == 0) {
					_msg = '请输入商品原价';	
				} else if(!/^[1-9][0-9]*(\.[0-9]{1,2})?$/.test($('#' + id).val())) {
					_msg = '商品原价错误';
				}
			break;
		case 'good_number':
				if($('#' + id).val().length == 0) {
					_msg = '请输入商品货号';	
				}	
			break;	
		case 'good_barcode':
				if($('#' + id).val().length == 0) {
					_msg = '请输入商品条形码';	
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
		case 'good_warehouse_num':
				if($('#' + id).val().length == 0) {
					_msg = '请输入仓库商品数量';	
				} else if(!/[1-9][0-9]*$/.test($('#' + id).val())) {
					_msg = '仓库商品数量为正整数';
				}			
			break;
		case 'good_market_num':
				if($('#' + id).val().length == 0) {
					_msg = '请输入卖场商品数量';	
				} else if(!/[1-9][0-9]*$/.test($('#' + id).val())) {
					_msg = '卖场商品数量为正整数';
				}			
			break;
		case 'good_sold_num':
				if($('#' + id).val().length == 0) {
					_msg = '请输入已售商品数量';	
				} else if(!/[1-9][0-9]*$/.test($('#' + id).val())) {
					_msg = '已售商品数量为正整数';
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
	if(_input_type == 1) {
		if(_msg == '') {
			$('input[name=' + id + ']').closest("td").children("label").attr('class', 'field_notice').html('');
		} else {
			$('input[name=' + id + ']').closest("td").children("label").attr('class', 'error').html(_msg);
			return false;
		}		
	} else {
		if(_msg == '') {
			$('#' + id).closest("td").children("label").attr('class', 'field_notice').html('');
		} else {
			$('#' + id).closest("td").children("label").attr('class', 'error').html(_msg);
			return false;
		}
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