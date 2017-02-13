{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/autocomplete/autocomplete.css"  />
<link href="/css/JqueryDialog/jquery-ui-1.10.4.custom.css?2014062311" rel="stylesheet">
<script type="text/javascript" src="/js/JqueryDialog/jquery-ui-1.10.4.custom.js"></script>
<script type="text/javascript" src="/js/jquery.form.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/validate.min.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/autocomplete/autocomplete.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/DatePicker/WdatePicker.js"></script>
<script type="text/javascript">
$(function(){

    $('#form').validate({
        errorPlacement: function(error, element){
			if ( element.is(":radio") ) {
				$(element).nextUntil('label').hide();
				$(element).nextUntil('label').after(error);
			} else {
				$(element).next('.field_notice').hide();
				$(element).after(error);
			}
			error.removeClass('right');
        },
		success : function(label){
			label.addClass('right').text('OK!');
		},
		onkeyup : false, 
        rules : { 
			store_id : {
				required : true
			},
            shop_name : {
            	required : true,
				remote   : {
					url :'/admin/shop/check-shop-name',
					type:'post',
					data:{
						shop_name : function(){
							return $('#shop_name').val();
						},
						sid : '{{$row.shop_id}}'
					}
				}
            },
			region_id : {
				required : true
			},
			circle_id : {
				required : true
			},
			shop_address : {
				required : true,
				remote   : {
					url :'/admin/shop/check-shop-address',
					type:'post',
					data:{
						shop_address : function(){
							return $('#shop_address').val();
						}
					}
				}
			},
			brand_name : {
				required : true,
				remote   : {
					url :'/admin/shop/check-brand-name',
					type:'post',
					data:{
						brand_name : function(){
							return $('#brand_name').val();
						}
					}
				}				
			},
			business_hour : {
				required : true
			}
        },
        messages : {
			store_id : {
				required : '请选择店铺分类'
			},
            shop_name : {
            	required : '请输入店铺名称',
				remote : '店铺名称重复'
            },
			region_id : {
				required : '请选择所属的区'
			},
			circle_id : {
				required : '请选择所属商圈'
			},
			shop_address :  {
				required : '请输入详细地址',
				remote : '店铺地址错误，无法获取正确的经纬度'
			},
			brand_name : {
				required : '请输入名牌',
				remote : '该品牌不存在'
			},
			business_hour : {
				required : '请输入营业时间'
			}
        }
    });
	
	$('#region_id').change(function(){
		var _this = $('#circle_id');
		_this.empty();
		_this.append($("<option>").text('请选择商圈').val(''));
		$.post('/admin/good/get-circle', {id:$(this).val()}, function(obj){
			var data = eval('(' + obj + ')');
			$.each(data, function(i, s){
				_this.append($("<option>").text(s.name).val(s.id));
			});
		});
	});

	$('#circle_id').change(function(){
		var _this = $('#market_id');
		_this.empty();
		_this.append($("<option>").text('请选择商场').val(''));
		$.post('/admin/shop/get-market', {region_id:$('#region_id').val(), circle_id:$('#circle_id').val()}, function(obj){
			var data = eval('(' + obj + ')');
			$.each(data, function(i, s){
				_this.append($("<option>").text(s.name).val(s.id));
			});
		});
	});
	
	
	$("#brand_name").autocomplete(
	{ url:"/admin/shop/get-brand?t=" + new Date().getTime(), 
		onItemSelect:function (item) {
            var text = item.value; //文本
            var num = item.data; //数字
            $('#brand_name').val(text);
        }
        ,cellSeparator:"|"
	});


		
	{{if $row.shop_id}}
		$('#store_id').val({{$row.store_id}});
		$('#region_id').val({{$row.region_id}});
		$('#circle_id').val({{$row.circle_id}});
		$('#market_id').val({{$row.market_id}});
	{{/if}}


	$('.flag').change(function(){
		var rs = $(this).val();
		if (rs == 1){
			$('#res, #isShow').show();
		} else {
			$('#res, #isShow').hide();
		}
	})
	
	
	$("#dialog-logo").click(function() {
    	dialog('dialog','logo');
    });
	
	$("input[name=is_flag]").each(function(index, element) {
        if(this.value == 1 && this.checked) {
			$('#res, #isShow').show();
		}
    });
		
});

function checkSubmit()
{	
	if($("#form").valid())
	{
		$("#form").submit();
	}
}


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
						url:'/admin/shop/upload',
						success:function(data){
							data = eval('(' + data + ')');
							if(data.msg == 100){
								$('#'+dialog_name+'Html').attr("src",data.url);
								$('#'+dialog_name+'TD').show();
								$('#'+dialog_name+'Href').attr("href",data.url);
								$('#'+dialog_name+'Img').val(data.img_url);
								$('#'+dialog_name+'Error').html('');
							}else if (data.msg == 101){
								$('#'+dialog_name+'Error').html('请选择上传图片');
							}else if (data.msg == 102){
								$('#'+dialog_name+'Error').html('选择的图片尺寸不对');
							}else if (data.msg == 103){
								$('#'+dialog_name+'Error').html('选择的图片格式不对或者太大了');
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

<style>
    #dialog-logo {
        padding: .4em 1em .4em 20px;
        text-decoration: none;
        position: relative;
    }
    #dialog-logo span.ui-icon {
        margin: 0 5px 0 0;
        position: absolute;
        left: .2em;
        top: 50%;
        margin-top: -8px;
    }
	
    #logo {    font-size: 11px;
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
  <p>店铺管理</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/shop/list/page:{{$page}}">店铺列表</a></li>
    <li><span>{{if $row.shop_id}}编辑{{else}}新建{{/if}}店铺</span></li>
  </ul>
</div>
<div class="info">
<form method="POST" action="{{$_CONF.FORM_ACTION}}" id="form">
<input type="hidden" name="page" id="page" value="{{$page}}" />
<input type="hidden" name="sid" id="sid" value="{{$row.shop_id}}" />
<table class="infoTable">
	  <tr>
        <th class="paddingT15">店铺分类:</th>
        <td class="paddingT15 wordSpacing5">
            <select name="store_id" id="store_id">
            	<option value="">请选择分类</option>
                {{foreach from=$storeArray key=key item=item}}
                <option value="{{$key}}">{{$item}}</option>
                {{/foreach}}
            </select>	
            <label class="field_notice"></label>
        </td>
      </tr>
      <tr>
      	<th class="paddingT15">自动返利:</th>
        <td class="paddingT15 wordSpacing5">
            <input type="checkbox" name="is_selfpay" value="1"  {{if $row.is_selfpay eq 1}}checked="checked"{{/if}}/> APP自动返利[TO店长]
        </td>
      </tr>
<!--      {{if $packArray}}
      <tr>
      	<th class="paddingT15">套餐设置:</th>
        <td class="paddingT15 wordSpacing5">
            {{foreach from=$packArray key=key item=item}}
            <input type="radio" name="pack_id" value="{{$item.pack_id}}" 
            {{if $row.pack_id && $item.pack_id eq $row.pack_id}} checked="checked"
            {{elseif $item.pack_logo eq 'basic'}} checked="checked" {{/if}} /> {{$item.pack_name}}
            {{/foreach}}
            <label class="field_notice"></label>        
        </td>
      </tr>
      {{/if}}
      <tr>
      	<th class="paddingT15">套餐开始时间:</th>
        <td class="paddingT15 wordSpacing5">
            <input type="text" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" name="pack_stime" id="pack_stime" value="{{if $row.pack_stime}}{{$row.pack_stime|date_format:'%Y-%m-%d %H:%M:%S'}}{{/if}}" />
            <label class="field_notice"></label>        
        </td>
      </tr>
      <tr>
      	<th class="paddingT15">套餐结束时间:</th>
        <td class="paddingT15 wordSpacing5">
            <input type="text" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'#F{$dp.$D(\'pack_stime\',{y:1})}'})" name="pack_etime" id="pack_etime" value="{{if $row.pack_etime}}{{$row.pack_etime|date_format:'%Y-%m-%d %H:%M:%S'}}{{/if}}" />
            <label class="field_notice"></label>        
        </td>
      </tr>      --> 
      <tr>
        <th class="paddingT15">店铺名称:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" type="text" name="shop_name" id="shop_name" value="{{$row.shop_name}}" style="width:400px;" />
          <label class="field_notice"></label>
        </td>
      </tr>
      <tr>
        <th class="paddingT15">所属行政区:</th>
        <td class="paddingT15 wordSpacing5">
			<select name="region_id" id="region_id">
            	<option value="">请选择所在区</option>
                {{foreach from=$regionArray key=key item=item}}
                <option value="{{$key}}">{{$item}}</option>
                {{/foreach}}
            </select>	
            <label class="field_notice"></label>
        </td>
      </tr> 
      <tr>
        <th class="paddingT15">所属商圈:</th>
        <td class="paddingT15 wordSpacing5">
            <select name="circle_id" id="circle_id">
            	<option value="">请选择商圈</option>
                {{foreach from=$circleArray key=key item=item}}
                <option value="{{$item.id}}">{{$item.name}}</option>
                {{/foreach}}
            </select>	
            <label class="field_notice"></label>
        </td>
      </tr>
      <tr>
        <th class="paddingT15">所属商场:</th>
        <td class="paddingT15 wordSpacing5">
            <select name="market_id" id="market_id">
            	<option value="">请选择商场</option>
                {{foreach from=$marketArray key=key item=item}}
                <option value="{{$item.id}}">{{$item.name}}</option>
                {{/foreach}}
            </select>	
            <label class="field_notice"></label>
        </td>
      </tr>  
  	  <tr>
        <th class="paddingT15">详细地址:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" type="text" name="shop_address" id="shop_address" value="{{$row.shop_address}}" style="width:600px;" />
          <label class="field_notice"></label>
        </td>
      </tr>
      <tr>
        <th class="paddingT15">所属品牌:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" type="text" name="brand_name" id="brand_name" value="{{$row.brand_name}}" style="width:200px;" />
          <label class="field_notice" id="error_brand_name"></label>
        </td>
      </tr>
      <tr>
        <th class="paddingT15">联系电话:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" type="text" name="phone" id="phone" value="{{$row.phone}}" style="width:200px;" />
          <label class="field_notice" id="error_brand_name"></label>
        </td>
      </tr>
      <tr>
        <th class="paddingT15">营业时间:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" type="text" name="business_hour" id="business_hour" value="{{$row.business_hour}}" style="width:200px;" />
          <label class="field_notice" id="error_brand_name"></label>
        </td>
      </tr>
