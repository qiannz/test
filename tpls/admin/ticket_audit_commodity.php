{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/jquery.validate.min.js" ></script>
<script type="text/javascript">
$(function(){
    $('#audit_form').validate({
        errorPlacement: function(error, element){
            $(element).parent().find('span').html(error);
        },
		submitHandler: function(form) {
			$(form).find(":submit").attr("disabled", true).attr("value","提交...");
			form.submit();
		},
        rules : { 
        	audit_type : {
                required : true  
            },
            reason2 : {
            	required : true
            }
            
        },
        messages : {
        	audit_type :{
                required : '请选择审核操作'
            },
            reason2 : {
            	required : '请填写不通过原因'
            }
        }
    });

	$('#sel').change(function(){
		var rs = $(this).val();
		if (rs == 4){
			$('#res').show();
		} else {
			$('#res').hide();
		}
	})
});
</script>

<div id="rightTop">
  <p>商城商品</p>
  <ul class="subnav">
    <li><a class="btn4" href="/admin/commodity/list/page:{{$page}}">商品管理</a></li>
    <li><span>审核商城商品</span></li>
  </ul>
</div>

<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="{{$_CONF.FORM_ACTION}}" id="audit_form">
    <input type="hidden" name="tid" id="tid" value="{{$tid}}" >
    <input type="hidden" name="page" id="page" value="{{$page}}" >
    <input type="hidden" name="sid" id="sid" value="{{$row.shop_id}}" >
    <input type="hidden" name="title" id="title" value="{{$row.ticket_title}}" >
       <div class="left">
       	<input type="radio" name="audit_type" value="1"> 通过审核　
        <input type="radio" name="audit_type" value="2"> 审核不通过　
         不通过原因：
        <select class="querySelect" name="reason1" id="sel">
            <option value = "1" >虚假信息</option>
            <option value = "2" >恶意广告</option>
            <option value = "3" >敏感内容</option>
            <option value = "4" >其他原因</option>
        </select>&nbsp;
        <input id="res" class="queryInput" type="text" name="reason2" value="" style="display:none;" />
      	<input class="formbtn" type="submit" name="Submit" value="确定" />
        <span></span>
      </div>
    </form>
  </div>
</div>

<div class="info">
<table class="infoTable">
<!--<iframe width=100% height=600 frameborder=0 scrolling=auto src="/home/ticket/show/tid/{{$tid}}"></iframe>-->
</table>
</div>
{{include file='admin/footer.php'}}