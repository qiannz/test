{{include file='admin/header.php'}}
<div id="rightTop">
  <p>用户管理</p>
  <ul class="subnav">
  	<li><a class="btn4" href="/admin/user/list/page:{{$request.page}}">用户列表</a></li>
    <li><span>用户权限</span></li>
    <li><span>{{$uname}}</span> &hArr;</li>
    <li><span>{{if $request.utype eq 1}}营业员{{elseif $request.utype eq 2}}店长{{elseif $request.utype eq 3}}收银员{{/if}}</span></li>
  </ul>
</div>
<div class="tdare">
<form method="POST" action="{{$_CONF.FORM_ACTION}}" id="form">
<input type="hidden" name="uid" id="uid" value="{{$request.uid}}" />
<input type="hidden" name="uname" id="uname" value="{{$request.uname}}" />
<input type="hidden" name="utype" id="utype" value="{{$request.utype}}" />
<input type="hidden" name="page" id="page" value="{{$request.page}}" />
<input type="hidden" name="num" id="num" value="{{$row|@count}}" />
<table class="dataTable" width="400">
      <tr>
        <td class="paddingT15"> 是否允许线下预购:</td>
        <td class="paddingT15 wordSpacing5"><label><input type="checkbox" value="1" name="AllowOffline" {{if $row.AllowOffline eq '1'}}checked="checked"{{/if}} /></label></td>
      </tr>
      <tr>
        <td class="paddingT15"> 是否允许验证:</td>
        <td class="paddingT15 wordSpacing5"><label><input type="checkbox" value="1" name="AllowVerify" {{if $row.AllowVerify eq '1'}}checked="checked"{{/if}} /></label></td>
      </tr>
      <tr>
        <td class="paddingT15"> 是否允许查看验证记录:</td>
        <td class="paddingT15 wordSpacing5"><label><input type="checkbox" value="1" name="AllowView" {{if $row.AllowView eq '1'}}checked="checked"{{/if}} /></label></td>
      </tr>
      <tr>
        <td class="paddingT15"> 是否允许打印:</td>
        <td class="paddingT15 wordSpacing5"><label><input type="checkbox" value="1" name="AllowPrint" {{if $row.AllowPrint eq '1'}}checked="checked"{{/if}} /></label></td>
      </tr>
      <tr>
        <td class="paddingT15"> 是否允许商家验证:</td>
        <td class="paddingT15 wordSpacing5"><label><input type="checkbox" value="1" name="AllowMerchantVerify" {{if $row.AllowMerchantVerify eq '1'}}checked="checked"{{/if}} /></label></td>
      </tr>
      <tr>
        <td class="paddingT15"> 是否允许商家查看记录:</td>
        <td class="paddingT15 wordSpacing5"><label><input type="checkbox" value="1" name="AllowMerchantView" {{if $row.AllowMerchantView eq '1'}}checked="checked"{{/if}} /></label></td>
      </tr>
      <tr>
        <td class="paddingT15"> 是否允许商家管理:</td>
        <td class="paddingT15 wordSpacing5"><label><input type="checkbox" value="1" name="AllowMerchantManage" {{if $row.AllowMerchantManage eq '1'}}checked="checked"{{/if}} /></label></td>
      </tr>
      <tr>
        <td class="paddingT15"> 是否允许申请退款（货）:</td>
        <td class="paddingT15 wordSpacing5"><label><input type="checkbox" value="1" name="AllowRefundApply" {{if $row.AllowRefundApply eq '1'}}checked="checked"{{/if}} /></label></td>
      </tr>
      <tr>
        <td class="paddingT15"> 是否允许发放抵用券:</td>
        <td class="paddingT15 wordSpacing5"><label><input type="checkbox" value="1" name="AllowBindCoupon" {{if $row.AllowBindCoupon eq '1'}}checked="checked"{{/if}} /></label></td>
      </tr>
      <tr>
        <td class="ptb20" colspan="2"><input type="submit" value="提交" name="Submit" class="formbtn">
          <input type="reset" value="重置" name="Reset" class="formbtn">        
        </td>
      </tr>
</table>
</form>
</div>
<script type="text/javascript">
	var utype = $("#utype").val();
	var num = $("#num").val();
	if(num == 0) {
		if(utype == 1 || utype == 2) {
			$("input[name=AllowVerify], input[name=AllowPrint], input[name=AllowRefundApply]").attr("checked", true);
		} else if(utype == 3) {
			$("input[name=AllowVerify], input[name=AllowPrint]").attr("checked", true);
		}
	}
</script>
{{include file='admin/footer.php'}}