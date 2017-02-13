{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/jquery.validate.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
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
	
	var auditDay = $('#auditDay').val();
	var gname = $('#gname').val();
	var gid = $('#gid').val();
	var page = $('#page').val();
/*	$.post('/admin/good/check-audit', {audit_day: auditDay}, function(data){
		if (data == 'audit') {
			$.dialog.alert('之前还有商品没有被审核！');
		}
	});*/
});
</script>
<div id="rightTop">
  <p>商品管理</p>
  <ul class="subnav"> 
    <li><a class="btn1" href="/admin/good/list">商品列表</a></li>
    <li><span>审核商品</span></li>
  </ul>
</div>

<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="{{$_CONF.FORM_ACTION}}" id="audit_form">
    <input type="hidden" name="gid" id="gid" value="{{$gid}}" >
    <input type="hidden" name="page" id="page" value="{{$page}}" >
    <input type="hidden" name="audit_day" id="auditDay" value="{{$audit_day}}" >
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
        <input id="res" class="queryInput" type="text" name="reason2" value="" style="display:none;">
      	<input class="formbtn" type="submit" name="Submit" value="确定" />
        <span></span>
      </div>
    </form>
  </div>
</div>

<div class="info">
<table class="infoTable">
<iframe width=100% height=600 frameborder=0 scrolling=auto src="/home/good/show/gid/{{$gid}}"></iframe>
</table>
</div>
{{include file='admin/footer.php'}}