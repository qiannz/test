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
		if (rs == 3){
			$('#res').show();
		} else {
			$('#res').hide();
		}
	})
});
</script>

<div id="rightTop">
  <p>店铺管理</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/merchant/list">商户入驻</a></li>
    <li><span>商户审核</span></li>
  </ul>
</div>

<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="{{$_CONF.FORM_ACTION}}" id="audit_form">
    <input type="hidden" name="uid" id="uid" value="{{$uid}}" >
    <input type="hidden" name="page" id="page" value="{{$page}}" >
    <input type="hidden" name="uname" value="{{$row.user_name}}" >
    <input type="hidden" name="shop_name" value="{{$row.shop_name}}" >
    <input type="hidden" name="shop_address" value="{{$row.shop_address}}" >
    <input type="hidden" name="shop_id" value="{{$row.shop_id}}" >
       <div class="left">
       	<input type="radio" name="audit_type" value="1"> 通过审核　
        <input type="radio" name="audit_type" value="2"> 审核不通过　
         不通过原因：
        <select class="querySelect" name="reason1" id="sel">
            <option value = "1" >虚假信息</option>
            <option value = "2" >重复认证</option>
            <option value = "3" >其他原因</option>
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
	<tr>
        <th class="paddingT15">用户名：</th>
        <td class="paddingT15 wordSpacing5">{{$row.user_name}}</td>
    </tr>
    <tr>
        <th class="paddingT15">姓　名：</th>
        <td class="paddingT15 wordSpacing5">{{$row.real_name}}</td>
    </tr>
    <tr>
        <th class="paddingT15">手机号码：</th>
        <td class="paddingT15 wordSpacing5">{{$row.mobile}}</td>
    </tr>
	<tr>
        <th class="paddingT15">认领店铺：</th>
        <td class="paddingT15 wordSpacing5">{{$row.shop_name}}</td>
    </tr>
    <tr>
        <th class="paddingT15">店铺地址：</th>
        <td class="paddingT15 wordSpacing5">{{$row.shop_address}}</td>
    </tr>
	<tr>
        <th class="paddingT15">身份证照：</th>
        <td class="paddingT15 wordSpacing5"><img src="{{$_CONF.SITE_URL}}/data/verify/{{$row.id_img}}" class="makesmall" max_width="800" max_height="600" /></td>
    </tr>
    <tr>
        <th class="paddingT15">营业执照：</th>
        <td class="paddingT15 wordSpacing5"><img src="{{$_CONF.SITE_URL}}/data/verify/{{$row.bus_img}}" class="makesmall" max_width="800" max_height="600" /></td>
    </tr>
</table>
</div>
{{include file='admin/footer.php'}}