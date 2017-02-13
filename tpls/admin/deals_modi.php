{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/css/JqueryDialog/jquery-ui-1.10.4.custom.css?t={{$_CONF.WEB_VERSION}}">
<script charset="utf-8" type="text/javascript" src="/js/validate.min.js?t={{$_CONF.WEB_VERSION}}" ></script>
<script charset="utf-8" type="text/javascript" src="/js/JqueryDialog/jquery-ui-1.10.4.custom.js?t={{$_CONF.WEB_VERSION}}"></script>
<script charset="utf-8" type="text/javascript" src="/js/DatePicker/WdatePicker.js?t={{$_CONF.WEB_VERSION}}" ></script>
<script type="text/javascript" src="/js/jquery.form.js?t={{$_CONF.WEB_VERSION}}"></script>
<style>
    #dialog-head {
        padding: .4em 1em .4em 20px;
        text-decoration: none;
        position: relative;
    }
    #dialog-head span.ui-icon {
        margin: 0 5px 0 0;
        position: absolute;
        left: .2em;
        top: 50%;
        margin-top: -8px;
    }
    #m_head {
		font-size: 11px;
        height: 25px;
        left: 90px;
        top:19px;
        position: absolute;
        width: 250px;
        z-index: 2;
        line-height: 25px;;
    }
  
    input.text { margin:5px 0 12px; width:150px; padding: .4em; vertical-align: middle}
    fieldset { padding:0; border:0; margin-top:25px; }
    h1 { font-size: 1.2em; margin: .6em 0; }
    div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
    div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
</style>

<div id="rightTop">
  <p>特卖管理</p>
  <ul class="subnav">
    <li><a class="btn4" href="/admin/deals/list/page:{{$page}}">特卖列表</a></li>
    <li><span>特卖新增</span></li>
  </ul>
</div>
<div class="info">
<form method="POST" action="{{$_CONF.FORM_ACTION}}" id="deals_form">
<input type="hidden" name="deals_id" id="deals_id" value="{{$deals_id}}" />
<input type="hidden" name="page" id="page" value="{{$page}}" />
<table class="infoTable">
      <tbody>
      <tr>
        <th class="paddingT15"> 特卖名称:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" type="text" name="deals_name" id="deals_name" value="{{$row.deals_name}}" style="width:400px;" />
          <label class="field_notice"></label>
        </td>
      </tr>
      <tr>
        <th class="paddingT15"> 特卖ID:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" type="text" name="voucher_id" id="voucher_id" value="{{$row.voucher_id}}" style="width:280px;" />
          <label class="field_notice"></label>
        </td>
      </tr>
      <tr>
        <th class="paddingT15"> 折扣信息:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" type="text" name="discount" id="discount" value="{{$row.discount}}" style="width:150px;" />
          <label class="field_notice"></label>
        </td>
      </tr>
      <tr>
        <th class="paddingT15"> 链接地址:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" type="text" name="link" id="link" value="{{$row.link}}" style="width:600px;" />
          <label class="field_notice"></label>
        </td>
      </tr>
      <tr>
        <th class="paddingT15"> 是否有券:</th>
        <td class="paddingT15 wordSpacing5">
          <input type="checkbox" name="had_ticket" class="flag" value="1" {{if $row.had_ticket eq 1}}checked="checked"{{/if}}/>
          <label class="field_notice"></label>
        </td>
      </tr>
      <tr>
        <th class="paddingT15"> 开始时间:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableFile2" style="width:140px;" type="text" name="start_time" id="start_time" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm',minDate:'now()'})" value="{{if $row.start_time}}{{$row.start_time|date_format:'%Y-%m-%d %H:%I'}}{{/if}}" />
          <label class="field_notice"></label>
        </td>
      </tr>
      <tr>
        <th class="paddingT15"> 结束时间:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableFile2" style="width:140px;" type="text" name="end_time" id="end_time" onFocus="WdatePicker({isShowClear:true,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm',minDate:'#F{$dp.$D(\'start_time\',{d:1})}'})" value="{{if $row.end_time}}{{$row.end_time|date_format:'%Y-%m-%d %H:%I'}}{{/if}}"/>
          <label class="field_notice"></label>
        </td>
      </tr>
      <tr>
        <th class="paddingT15"> 特卖图片:</th>
        <td class="paddingT15 wordSpacing5">
          <input name="img" id="img" type="text" style="width:300px;" value="{{$row.img}}"/>
          <label class="field_notice">图片宽高 640 * 300</label>
          <label><a href="#" id="dialog-head" class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-newwin"></span>点击上传特卖图片</a></label>
        </td>
      </tr>
      <tr>
        <th class="paddingT15"></th>
        <td class="paddingT15 wordSpacing5" id="imgHtml">
        {{if $row.img}}
        <img src="{{$_CONF.IMG_URL}}/buy/deals/{{$row.img}}"></a>
        {{/if}}
        </td>
      </tr>      
      <tr>
        <th></th>
        <td class="ptb20"><input type="submit" value="提交" name="Submit" class="formbtn">
          <input type="reset" value="重置" name="Reset" class="formbtn">        
        </td>
      </tr>
    </tbody>
    </table>
</form>
</div>

    <!-- ui dialog-->    
    <div id="dialog" title="图片上传" style="display:none">
        <form id="m_head" method="post" enctype="multipart/form-data" >
            <fieldset>
                <label for="head">特卖图片</label>
                <input type="file" name="uploadImg" id="uploadImg" class="text ui-widget-content ui-corner-all">
            </fieldset>
        </form>
    </div>
    
<script type="text/javascript">
	
$(function(){
	
	$('#deals_form').validate({
		errorPlacement: function(error, element){
			$(element).next('.field_notice').hide();
			$(element).after(error);
		},
		success       : function(label){
			label.addClass('right').text('OK!');
		},
		rules : {
			deals_name : {
				required : true
			},
			voucher_id : {
				required : true
			},
			discount : {
				required : true
			},
			start_time : {
				required : true
			},
			end_time : {
				required : true
			},
			img : {
				required : true
			}
		},
		messages : {
			deals_name : {
				required : '请填写特卖名称！'
			},
			voucher_id : {
				required : '请填写特卖ID！'
			},
			discount : {
				required : '请填写折扣信息！'
			},
			start_time : {
				required : '请填写开始时间！'
			},
			end_time : {
				required : '请填写结束时间！'
			},
			img : {
				required : '请上传特卖图片！'
			}
		}
	});	
	//上传图片dialog 弹出层调用
	$( "#dialog-head" ).click(function() {
		dialog('dialog','m_head');
	});
});

function dialog(class_id,dialog_name){
	$('#'+class_id).dialog({
		autoOpen: true,
		width: 400,
		height:200,
		modal:true,
		buttons: [
			{
				text: "上传",
				click: function() {
					$('#'+dialog_name).ajaxSubmit({
						type:'post',
						url:'/admin/deals/upload',
						success:function(data){
							data = eval('(' + data + ')');
							if(data.msg == 100){
								$('#img').val(data.img_url);
								$('#imgHtml').html('<img src="' + data.url+'" />');
							}else if (data.msg == 101){
								alert('请选择上传图片');
							}else if (data.msg == 102){
								alert('选择的图片尺寸不对');
							}else if (data.msg == 103){
								alert('选择的图片格式不对或者太大了');
							}
						}
					})
					$( this ).dialog( "close" );
				}
			},
			{
				text: "取消",
				click: function() {
					$( this ).dialog( "close" );
				}
			}
		]
	});
}
</script>
{{include file='admin/footer.php'}}