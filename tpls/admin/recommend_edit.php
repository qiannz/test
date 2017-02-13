{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/validate.min.js" ></script>
<script type="text/javascript">
var rjson = {{$rjson}};
var moduleLocationListJson = {{$moduleLocationListJson}};

$(function(){
    $('#recommend_form').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error); 
        },
        success       : function(label){
            label.addClass('right').text('OK!');
        },
        rules : { 
        	title : {
                required : true  
            }, 
            pos_id : {
            	required : true
            },
            summary : {
            	required : true,
            	maxlength: 200
            }    
        },
        messages : {
        	title :{
                required     : '请输入推荐标题'
            },
            pos_id : {
            	required : '请选择推荐位'
            },
            summary :{
                required  : '请输入简介',
				maxlength : '只能输入200字'
            }
        }
    });
});


function showTr(){
	 var pid;
	 if(arguments.length == 1) {
	 	pid = arguments[0];
	 } else {
	 	pid = $('#pos_id').val();
	 }
	 var str='';
     $.each(rjson,function(index, sitem) {		 
		 $.each(sitem['child'], function(cindex, citem) {
			if(pid == citem.pos_id) {
				str = citem.width + '*' + citem.height;
			}					
		 });
 	});
   	$('span#dis').html(str);
}
</script>
<div id="rightTop">
  <p>推荐管理</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/recommend/list">推荐列表</a></li>
    <li>
      {{if $recommend.recommend_id}}
      <a class="btn1" href="/admin/recommend/add">新增</a>
      {{else}}
      <span>新增</span>
      {{/if}}
    </li>
  </ul>
</div>
<div class="info">
<form method="POST" id="recommend_form" enctype="multipart/form-data">
<input type="hidden" name="id" value="{{$recommend.recommend_id}}">
<input type="hidden" name="page" value="{{$page}}">
<table class="infoTable">
	<tr>
		<th class="paddingT15">推荐标题:</th>
		<td class="paddingT15 wordSpacing5">
		<input class="infoTableInput2" type="text" name="title" id="title"  value="{{$recommend.title}}"  style="width:400px;" />
		</td>
	</tr>
	
	<tr>
		<th class="paddingT15">推荐位:</th>
		<td class="paddingT15 wordSpacing5">
		<select name="pos_id" id="pos_id" onChange="showTr();">
            	<option value="">请选择...</option>
            	{{foreach from=$position key=key item=item}}
            		<optgroup label="{{$item.pos_name}}"/>
                    {{foreach from=$item.child key=skey item=sitem}}
            		<option value="{{$sitem.pos_id}}" {{if $recommend.pos_id eq $sitem.pos_id}} selected="selected"{{/if}}>{{$sitem.pos_name}}</option>
                    {{/foreach}}
            	{{/foreach}}
        </select>
        <span id="dis" style="color:red"></span>
		</td>
	</tr>
	
<!--	<tr>
		<th class="paddingT15">排序:</th>
		<td class="paddingT15 wordSpacing5">
			<input id="sequence" class="infoTableInput2" type="text" name="sequence" value="{{if $recommend.sequence}}{{$recommend.sequence}}{{else}}99{{/if}}"  />
		</td>
	</tr>-->
    
    <tr>
        <th class="paddingT15">简介:</th>
        <td class="paddingT15 wordSpacing5"><textarea id="summary" name="summary" style="width:300px; height:150px;" class="limitedContent">{{$recommend.summary}}</textarea>
        <span class="field_notice">200字以内（汉字算一个字符）</span>
        </td>
    </tr>

    <tr>
        <th class="paddingT15">图片:</th>
        <td class="paddingT15 wordSpacing5">
		<input id="uploadFile" type="file"  name="uploadFile" />
		<input type="hidden" name="img_url" value="{{$recommend.img_url}}" />
        </td>
    </tr>
    {{if $recommend.img_url}}
    <tr>
    	<th colspan="paddingT15"></th>
        <td><img src="{{$_CONF.SITE_URL}}/data/recommend/{{$recommend.img_url}}"></td>
    </tr>
    {{/if}}
    <tr>
		<th class="paddingT15">链接地址:</th>
		<td class="paddingT15 wordSpacing5">
		<input class="infoTableInput" id="www_url" type="text" name="www_url" value="{{$recommend.www_url}}" style="width:500px;" />
		</td>
	</tr>
	<tr>
		<th class="paddingT15">链接目标:</th>
		<td class="paddingT15 wordSpacing5">
		<select name="pmark" id="pmark">
        	<option value="">请选择</option>
        	{{foreach from=$moduleLocationList key=key item=item}}
            <option value="{{$key}}">{{$item.name}}</option>
            {{/foreach}}
        </select>
        <select name="cmark" id="cmark"><option value="">请选择</option></select>
		</td>
	</tr>
    <tr>
		<th class="paddingT15">目标ID:</th>
		<td class="paddingT15 wordSpacing5">
		<input class="infoTableInput" id="come_from_id" type="text" name="come_from_id" value="{{$recommend.come_from_id}}" />
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
<script type="text/javascript">
{{if $recommend.recommend_id}}showTr({{$recommend.pos_id}});{{/if}}

{{if $recommend.pmark}}
	$("#pmark").val('{{$recommend.pmark}}');
	$("#cmark").empty().append("<option value=''>请选择</option>");
		$.each(moduleLocationListJson, function(k, v){
			if('{{$recommend.pmark}}' == k) {
				$.each(v.child, function(ck, cv){
					$("#cmark").append("<option value='"+cv.mark+"'>"+cv.name+"</option>");
				});
			}
		});
{{/if}}

{{if $recommend.cmark}}
	$("#cmark").val('{{$recommend.cmark}}');
{{/if}}

$("#pmark").change(function(){
	if(!$(this).val()) {
		$("#cmark").empty().append("<option value=''>请选择</option>");
	} else {
		var _value = $(this).val();
		$("#cmark").empty().append("<option value=''>请选择</option>");
		$.each(moduleLocationListJson, function(k, v){
			if(_value == k) {
				$.each(v.child, function(ck, cv){
					$("#cmark").append("<option value='"+cv.mark+"'>"+cv.name+"</option>");
				});
			}
		});
	}
});
</script>
{{include file='admin/footer.php'}}