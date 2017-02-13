{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/validate.min.js" ></script>
<script type="text/javascript">

$(function(){
	
	jQuery.validator.addMethod("isInter", function(value, element) {   
		var inter = /^-?[1-9][0-9]*$/;
		return this.optional(element) || (inter.test(value));
	}, "");
	
    $('#star_form').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error); 
        },
        success       : function(label){
            label.addClass('right').text('OK!');
        },
        rules : { 
            user_name : {
                required : true  
            }, 
			number : {
				required : true,
				isInter : true
			}
        },
        messages : {
            user_name :{
                required     : '指定用户名，用户名不能为空且一行一个会员名'
            },
			number :{
                required     : '请填写幸运星数量',
				isInter	 : '幸运星数量必须为正整数/负整数'
            }
        }
    });

});

</script>
<div id="rightTop">
  <p>用户管理</p>
  <ul class="subnav">
    <li><a class="btn4" href="/admin/user/list/page:{{$page}}">用户列表</a></li>
    <li><span>幸运星增/减</span></li>
  </ul>
</div>
<div class="info">
<form method="POST" action="{{$_CONF.FORM_ACTION}}" id="star_form">
<table class="infoTable">

    <tr id="user_list">
        <th class="paddingT15">用户列表:</th>
        <td class="paddingT15 wordSpacing5"><textarea name="user_name" style="height:100px;" id="user_name"></textarea><span class="field_notice">每行填写一个用户名<span></td>
    </tr>
    <tr id="msg">
        <th class="paddingT15">幸运星:</td>
        <td class="paddingT15 wordSpacing5"><input type="text" value="" id="number" name="number" class="infoTableInput2"><span class="field_notice">正整数/负整数<span></td>
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