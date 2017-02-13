<?php /* Smarty version 2.6.27, created on 2016-02-04 15:20:37
         compiled from admin/ajax/ajax_color_size.php */ ?>
<div class="info">
<form method="POST" id="brand_sku_form">
    <input type="hidden" name="brand_id" id="brand_id" value="<?php echo $this->_tpl_vars['brand_id']; ?>
" />
    <input type="hidden" name="page" id="page" value="<?php echo $this->_tpl_vars['page']; ?>
" />
    <table class="infoTable">
        <tr>
            <th class="paddingT15"> 分类:</th>
            <td class="paddingT15 wordSpacing5" >
                <input  type="radio" name="type" value="1" <?php if ($this->_tpl_vars['row']['type'] == 1): ?>checked="checked"<?php endif; ?> /> 颜色
                <input  type="radio" name="type" value="2" <?php if ($this->_tpl_vars['row']['type'] == 2): ?>checked="checked"<?php endif; ?> /> 尺码
                <label class="field_notice"></label>
            </td>
            <td></td>
        </tr>
        <tr>
            <th class="paddingT15" width="200"> 名称:</th>
            <td class="paddingT15 wordSpacing5" width="40%">
                <input class="infoTableInput2" id="name" type="text" name="name" value="<?php echo $this->_tpl_vars['row']['name']; ?>
" />
                <label class="field_notice"></label>
            </td>
            <td></td>
        </tr>
        <tr>
            <th class="paddingT15"> 编号:</th>
            <td class="paddingT15 wordSpacing5">
                <input class="infoTableInput2" id="number" type="text" name="number" value="<?php echo $this->_tpl_vars['row']['number']; ?>
" />
                <label class="field_notice"></label>
            </td>
            <td></td>
        </tr>
<!--       <tr>
            <th class="paddingT15"> </th>
            <td class="ptb20">
                <input class="formbtn" type="button" value="新增" />
            </td>
        </tr>-->
    </table>
</form>
</div>
<script type="text/javascript">
var validArray = ['type', 'name', 'number'];
$('.formbtn').attr("disabled", false);

$(function(){	
	for(var i=0; i<validArray.length; i++) {
		$('#' + validArray[i]).bind('blur',function(){
			validInput($(this).attr("id"));
		});
	}
	
	$('.formbtn').click(function(){
		var len = 0;
		for(var i=0; i<validArray.length; i++) {
			if(validInput(validArray[i])) {
				len++;
			}
		}
		var _brand_id = $("#brand_id").val();
		var _type = $("input[name=type]:checked").val();
		var _name = $("#name").val();
		var _number = $("#number").val();
		
		if(len == validArray.length) {
			$('.formbtn').attr("value", "提交中...").attr("disabled", true);
			$.ajax({
                type:'POST',
                url:'/admin/brand/color-size',
                data:{bid : _brand_id, type:_type, name:_name, number:_number},
                dataType:'json',
                success:function(data){
                    if(data.res == 100){
						
                    }
                }
            });
		}	
	});
	
	function validInput(id) {
		var _msg = '';
		var _is_radio = 0;
		switch(id) {
			case 'type':
					_is_radio = 1;
					if($('input[name=' + id + ']:checked').val() == undefined) {
						_msg = '请选择分类';	
					}
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
});  
</script>