{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/validate.min.js" ></script>
<script type="text/javascript">
$(function(){
    $('#circle_form').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error); 
        },
        success       : function(label){
            label.addClass('right').text('OK!');
        },
        rules : { 
        	region_id : {
                required : true  
            }, 
            circle_name : {
            	required : true
            }    
        },
        messages : {
        	region_id :{
                required     : '请选择行政区'
            },
            circle_name : {
            	required : '请填写商圈名'
            }
        }
    });

	{{if $circle.circle_id}}
		$('#region_id').val({{$circle.region_id}});
	{{/if}}
});

</script>
<div id="rightTop">
  <p>数据配置</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/circle/list">商圈管理</a></li>
    <li>
      {{if $recommend.recommend_id}}
       <span>编辑</span>
      {{else}}
      <span>新增</span>
      {{/if}}
    </li>
  </ul>
</div>
<div class="info">
<form method="POST" id="circle_form">
<input type="hidden" name="circle_id" value="{{$circle.circle_id}}">
<table class="infoTable">
	<tr>
        <th class="paddingT15">行政区:</th>
        <td class="paddingT15 wordSpacing5">
            <select name="region_id" id="region_id">
            	<option value="">请选择行政区</option>
                {{foreach from=$region key=key item=item}}
                <option value="{{$key}}">{{$item}}</option>
                {{/foreach}}
            </select>
        <span class="field_notice"></span>
        </td>
    </tr>
	
	<tr>
		<th class="paddingT15">商圈名称:</th>
		<td class="paddingT15 wordSpacing5">
		<input class="infoTableInput2" type="text" name="circle_name" id="circle_name"  value="{{$circle.circle_name}}" />
		<span class="field_notice"></span>
		</td>
	</tr>
	    
    <tr>
		<th class="paddingT15">是否热门:</th>
		<td class="paddingT15 wordSpacing5">
			<input  id="is_hot" type="radio" name="is_hot" value="1" {{if $circle.is_hot eq 1}}checked="checked"{{/if}} />是
	        <input  id="is_hot" type="radio" name="is_hot" value="0" {{if $circle.is_hot eq 0}}checked="checked"{{/if}} />否
		</td>
	</tr>
	
	<tr>
		<th class="paddingT15">是否展示:</th>
		<td class="paddingT15 wordSpacing5">
			<input  id="is_show" type="radio" name="is_show" value="1" {{if $circle.is_show eq 1}}checked="checked"{{/if}} />是
	        <input  id="is_show" type="radio" name="is_show" value="0" {{if $circle.is_show eq 0}}checked="checked"{{/if}} />否
		</td>
	</tr>
	
    <tr>
        <th class="paddingT15"> </th>
        <td class="ptb20"><input class="formbtn" type="submit" name="Submit" value="确定" />
          <input class="formbtn" type="reset" name="Submit2" value="重置" /></td>
    </tr>
</table>
</form>
</div>

{{include file='admin/footer.php'}}