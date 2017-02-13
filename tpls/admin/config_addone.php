{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/validate.min.js" ></script>
<script type="text/javascript">
$(function(){
    $('#position_form').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error); 
        },
        rules : { 
        	config_key : {
                required : true  
            }, 
            config_value : {
            	required : true
            },
            config_ex : {
            	required : true
            }
        },
        messages : {
        	config_key :{
                required : '请输入全局设置标识(key)'
            },
            config_value : {
            	required : '请输全局设置内容(value)'
            },
            config_ex :{
                required : '请输入解释'
            }
        }
    });
});
</script>
<div id="rightTop">
  <p>推荐位管理</p>
  <ul class="subnav">
  	<li><span>新增全局配置(单例)</span></li>
    <li><a class="btn4" href="/admin/config/list">全局配置列表</a></li>
  </ul>
</div>
<div class="info">
<form method="POST" id="position_form">
<input type="hidden" name="pos_id" value="{{$pRow.pos_id}}">
<table class="infoTable">
      <tr>
        <th class="paddingT15"> 全局设置标识(key):</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" id="config_key" type="text" name="config_key" value="{{$pRow.pos_name}}" style="width:200px;" />
          <label class="field_notice"></label>
          </td>
      </tr>
    
    <tr>
        <th class="paddingT15"> 全局设置内容(value):</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" id="config_value" type="text" name="config_value" value="{{$pRow.identifier}}" style="width:200px;" />
          <label class="field_notice"></label>
          </td>
      </tr>

    <tr>
        <th class="paddingT15"> 解释:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" id="config_ex" type="text" name="config_ex" value="{{$pRow.width}}" style="width:200px;" />
          <label class="field_notice"></label>
          </td>
      </tr>
      
    <tr>
        <th class="paddingT15"> </th>
        <td class="ptb20">
    	  <input class="formbtn" type="submit" value="确定" />
          <input class="formbtn" type="reset" name="Submit2" value="重置" /></td>
    </tr>
</table>
</form>
</div>
{{include file='admin/footer.php'}}