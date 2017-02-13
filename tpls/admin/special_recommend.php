{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/validate.min.js" ></script>
<script type="text/javascript">
$(function(){
    $('#recommend').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error); 
        },
		submitHandler: function(form) {
			$(form).find(":submit").attr("disabled", true).attr("value","提交...");
			$(form).submit();
		},
        success       : function(label){
            label.addClass('right').text('OK!');
        },
        onkeyup    : false,
        rules : { 
			pos_id : {
				required:true
			}
        },
        messages : {
			pos_id : {
				required : '请选择推荐位'
			}
        }
    });
	
	showTr($('#pos_id').val());
});
var rjson = {{$rjson}};
function showTr(){
	 var pid = $('#pos_id').val();
	 var str='';
     $.each(rjson,function(index, sitem) {		 
		 $.each(sitem['child'], function(cindex, citem) {
			if(pid == citem.pos_id) {
				str = citem.width + '*' + citem.height;
			}					
		 });
 	});
   	$('#dis').html(str);
}
</script>
<div id="rightTop">
  <p>用户管理</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/special/list/page:{{$page}}">专题管理</a></li>
    <li><span>推荐</span></li>
    <li><span>{{$specialRow.title}}</span></li>
  </ul>
</div>
<div class="info">
 <form method="post" id="recommend" enctype="multipart/form-data" >
  <input type="hidden" name="sid" id="sid" value="{{$specialRow.special_id}}" />
  <input type="hidden" name="page" id="page" value="{{$page}}" />
    <table class="infoTable">
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
      <tr>
		<th class="paddingT15">图片上传：</th>
		<td class="paddingT15 wordSpacing5">
		<input id="uploadFile" type="file"  name="uploadFile" />
		</td>
	 </tr>
      <tr>
        <th></th>
        <td class="ptb20"><input class="formbtn" type="submit" name="submit" value="提交" />
          <input class="formbtn" type="reset" name="reset" value="重置" /></td>
      </tr>
    </table>
  </form>
</div>
{{include file='admin/footer.php'}}