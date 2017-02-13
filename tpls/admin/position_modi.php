{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/validate.min.js" ></script>
<script type="text/javascript">
$(function(){
    $('#position_form').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error); 
        },
		success       : function(label){
            label.addClass('right').text('OK!');
        },
        rules : { 
        	pos_name : {
                required : true,
				remote   : {
					url :'/admin/position/check-name',
					type:'post',
					data:{
						pos_name : function(){
							return $('#pos_name').val();
						},
						id : '{{$row.pos_id}}'
					}
				}
            }, 
            identifier : {
            	required : true,
				isCode : true,
				remote   : {
					url :'/admin/position/check-plate',
					type:'post',
					data:{
						identifier : function(){
							return $('#identifier').val();
						},
						id : '{{$row.pos_id}}'
					}
				}
            }
        },
        messages : {
        	pos_name :{
                required : '请输入推荐位名称',
				remote : '推荐位名称已存在，请换一个'
            },
            identifier : {
            	required : '请输入推荐位标识',
				isCode : '请输入正确的标记位',
				remote : '推荐位标记已存在，请换一个'
            }
        }
    });
});

jQuery.validator.addMethod("isCode", function(value, element) {  
    var code = /^[a-z_]+$/;
    return this.optional(element) || (code.test(value));
}, "");
</script>
<div id="rightTop">
  <p>推荐管理</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/position/list">推荐位列表</a></li>
    <li><span>新增推荐位</span></li>
  </ul>
</div>
<div class="info">
<form method="POST" id="position_form">
<input type="hidden" name="id" value="{{$row.pos_id}}" />
<input type="hidden" name="pid" id="pid" value="{{$pid}}" />
<table class="infoTable">
    <tr>
        <th class="paddingT15">推荐位名称:</th>
        <td class="paddingT15 wordSpacing5">
        <input class="infoTableInput2" id="pos_name" type="text" name="pos_name" value="{{$row.pos_name}}" style="width:200px;" />
        <label class="field_notice"></label>
        </td>
    </tr>    
    <tr>
        <th class="paddingT15">推荐位标识:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" id="identifier" type="text" name="identifier" value="{{$row.identifier}}" style="width:200px;" />
          <label class="field_notice">只能是小写字母和下划线构成</label>
          </td>
    </tr>
    {{if $pid && $pid gt 0 && $row.pos_id}}    
    <tr>
        <th class="paddingT15">分类名称:</th>
        <td class="paddingT15 wordSpacing5">
          <select name="pos_pid" id="pos_pid">
          	{{foreach from=$parentSortList key=key item=item}}
            <option value="{{$item.pos_id}}" {{if $item.pos_id eq $pid}}selected="selected"{{/if}}>{{$item.pos_name}}</option>
            {{/foreach}}
          </select>
          <label class="field_notice"></label>
          </td>
    </tr>
    {{/if}}
    {{if $pid && $pid gt 0}} 
    <tr>
        <th class="paddingT15"> 宽度:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" id="width" type="text" name="width" value="{{if $row.width}}{{$row.width}}{{/if}}" style="width:50px;" />
          <label class="field_notice"></label>
        </td>
    </tr>
    <tr>
        <th class="paddingT15"> 高度:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" id="height" type="text" name="height" value="{{if $row.height}}{{$row.height}}{{/if}}" style="width:50px;" />
          <label class="field_notice"></label>
      	</td>
    </tr>
    {{/if}}
    <tr>
        <th class="paddingT15">链接地址:</th>
        <td class="paddingT15 wordSpacing5">
        <input class="infoTableInput2" id="pos_url" type="text" name="pos_url" value="{{$row.pos_url}}" style="width:500px;" />
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