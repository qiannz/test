{{include file='admin/header.php'}}
<style type="text/css">
	.img img{max-width:160px;max-height:160px;}
	.choiceBox a{position:relative;}
	.choiceBox a img {position: absolute;top: -6px;right: -6px;width: 20px;cursor:pointer;}
</style>
<div id="rightTop">
  <p>数据配置</p>
  <ul class="subnav">
    <li><a class="btn4" href="/admin/slogan/list/page:{{$page}}">宣传标语管理</a></li>
    <li><span>{{if $row.slogan_id}}编辑宣传标语{{else}}新建宣传标语{{/if}}</span></li>
  </ul>
</div>
<div class="info">
<form method="POST" enctype="multipart/form-data" action="{{$_CONF.FORM_ACTION}}" id="form">
<input type="hidden" name="page" id="page" value="{{$page}}" />
<input type="hidden" name="sid" id="sid" value="{{$row.slogan_id}}" />
	<table class="infoTable">
			<tr>
		        <th class="paddingT15">类型:</th>
		        <td class="paddingT15 wordSpacing5">
					 <select name="category" id="category">
		            	<option value="">全部</option>
		                <option value="1"  {{if $row.category eq 1 }}selected="selected"{{/if}}>商品</option>
		                <option value="2"  {{if $row.category eq 2 }}selected="selected"{{/if}}>店铺</option>
		                <option value="3"  {{if $row.category eq 3 }}selected="selected"{{/if}}>商场</option>
		                <option value="4"  {{if $row.category eq 4 }}selected="selected"{{/if}}>品牌</option>
		                <option value="5"  {{if $row.category eq 5 }}selected="selected"{{/if}}>收藏折扣</option>
		                <option value="6"  {{if $row.category eq 6 }}selected="selected"{{/if}}>发布折扣</option>
		                <option value="7"  {{if $row.category eq 7 }}selected="selected"{{/if}}>浏览折扣</option>
		            </select>
		            <label class="field_notice"></label>
		        </td>
		      </tr> 
		      <tr>
		        <th class="paddingT15">宣传标语:</th>
		        <td class="paddingT15 wordSpacing5">
		          <input class="infoTableInput2" type="text" name="name" id="name" value="{{$row.name}}" style="width:400px;" />
		          <label class="field_notice">格式为：这个{name}非常赞；{name}是（商品，店铺，商场，品牌，折扣）名称的占位符</label>
		        </td>
		      </tr>
		    <tr>
		    	<th></th>
		    	<td><br/></td>
		    <tr>
	</table>
    <table class="infoTable">
    	<tbody>
        <tr>
            <th class="paddingT15"> </th>
            <td class="ptb20">
                {{if $row.slogan_id}}
                <input type="button" value="编辑" name="Submit" class="formbtn1" onClick="checkSubmit()">
                <input type="reset" value="重置" name="reset" class="formbtn2">
                {{else}}
                <input type="button" value="提交" name="Submit" class="formbtn1" onClick="checkSubmit()">
                {{/if}}
            </td>
        </tr>   
        </tbody> 
    </table>
</div>
</form>
</div>
<script charset="utf-8" type="text/javascript" src="/js/jquery.validate.min.js" ></script>
<script type="text/javascript" src="/js/jquery.form.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript">
$(function(){
	$('#form').validate({
        errorPlacement: function(error, element){
			$(element).next('.field_notice').hide();
			$(element).after(error); 
        },
		success : function(label){
			label.addClass('right').text('OK!');
		},
		onkeyup : false, 
        rules : { 
        	category : {
				required : true
			},
			name : {
				required : true
			}
        },
        messages : {
			category : {
				required : '请选择类型'
			},
			name : {
				required : '请输入宣传标语',
			}
        }
    });
});

function checkSubmit()
{
	if($("#form").valid())
	{
		$('.formbtn1').attr("value", "提交中...").attr("disabled", true);
		$("#form").submit();
	}
}
</script>
{{include file='admin/footer.php'}}
