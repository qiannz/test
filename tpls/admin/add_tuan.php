{{include file='admin/header.php'}}
<div id="rightTop">
  <p>券管理</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/buygood/list/page:{{$page}}">团购商品</a></li>
    {{if !$row.ticket_id}}<li><a class="btn3" href="/admin/buygood/user-shop/uname:{{$uname}}">店铺选择列表</a></li>{{/if}}
    <li><span>{{if $row.ticket_id}}编辑团购{{else}}新建团购{{/if}}</span></li>
  </ul>
</div>
<div class="info">
<form method="POST" action="{{$_CONF.FORM_ACTION}}" id="form" enctype="multipart/form-data">
<input type="hidden" name="sid" id="sid" value="{{$sid}}" />
<input type="hidden" name="user_name" id="user_name" value="{{$uname}}" />
<input type="hidden" name="tid" id="tid" value="{{$row.ticket_id}}" />
<input type="hidden" name="page" id="page" value="{{$page}}" />
<input type="hidden" name="sids" id="sids" value="" />
<input type="hidden" name="dataStr" id="dataStr" value="" />
<input type="hidden" name="dataRetStr" id="dataRetStr" value="" />
<input type="hidden" name="dataSkuStr" id="dataSkuStr" value='{{$row.SkuStr}}' />
<input type="hidden" id="MaxNumber" value="" />
<input type="hidden" id="MinNumber" value="" />
<input type="hidden" id="MaxAppNumber" value="" />
<input type="hidden" id="MinAppNumber" value="" />
<div id="tabs">
	<ul>
		<li><a href="#tabs-1">WEB</a></li>
		<li><a href="#tabs-2">WAP</a></li>
	</ul>
	<div id="tabs-1">
        <table class="infoTable">
            <tr>
                <th class="paddingT15">所属店铺:</th>
                <td class="paddingT15 wordSpacing5">
                 {{$row.region_name}} {{$row.circle_name}} {{$row.shop_name}}
                </td>
            </tr>
            <tr>
                <th class="paddingT15">活动名称:</th>
                <td class="paddingT15 wordSpacing5">
                <select name="activity_id" id="activity_id" style="width:300px;">
                        <option value="">请选择活动</option>
                        {{foreach from=$activity key=key item=item}}
                        <option value="{{$item.activity_id}}" {{if $row.activity_id eq $item.activity_id}} selected="selected" {{/if}}>{{$item.activity_name}}</option>
                        {{/foreach}}
                    </select>  
                  <a href="javascript:add_a();" >创建活动</a>
                  <input type="button" class="formbtn2" value="刷新活动" id="refresh_act" />	
                  <label class="field_notice"></label>
                </td>
            </tr>    
            <tr>
                <th class="paddingT15">关联店铺:</th>
                <td class="paddingT15 wordSpacing5">
                    <a href="javascript:associate()">点击选择关联店铺</a>
                </td>
            </tr>
            <tr>
                <th class="paddingT15"></th>
                <td class="paddingT15 wordSpacing5">
                <span id="choiceBoxShop" class="choiceBox">
                {{foreach from=$ticketRelationShopArray key=key item=item}}
                <a>{{$item.shop_name}}</a>
                {{/foreach}}
                </span>
                </td>
            </tr>  
            <tr>
                <th class="paddingT15">商品名称:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableFile" type="text" name="ticket_title" id="ticket_title" value="{{$row.ticket_title}}" />
                  <label class="field_notice">30字以内</label>
                </td>
            </tr>    
            <tr>
                <th class="paddingT15">商品分类:</th>
                <td class="paddingT15 wordSpacing5">
                    <select name="ticket_sort" id="ticket_sort" class="querySelect">
                        <option value="">请选择商品分类</option>    	
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
                  <input class="infoTableFile2" style="width:140px;" type="text" name="edate" id="edate" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm',minDate:'#F{$dp.$D(\'sdate\',{d:1})}'})" value="{{if $row.end_time}}{{$row.end_time|date_format:'%Y-%m-%d %H:%M'}}{{/if}}"/>
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
                <th class="paddingT15">SKU商品分类:</th>
                <td class="paddingT15 wordSpacing5">
                    <div class="inputBox" style="position:relative; z-index:995">
                                <span class="change-shop">{{if $row.category_name}}{{$row.category_name}}{{else}}----选择----{{/if}}</span><span class="change-shop-pic"><s class="change-shop-pic-s"></s></span>
                                
                              <div class="allList" id="allBox">
                                    <div class="listBox">
                                            <ul>
                                                {{foreach from=$skuArray key=key item=item}}
                                                <li class="listBox-col">
                                                    <a data-cid="{{$item.CategoryId}}">{{$item.CategoryName}}</a>
                                                    {{if $item.child}}
                                                    <div class="shopBox">
                                                        <ul>
                                                            {{foreach from=$item.child key=ckey item=citem}}
                                                            <li><a data-cid="{{$citem.CategoryId}}">{{$citem.CategoryName}}</a></li>
                                                            {{/foreach}}
                                                        </ul>
                                                    </div>
                                                    {{/if}}
                                                </li>
                                                {{/foreach}}
                                            </ul>
                                    </div>
                               </div>
                            </div>
                </td>    	
            </tr> 
            <tr>
                <th class="paddingT15"></th>
                <td class="paddingT15 wordSpacing5">
                <div class="sku-dis">
                        {{foreach from=$skuListArray key=key item=item}}
                        <div class="inputBox">
                            <label class="sku-label" data-cid="{{$item.CategoryId}}" data-pid="{{$item.PropId}}">{{$item.PropName}}</label>
                            <div class="color-list">
                                <ul>
                                    {{foreach from=$item.Values key=skey item=sitem}}
                                    <li><input type="checkbox" class="color-checkbox" value="{{$sitem.ValueId}}" initvalue="{{$sitem.ValueName}}" data-alias="{{$item.AllowAlias}}">
                                    <span class="color-val">{{$sitem.ValueName}}</span><input type="text" class="color-input" /></li>
                                    {{/foreach}}
                                </ul>
                            </div>
                        </div>
                        {{/foreach}}
                </div>
                <div class="inputBox" id="ske-table-box">
                    <label class="label">SKU设置：</label>
                    <div class="right-list"></div>
                </div>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">数量:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="total" id="total" value="{{$row.total}}" readonly="readonly" />
                  <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">原价:</th>
                <td class="paddingT15 wordSpacing5">
                  <input class="infoTableInput2" type="text" name="p_value" id="p_value" value="{{$row.par_value}}" />
                  <label class="field_notice"></label>
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
                  <input class="infoTableInput2" type="text" name="a_price" id="a_price" value="{{if $row.app_price lt 0}}{{$row.app_price|floor}}{{else}}{{$row.app_price}}{{/if}}" />
                  <label class="field_notice">售价 值为 -1 时，APP端免单</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">商品简介:</th>
                <td class="paddingT15 wordSpacing5">
                  <textarea id="ticket_summary" name="ticket_summary">{{$row.ticket_summary}}</textarea>
                  <label class="field_notice">70字内(汉字算一个字符)</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">商品大图:</th>
                <td class="paddingT15 wordSpacing5">
                  <input type="file" class="file" name="file_img_large" id="file_img_large" />
                  <label class="field_notice">图片尺寸 {{$recommendArray.buygood_img_large.width}} * {{$recommendArray.buygood_img_large.height}}</label>
                </td>
            </tr>
            {{if $row.file_img_large}}
            <tr>
                <th class="paddingT15"></th>
                <td class="paddingT15 wordSpacing5">
                  <img src="{{$_CONF.IMG_URL}}/buy/ticket/{{$row.file_img_large}}" />
                </td>
            </tr>
            {{/if}} 
            <tr>
                <th class="paddingT15">商品小图:</th>
                <td class="paddingT15 wordSpacing5">
                  <input type="file" class="file" name="file_img_small" id="file_img_small" />
                  <label class="field_notice">图片尺寸 {{$recommendArray.buygood_img_small.width}} *{{$recommendArray.buygood_img_small.width}}</label>
                </td>
            </tr>
            {{if $row.file_img_small}}
            <tr>
                <th class="paddingT15"></th>
                <td class="paddingT15 wordSpacing5">
                  <img src="{{$_CONF.IMG_URL}}/buy/ticket/{{$row.file_img_small}}" />
                </td>
            </tr>
            {{/if}}   
            <tr>
                <th class="paddingT15">上传图片</th>
                <td class="paddingT15 wordSpacing5">
                    <div class="list-box">
                        <div class="imgBtn">
                            上传图片
                            <input type="file" class="file" id="file">
                        </div>
                        <div id="loadBox"></div>
                        <span class="loadBar"><em style="width: 0%" id="loadBar"></em></span>
                        <span id="loadNum" class="exp"><em>等待上传</em></span>
                        <span class="exp">最多不超过10张，每张图片限1M</span>
                    </div>
                    <div class="imgBox" id="postImageList" style="display:none">
                        <ul class="clearfix"></ul>
                    </div>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">使用说明:</th>
                <td class="paddingT15 wordSpacing5">
                  <textarea id="content" name="content" style="width:800px; height:400px;">{{$row.content}}</textarea>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">WAP详情:</th>
                <td class="paddingT15 wordSpacing5">
                  <textarea id="wap_content" name="wap_content" style="width:600px; height:300px;">{{$row.wap_content}}</textarea>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">上下架:</th>
                <td class="paddingT15 wordSpacing5" >
                    <input type="radio" name="is_auth" value="1" {{if $row.is_auth|_isset && $row.is_auth eq 1}}checked="checked"{{else}}checked="checked"{{/if}} />上架
                    <input type="radio" name="is_auth" value="0" {{if $row.is_auth|_isset && $row.is_auth eq 0}}checked="checked"{{/if}} />下架
                    <label class="field_notice"></label>
                </td>
            </tr> 
            <tr>
                <th class="paddingT15">是否显示:</th>
                <td class="paddingT15 wordSpacing5" >
                    <input type="radio" name="is_show" value="0" {{if $row.is_show|_isset && $row.is_show eq 0}}checked="checked"{{else}}checked="checked"{{/if}} />否
                    <input type="radio" name="is_show" value="1" {{if $row.is_show|_isset && $row.is_show eq 1}}checked="checked"{{/if}} />是
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
<script type="text/javascript" src="/js/sku20141120.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/JqueryDialog/jquery-ui-1.9.2.custom.min.js" charset="utf-8" ></script>
<script type="text/javascript" src="/js/admin/inline_edit.js" charset="utf-8"></script>
<script type="text/javascript">
var postImage, editor, uploadSwf;
var validArray = ['activity_id', 'ticket_title', 'ticket_sort', 'p_value', 's_value','a_price', 'total', 'sdate', 'edate', 'stime', 'etime', 'ticket_summary', 'file_img_large', 'file_img_small', 'postTextarea'];
$(function(){	
	editor = KindEditor.create('#content',{items : ['source', '|', 'undo', 'redo', '|', 'justifyleft', 'justifycenter', 'justifyright','justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent','|', 'fullscreen', '/','fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold','italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'emoticons', 'link', 'unlink', 'baidumap']});
	postImage=new couponImage();
	uploadSwf = new SWFUpload({
        upload_url: "/admin/good/upload?folder=ticket",
        file_size_limit : "1024 KB",
        file_types : "*.jpg;*.gif;*.png",
        file_types_description : "All Files",
        file_upload_limit : "10",
        file_queue_limit : "10",
        file_post_name : "uploadFile",
		post_params: {"user_name" : "{{$uname}}"},
        file_dialog_start_handler : fileDialogStart,
        file_queued_handler : fileQueued,
        file_queue_error_handler : fileQueueError,
        file_dialog_complete_handler : fileDialogComplete,
        upload_start_handler : uploadStart,
        upload_progress_handler : uploadProgress,
        upload_error_handler : uploadError,
        upload_success_handler : uploadSuccess,
        upload_complete_handler : uploadComplete,

        // Button Settings
        button_image_url : "/images/upload.png",
        button_placeholder_id : "file",
        button_width: 137,
        button_height: 26,
        button_cursor: SWFUpload.CURSOR.HAND,
        button_window_mode : SWFUpload.WINDOW_MODE.TRANSPARENT,
        // Flash Settings
        flash_url : "/js/swfupload/swfupload.swf",
        // Debug Settings
        debug: false
    });
	
	for(var i=0; i<validArray.length; i++) {
		$('#' + validArray[i]).bind('blur',function(){
			validInput($(this).attr("id"));
		});
	}
	
	$('#refresh_act').click(function(){
		var _this = $('#activity_id');
		var user_name = $('#user_name').val();
		_this.empty();
		_this.append($("<option>").text('请选择活动').val(''));
		$.post('/admin/ticket/get-act', {user_name:user_name}, function(obj){
			var data = eval('(' + obj + ')');
			$.each(data, function(i, s){
				_this.append($("<option>").text(s.activity_name).val(s.activity_id));
			});
			alert('已刷新');
		});	
	});
	
	changeShop({
		btn:'.change-shop-pic',
		txt:'.allList',
		val:'.change-shop'
	});
	
	$('.shopBox a').click(function(){
		var cid = $(this).attr('data-cid');
		var _html = '';
		$(".sku-dis").html('加载中。。。');
		$(".right-list").html('');
		$.ajax({
		   type:"POST",
		   url:"/admin/buygood/get-sku-list",
		   data:{cid:cid},
		   dataType:"json",
		   success:function(data){
			   
			   var len = data.length;
			   for(var i=0; i < len; i++) {
					_html += '<div class="inputBox">';
					_html += '<label class="sku-label" data-cid="'+data[i]['CategoryId']+'" data-pid="'+data[i]['PropId']+'">'+data[i]['PropName']+'：</label>';
					_html += '<div class="color-list">';
					_html += '<ul>';
					var vlen = data[i].Values.length;
					for(var j=0; j < vlen; j++) {
						_html += '<li><input type="checkbox" class="color-checkbox" value="'+data[i]['Values'][j]['ValueId']+'" initvalue="'+data[i]['Values'][j]['ValueName']+'" data-alias="'+data[i]['AllowAlias']+'">';
						_html += '<span class="color-val">'+data[i]['Values'][j]['ValueName']+'</span><input type="text" class="color-input" /></li>';
					}
					_html += '</ul>';
					_html += '</div>';
					_html += '</div>';
			   }
			   if(_html != '') {
			   	  $(".sku-dis").html(_html);
				  sku();
			   } else {
				  $(".sku-dis").html('');
			   }
		   }
		});
	});
				   

	if($("#tid").val() > 0) {
		sku({{$row.SkuStr}});
	}
});

$(function() {
	$( "#tabs" ).tabs();
});

function add_a() {
	$.dialog({
		title: '添加活动',
		content: '活动名称：<br/><br/>'
				 + '<input type="text" name="aname" id="aname" /><br/>',
		ok: function () {
			var aname = $('#aname').val();
			var user_name = $('#user_name').val();
			if(aname == '') {
				alert('活动名称不能为空');
				return false;
			} else {
				var url = '/admin/ticket/add-activity';
				$.post(url, {aname:aname, user_name:user_name}, function(data){
					var obj = eval('(' + data + ')');
					if (obj.res == 1) {
						$.dialog({
							title : '结果',
							content : obj.msg, 
							okValue: '确定',
							ok: true
						});							
					}
				});
			}
		},
		cancel: true
	});
}

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
		$("#content").val(editor.html());
		getSkuToStr();
		$('.formbtn1').attr("value", "提交中...").attr("disabled", true);
		$("#form").submit();
	}
	
}

function associate() {
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

function getSkuToStr() {
	var dataStr = '';
	var data_cid;
	$("div .sku-dis .inputBox").each(function(index, element) {
		var data_pid;
        data_cid = $(this).children("label").attr('data-cid');
		data_pid = $(this).children("label").attr('data-pid');
		var skuStr = "";
		$.each($(this).find("li"), function(){
			var ckStr = "";
			if($(this).find(".color-checkbox").attr("checked") == "checked") {
				ckStr = $(this).find(".color-checkbox").val() + ',' + $(this).find(".color-val").html();
			}
			if(ckStr != "") {
				skuStr += data_pid + ';' + ckStr + '^^';
			}
		});
		
		if(skuStr != "") {
			skuStr = skuStr.substr(0, skuStr.length - 2);
			dataStr +=  skuStr + '&&';
		}
    });
	if(dataStr != "") {
		dataStr = data_cid + ',' + $("span.change-shop").html() + '&&' + dataStr.substr(0, dataStr.length - 2);
		$("#dataStr").val(dataStr);
	}
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
				} else if($('#' + id).val().length > 70) {
					_msg = '商品简介最多70个字符，汉字算一个字符';
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
                    url:'/admin/ticket/wap-img-del',
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