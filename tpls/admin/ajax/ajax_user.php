<form method="POST" action="">
<input type="hidden" name="uid" id="uid" value="{{$mobileRow.user_id}}" />
<table class="infoTable">      
    <tr>
        <th class="paddingT15">真实姓名:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" type="text" name="real_name" id="real_name" value="{{$mobileRow.RealName}}" />
          <label class="field_notice"></label>
        </td>
    </tr>
    <tr>
        <th class="paddingT15">手机号码:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableInput2" type="text" name="mobile" id="mobile" value="{{$mobileRow.Mobile}}" />
          <label class="field_notice"></label>
        </td>
    </tr>
    <tr>
        <th class="paddingT15">身份:</th>
        <td class="paddingT15 wordSpacing5">
          <input type="radio" name="user_type" class="flag" value="1" checked="checked" /> 营业员
          <input type="radio" name="user_type" class="flag" value="3" /> 收银员
        </td>
      </tr>
    <tr>
        <th class="paddingT15"> </th>
        <td class="ptb20">
            <input type="button" class="formbtn1" value="确认" />
        </td>
    </tr>
</table>
</form>
<script type="text/javascript">
$(function(){	
	$(".formbtn1").click(function(){
		var real_name_obj = $('#real_name');
		var mobile_obj = $("#mobile");
		if(real_name_obj.val().length == 0) {
			real_name_obj.next("label").addClass("error").html('请填写真实姓名');
			return false;
		}
		
		if(!/^1[2-9][0-9]{9}$/.test(mobile_obj.val())) {
			mobile_obj.next("label").addClass("error").html('请填写正确的手机号码');
			return false;
		}
		
		
		var _this = $(this);
		_this.val('确认。。。').attr('disabled', true);
		$.ajax({
			type: "POST",
			url: "/admin/shop/add-staff",
			data: "sid=" + $('#sid').val() + "&real_name=" + real_name_obj.val() + "&uid=" + $('#uid').val() + "&mobile=" + mobile_obj.val() + "&user_type=" + $("input[name=user_type]:checked").val() + "&page=" + $("#page").val(),
			dataType : 'text',
			success: function(data) {
				_this.val('确认').attr('disabled', false);
				$(".info").html(data);
			}
		});		
	});
	
});
</script>