<!--      {{if $row.shop_id}}
	  <tr>
        <th class="paddingT15">店长分成:</th>
        <td class="paddingT15 wordSpacing5">
          <input type="radio" name="is_divided"  value="0" {{if $row.is_divided eq 0}}checked="checked"{{/if}}/> 不分
          <input type="radio" name="is_divided"  value="1" {{if $row.is_divided eq 1}}checked="checked"{{/if}}/> 分
          <span id="is_divided_ratio" {{if $row.is_divided eq 0}}style="display:none"{{/if}}>
          <input class="infoTableFile2" type="text" name="divided_ratio" id="divided_ratio" value="{{if $row.divided_ratio}}{{$row.divided_ratio}}{{else}}20{{/if}}" placeholder="分成比率"  />
          店主选择：
          <select name="divided_user_id" id="divided_user_id"></select>
          </span>
        </td>
      </tr>
      {{/if}}-->
      <tr>
        <th class="paddingT15">开通旗舰店:</th>
        <td class="paddingT15 wordSpacing5">
          <input type="radio" name="is_flag" class="flag" value="0" {{if $row.is_flag eq 0}}checked="checked"{{/if}}/> 否
          <input type="radio" name="is_flag" class="flag" value="1" {{if $row.is_flag eq 1}}checked="checked"{{/if}}/> 是
        </td>
      </tr>
      <tr id="res" {{if $row.is_flag neq 1}} style="display:none;" {{/if}}>
      	<th class="paddingT15"> 店铺头图:</th>
        <td class="paddingT15 wordSpacing5">
            <input name="logoImg" id="logoImg" type="text" size="35" value="{{$row.shop_img}}"/>
            <span id="logoError" style="color: red;font-size: 12px;">店铺头图 {{$shopheadsize.width}} * {{$shopheadsize.height}}</span>
        </td>
        <td><p><a href="#" id="dialog-logo" class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-newwin"></span>点击上传店铺头图</a></p></td>
     </tr>
     
    <tr id="isShow" {{if $row.is_flag neq 1}} style="display:none;" {{/if}}>
      <th class="paddingT15"></th>
        <td id="logoTD">    
        {{if $row.shop_img}}
        <a href="{{$_CONF.IMG_URL}}/buy/shop/{{$row.shop_img}}" target="_BLANK" id="logHref"><img src="{{$_CONF.IMG_URL}}/buy/shop/{{$row.shop_img}}" id="logHtml"></a>
        {{/if}}
        </td>
    </tr>
     
     
      <tr>
        <th class="paddingT15"> </th>
        <td class="ptb20">
            <input type="button" class="formbtn1" value="确认递交" onClick="checkSubmit()" />
        </td>
      </tr>
