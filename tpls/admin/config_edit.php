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
  <p>全局配置</p>
  <ul class="subnav">
  	<li><span>编辑全局配置</span></li>
  	<li><a class="btn4" href="/admin/config/show/config_key:{{$configRow.config_key}}">全局配置详情</a></li>
    <li><a class="btn4" href="/admin/config/list">全局配置列表</a></li>
  </ul>
</div>
<div class="info">
<form method="POST" id="position_form">
<input type="hidden" name="id" value="{{$configRow.config_id}}">
<table class="infoTable">
      <tr>
        <th class="paddingT15"> 全局设置标识(key):</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" id="config_key" type="hidden" name="config_key" value="{{$configRow.config_key}}" style="width:200px;" />
          {{$configRow.config_key}}
          <label class="field_notice"></label>
          </td>
      </tr>
    
     <tr>
        <th class="paddingT15"> 解释:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" id="config_ex" type="text" name="config_ex" value="{{$configRow.config_ex}}" style="width:200px;" />
          <label class="field_notice"></label>
          </td>
      </tr>
      
    <tr>
        <th class="paddingT15"> 全局设置内容(value):</th>
        <td class="paddingT15 wordSpacing5">
          {{foreach from=$configRow.value_data key=key item=item}}
          {{if $key}} {{$key}} : <input class="infoTableInput2" type="text" name="{{if $key}}{{$key}}{{else}}config_value{{/if}}" value="{{$item}}" style="width:200px;" />
          {{else}}
          <input class="infoTableInput2" type="text" name="config_value" value="{{$item}}" style="width:800px;" />
          {{/if}}
          
          {{/foreach}}
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