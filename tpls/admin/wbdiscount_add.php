{{include file='admin/header.php'}}
<style type="text/css">
	.img img{max-width:160px;max-height:160px;}
	.choiceBox a{position:relative;}
	.choiceBox a img {position: absolute;top: -6px;right: -6px;width: 20px;cursor:pointer;}
</style>
<div id="rightTop">
  <p>微商系统</p>
  <ul class="subnav">
    <li><a class="btn4" href="/admin/wbdiscount/list/page:{{$page}}">折扣管理</a></li>
    <li><span>{{if $row.id}}编辑折扣{{else}}新建折扣{{/if}}</span></li>
  </ul>
</div>
<div class="info">
<form method="POST" enctype="multipart/form-data" action="{{$_CONF.FORM_ACTION}}" id="form">
	<input type="hidden" name="page" id="page" value="{{$page}}" />
	<input type="hidden" name="id" id="id" value="{{$row.id}}" />
	<table class="infoTable">
		      <tr>
		        <th class="paddingT15">起始价格:</th>
		        <td class="paddingT15 wordSpacing5">
		          <input class="infoTableInput2" type="text" name="min_price" id="min_price" value="{{$row.min_price}}" style="width:140px;" />
		          <label class="field_notice">必填项</label>
		        </td>
		      </tr>
		      <tr>
		        <th class="paddingT15">结束价格:</th>
		        <td class="paddingT15 wordSpacing5">
		          <input class="infoTableFile2" type="text" name="max_price" id="max_price" value="{{$row.max_price}}" style="width:140px;"/>
		          <label class="field_notice">必填项(结束价格必须大于起始价格)</label>
		        </td>
		      </tr>
		      <tr>
		        <th class="paddingT15">折扣:</th>
		        <td class="paddingT15 wordSpacing5">
		          <input class="infoTableFile2" type="text" name="discount" id="discount" value="{{$row.discount}}" style="width:140px;"/>
				  <label class="field_notice">必填项(0-99之间的整数)</label>
		        </td>
		      </tr>
		    <tr>
		        <th class="paddingT15"> </th>
		        <td class="ptb20">
		          <input class="formbtn" type="submit" name="Submit" value="确定" />
		          <input class="formbtn" type="reset" name="Submit2" value="重置" />
		        </td>
		    </tr>
	</table>
</form>
</div>
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<link rel="stylesheet" type="text/css" href="/css/admin/popup.css" />
<link rel="stylesheet" type="text/css" href="/css/JqueryDialog/jquery-ui-1.9.2.custom.min.css" />
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" charset="utf-8" ></script>
<script type="text/javascript" src="/js/jquery.validate.min.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/JqueryDialog/jquery-ui-1.9.2.custom.min.js" charset="utf-8" ></script>
<script type="text/javascript">
$(function(){
	// 手机号码验证
	jQuery.validator.addMethod("isMobile", function(value, element) {
	    var length = value.length;
	    var mobile = /^1[2-9][0-9]{9}$/;
	    return this.optional(element) || (length == 11 && mobile.test(value));
	}, "请输入正确手机号码");
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
        	min_price : {
				required : true,
				number   : true
			},
			max_price : {
				required : true,
				number   : true
			},
			discount:{
				required : true,
				digits   : true,
				range    : [0,99]
			}
        },
        messages : {
        	min_price : {
				required : '请输入起始价格',
				number   : '请输入合法的数字'
			},
			max_price : {
				required : '请输入结束价格',
				number   : '请输入合法的数字'
			},
			discount : {
				required : '请输入折扣',
				digits   : '请输入整数',
				range    : '输入的数字必须范围在0-99之间'
			}
        }
    });
});
</script>
{{include file='admin/footer.php'}}