</table>
</form>

   <div id="dialog" title="上传店铺头图" style="display: none">
        <form id ="logo" method ="post" enctype ="multipart/form-data" >
            <fieldset>
                <label for="logo">店铺头图</label>
                <input type="file" name="uploadlogo" id="uploadlogo" class="text ui-widget-content ui-corner-all">
                <input type="hidden" name="type" id="type" value="uploadlogo">
                <input type="hidden" name="width"  id="width"  value="{{$shopheadsize.width}}">
                <input type="hidden" name="height" id="height" value="{{$shopheadsize.height}}">
            </fieldset>
        </form>
    </div>
</div>
<script type="text/javascript">
	$(function(){
		{{if $row.is_divided eq 1}}
			ajaxDivided({{$row.divided_user_id}});
		{{/if}}		
	});

	
	$("input[name=is_divided]").click(function(e) {
        if(this.value == 1) {
			ajaxDivided();		
		} else {
			$("#is_divided_ratio").hide();
		}
    });
	
	function ajaxDivided(id) {
		$.ajax({
			url:"/admin/shop/is-owner",
			dataType:"json",
			data:{sid:$("#sid").val()},
			success: function(obj){
				if(obj.res == 100) {
					$("#is_divided_ratio").show();
					$("#divided_user_id").empty();
					$("#divided_user_id").append('<option value="">请选择</option>');
					$.each(obj.extra,function(k, v){
						$("#divided_user_id").append('<option value ="'+ v.user_id+'">'+ v.user_name+'</option>');
					});
					if(!!id && id > 0 ) {
						$("#divided_user_id").val(id);
					}
				} else {
					$("input[name=is_divided]").each(function(index, element) {
						if(this.value == 0) {
							this.checked = true;
						} else {
							this.checked = false;
						}
					});
					alert('本店无主');
				}
			},
			error:function(){
			}
		});	
	}
</script>
{{include file='admin/footer.php'}}