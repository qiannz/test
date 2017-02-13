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
			{{if !$psid or $pid gt 0}}
			pos_id : {
				required : true
			},
			{{/if}}
        	nav_name : {
                required : true,
				remote   : {
					url :'/admin/nav/check-name',
					type:'post',
					data:{
						nav_name : function(){
							return $('#nav_name').val();
						},
						pos_id : function() {
							return $('#pos_id').val() ? $('#pos_id').val() : '{{$psid}}';
						},
						id : '{{$row.nav_id}}'
					}
				}
            },
			nav_url : {
				required : true
			}    
        },
        messages : {
			{{if !$psid or $pid gt 0}}
            pos_id : {
            	required : '请选择分类名称'
            },
			{{/if}}
        	nav_name : {
                required : '请输入导航名称',
				remote : '当前分类下导航已存在，请换一个'
            },
			nav_url : {
				required : '请输入导航链接'
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
    <li><a class="btn1" href="/admin/nav/list">导航列表</a></li>
    <li><span>新增导航</span></li>
  </ul>
</div>
<div class="info">
<form method="POST" id="position_form">
<input type="hidden" name="id" value="{{$row.nav_id}}" />
<input type="hidden" name="pid" id="pid" value="{{$pid}}" />
{{if $psid}}
<input type="hidden" name="psid" id="psid" value="{{$psid}}" />
{{/if}}
<table class="infoTable">
	{{if !$psid or $pid gt 0}}
    <tr>
        <th class="paddingT15">分类名称:</th>
        <td class="paddingT15 wordSpacing5">
          <select name="pos_id" id="pos_id">
            <option value="">请选择分类</option>
          	{{foreach from=$posSortList key=key item=item}}
            <option value="{{$item.pos_id}}" {{if $psid && $item.pos_id eq $psid}}selected="selected"{{/if}}>{{$item.pos_name}}</option>
            {{/foreach}}
          </select>
          <label class="field_notice"></label>
          </td>
    </tr>
    {{/if}}
    <tr>
        <th class="paddingT15">导航名称:</th>
        <td class="paddingT15 wordSpacing5">
        <input class="infoTableInput2" id="nav_name" type="text" name="nav_name" value="{{$row.nav_name}}" style="width:200px;" />
        <label class="field_notice"></label>
        </td>
    </tr> 
    <tr>
        <th class="paddingT15">导航链接:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" id="nav_url" type="text" name="nav_url" value="{{$row.nav_url}}" style="width:500px;" />
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