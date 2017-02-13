<?php /* Smarty version 2.6.27, created on 2016-02-22 16:29:28
         compiled from admin/shop_modi.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'admin/shop_modi.php', 306, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<link rel="stylesheet" type="text/css" href="/js/autocomplete/autocomplete.css"  />
<link href="/css/JqueryDialog/jquery-ui-1.10.4.custom.css?2014062311" rel="stylesheet">
<script type="text/javascript" src="/js/JqueryDialog/jquery-ui-1.10.4.custom.js"></script>
<script type="text/javascript" src="/js/jquery.form.js?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
"></script>
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
						sid : '<?php echo $this->_tpl_vars['row']['shop_id']; ?>
'
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


		
	<?php if ($this->_tpl_vars['row']['shop_id']): ?>
		$('#store_id').val(<?php echo $this->_tpl_vars['row']['store_id']; ?>
);
		$('#region_id').val(<?php echo $this->_tpl_vars['row']['region_id']; ?>
);
		$('#circle_id').val(<?php echo $this->_tpl_vars['row']['circle_id']; ?>
);
		$('#market_id').val(<?php echo $this->_tpl_vars['row']['market_id']; ?>
);
	<?php endif; ?>


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
    <li><a class="btn1" href="/admin/shop/list/page:<?php echo $this->_tpl_vars['page']; ?>
">店铺列表</a></li>
    <li><span><?php if ($this->_tpl_vars['row']['shop_id']): ?>编辑<?php else: ?>新建<?php endif; ?>店铺</span></li>
  </ul>
</div>
<div class="info">
<form method="POST" action="<?php echo $this->_tpl_vars['_CONF']['FORM_ACTION']; ?>
" id="form">
<input type="hidden" name="page" id="page" value="<?php echo $this->_tpl_vars['page']; ?>
" />
<input type="hidden" name="sid" id="sid" value="<?php echo $this->_tpl_vars['row']['shop_id']; ?>
" />
<table class="infoTable">
	  <tr>
        <th class="paddingT15">店铺分类:</th>
        <td class="paddingT15 wordSpacing5">
            <select name="store_id" id="store_id">
            	<option value="">请选择分类</option>
                <?php $_from = $this->_tpl_vars['storeArray']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
                <option value="<?php echo $this->_tpl_vars['key']; ?>
"><?php echo $this->_tpl_vars['item']; ?>
</option>
                <?php endforeach; endif; unset($_from); ?>
            </select>	
            <label class="field_notice"></label>
        </td>
      </tr>
      <tr>
      	<th class="paddingT15">自动返利:</th>
        <td class="paddingT15 wordSpacing5">
            <input type="checkbox" name="is_selfpay" value="1"  <?php if ($this->_tpl_vars['row']['is_selfpay'] == 1): ?>checked="checked"<?php endif; ?>/> APP自动返利[TO店长]
        </td>
      </tr>
<!--      <?php if ($this->_tpl_vars['packArray']): ?>
      <tr>
      	<th class="paddingT15">套餐设置:</th>
        <td class="paddingT15 wordSpacing5">
            <?php $_from = $this->_tpl_vars['packArray']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
            <input type="radio" name="pack_id" value="<?php echo $this->_tpl_vars['item']['pack_id']; ?>
" 
            <?php if ($this->_tpl_vars['row']['pack_id'] && $this->_tpl_vars['item']['pack_id'] == $this->_tpl_vars['row']['pack_id']): ?> checked="checked"
            <?php elseif ($this->_tpl_vars['item']['pack_logo'] == 'basic'): ?> checked="checked" <?php endif; ?> /> <?php echo $this->_tpl_vars['item']['pack_name']; ?>

            <?php endforeach; endif; unset($_from); ?>
            <label class="field_notice"></label>        
        </td>
      </tr>
      <?php endif; ?>
      <tr>
      	<th class="paddingT15">套餐开始时间:</th>
        <td class="paddingT15 wordSpacing5">
            <input type="text" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" name="pack_stime" id="pack_stime" value="<?php if ($this->_tpl_vars['row']['pack_stime']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['row']['pack_stime'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M:%S') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M:%S')); ?>
<?php endif; ?>" />
            <label class="field_notice"></label>        
        </td>
      </tr>
      <tr>
      	<th class="paddingT15">套餐结束时间:</th>
        <td class="paddingT15 wordSpacing5">
            <input type="text" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'#F{$dp.$D(\'pack_stime\',{y:1})}'})" name="pack_etime" id="pack_etime" value="<?php if ($this->_tpl_vars['row']['pack_etime']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['row']['pack_etime'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M:%S') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M:%S')); ?>
<?php endif; ?>" />
            <label class="field_notice"></label>        
        </td>
      </tr>      --> 
      <tr>
        <th class="paddingT15">店铺名称:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" type="text" name="shop_name" id="shop_name" value="<?php echo $this->_tpl_vars['row']['shop_name']; ?>
" style="width:400px;" />
          <label class="field_notice"></label>
        </td>
      </tr>
      <tr>
        <th class="paddingT15">所属行政区:</th>
        <td class="paddingT15 wordSpacing5">
			<select name="region_id" id="region_id">
            	<option value="">请选择所在区</option>
                <?php $_from = $this->_tpl_vars['regionArray']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
                <option value="<?php echo $this->_tpl_vars['key']; ?>
"><?php echo $this->_tpl_vars['item']; ?>
</option>
                <?php endforeach; endif; unset($_from); ?>
            </select>	
            <label class="field_notice"></label>
        </td>
      </tr> 
      <tr>
        <th class="paddingT15">所属商圈:</th>
        <td class="paddingT15 wordSpacing5">
            <select name="circle_id" id="circle_id">
            	<option value="">请选择商圈</option>
                <?php $_from = $this->_tpl_vars['circleArray']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
                <option value="<?php echo $this->_tpl_vars['item']['id']; ?>
"><?php echo $this->_tpl_vars['item']['name']; ?>
</option>
                <?php endforeach; endif; unset($_from); ?>
            </select>	
            <label class="field_notice"></label>
        </td>
      </tr>
      <tr>
        <th class="paddingT15">所属商场:</th>
        <td class="paddingT15 wordSpacing5">
            <select name="market_id" id="market_id">
            	<option value="">请选择商场</option>
                <?php $_from = $this->_tpl_vars['marketArray']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
                <option value="<?php echo $this->_tpl_vars['item']['id']; ?>
"><?php echo $this->_tpl_vars['item']['name']; ?>
</option>
                <?php endforeach; endif; unset($_from); ?>
            </select>	
            <label class="field_notice"></label>
        </td>
      </tr>  
  	  <tr>
        <th class="paddingT15">详细地址:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" type="text" name="shop_address" id="shop_address" value="<?php echo $this->_tpl_vars['row']['shop_address']; ?>
" style="width:600px;" />
          <label class="field_notice"></label>
        </td>
      </tr>
      <tr>
        <th class="paddingT15">所属品牌:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" type="text" name="brand_name" id="brand_name" value="<?php echo $this->_tpl_vars['row']['brand_name']; ?>
" style="width:200px;" />
          <label class="field_notice" id="error_brand_name"></label>
        </td>
      </tr>
      <tr>
        <th class="paddingT15">联系电话:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" type="text" name="phone" id="phone" value="<?php echo $this->_tpl_vars['row']['phone']; ?>
" style="width:200px;" />
          <label class="field_notice" id="error_brand_name"></label>
        </td>
      </tr>
      <tr>
        <th class="paddingT15">营业时间:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" type="text" name="business_hour" id="business_hour" value="<?php echo $this->_tpl_vars['row']['business_hour']; ?>
" style="width:200px;" />
          <label class="field_notice" id="error_brand_name"></label>
        </td>
      </tr>
<!--      <?php if ($this->_tpl_vars['row']['shop_id']): ?>
	  <tr>
        <th class="paddingT15">店长分成:</th>
        <td class="paddingT15 wordSpacing5">
          <input type="radio" name="is_divided"  value="0" <?php if ($this->_tpl_vars['row']['is_divided'] == 0): ?>checked="checked"<?php endif; ?>/> 不分
          <input type="radio" name="is_divided"  value="1" <?php if ($this->_tpl_vars['row']['is_divided'] == 1): ?>checked="checked"<?php endif; ?>/> 分
          <span id="is_divided_ratio" <?php if ($this->_tpl_vars['row']['is_divided'] == 0): ?>style="display:none"<?php endif; ?>>
          <input class="infoTableFile2" type="text" name="divided_ratio" id="divided_ratio" value="<?php if ($this->_tpl_vars['row']['divided_ratio']): ?><?php echo $this->_tpl_vars['row']['divided_ratio']; ?>
<?php else: ?>20<?php endif; ?>" placeholder="分成比率"  />
          店主选择：
          <select name="divided_user_id" id="divided_user_id"></select>
          </span>
        </td>
      </tr>
      <?php endif; ?>-->
      <tr>
        <th class="paddingT15">开通旗舰店:</th>
        <td class="paddingT15 wordSpacing5">
          <input type="radio" name="is_flag" class="flag" value="0" <?php if ($this->_tpl_vars['row']['is_flag'] == 0): ?>checked="checked"<?php endif; ?>/> 否
          <input type="radio" name="is_flag" class="flag" value="1" <?php if ($this->_tpl_vars['row']['is_flag'] == 1): ?>checked="checked"<?php endif; ?>/> 是
        </td>
      </tr>
      <tr id="res" <?php if ($this->_tpl_vars['row']['is_flag'] != 1): ?> style="display:none;" <?php endif; ?>>
      	<th class="paddingT15"> 店铺头图:</th>
        <td class="paddingT15 wordSpacing5">
            <input name="logoImg" id="logoImg" type="text" size="35" value="<?php echo $this->_tpl_vars['row']['shop_img']; ?>
"/>
            <span id="logoError" style="color: red;font-size: 12px;">店铺头图 <?php echo $this->_tpl_vars['shopheadsize']['width']; ?>
 * <?php echo $this->_tpl_vars['shopheadsize']['height']; ?>
</span>
        </td>
        <td><p><a href="#" id="dialog-logo" class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-newwin"></span>点击上传店铺头图</a></p></td>
     </tr>
     
    <tr id="isShow" <?php if ($this->_tpl_vars['row']['is_flag'] != 1): ?> style="display:none;" <?php endif; ?>>
      <th class="paddingT15"></th>
        <td id="logoTD">    
        <?php if ($this->_tpl_vars['row']['shop_img']): ?>
        <a href="<?php echo $this->_tpl_vars['_CONF']['IMG_URL']; ?>
/buy/shop/<?php echo $this->_tpl_vars['row']['shop_img']; ?>
" target="_BLANK" id="logHref"><img src="<?php echo $this->_tpl_vars['_CONF']['IMG_URL']; ?>
/buy/shop/<?php echo $this->_tpl_vars['row']['shop_img']; ?>
" id="logHtml"></a>
        <?php endif; ?>
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
                <input type="hidden" name="width"  id="width"  value="<?php echo $this->_tpl_vars['shopheadsize']['width']; ?>
">
                <input type="hidden" name="height" id="height" value="<?php echo $this->_tpl_vars['shopheadsize']['height']; ?>
">
            </fieldset>
        </form>
    </div>
</div>
<script type="text/javascript">
	$(function(){
		<?php if ($this->_tpl_vars['row']['is_divided'] == 1): ?>
			ajaxDivided(<?php echo $this->_tpl_vars['row']['divided_user_id']; ?>
);
		<?php endif; ?>		
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
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>