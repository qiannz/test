{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/validate.min.js" ></script>
<script type="text/javascript">
$(function(){
    $('#user_form').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
        success       : function(label){
            label.addClass('right').text('OK!');
        },
        onkeyup    : false,
        rules : {
            user_name : {
                required : true,
                byteRange: [3,15,'utf-8'],
                remote   : {
                    url :'/admin/manager/check-user',
                    type:'post',
                    data:{
                        user_name : function(){
                            return $('#user_name').val();
                        },
                        id : '{{$user.a_id}}'
                    }
                }
            },
            password: {
                <!--{{if $_ACTION eq 'add'}}-->
                required : true,
                <!--{{/if}}-->
                maxlength: 20,
                minlength: 6
            },
			re_password: {
                <!--{{if $_ACTION eq 'add'}}-->
                required : true,
                <!--{{/if}}-->
                maxlength: 20,
                minlength: 6,
				equalTo: "#password"
            }
			<!--{{if ($user.user_id && $user.role_id == 2) || (!$user.user_id)}}-->
			,
			gid : {
				required : true
			}
			<!--{{/if}}-->
        },
        messages : {
            user_name : {
                required : '会员名称不能为空 ',
                byteRange: '用户名的长度应在3-15个字符之间',
                remote   : '该会员名已经存在了，请您换一个'
            },
            password : {
                <!--{{if $_ACTION eq 'add'}}-->
                required : '密码不能为空',
                <!--{{/if}}-->
                maxlength: '密码长度应在6-20个字符之间',
                minlength: '密码长度应在6-20个字符之间'
            },
			re_password : {
				<!--{{if $_ACTION eq 'add'}}-->
                required : '密码不能为空',
                <!--{{/if}}-->
				maxlength: '密码长度应在6-20个字符之间',
                minlength: '密码长度应在6-20个字符之间',
                equalTo: "两次密码不一致"
            }
			<!--{{if ($user.user_id && $user.role_id == 2) || (!$user.user_id)}}-->
			,
			gid : {
				required : '请选择组'
			}
			<!--{{/if}}-->
        }
    });
});
</script>
<div id="rightTop">
  <p>管理员管理</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/manager/list">管理</a></li>
    <li>
      <!-- {{if $user.user_id}} -->
      <a class="btn1" href="/admin/manager/add">添加</a>
      <!-- {{else}} -->
      <span>添加</span>
      <!-- {{/if}} -->
    </li>
  </ul>
</div>
<div class="info">
  <form method="post" enctype="multipart/form-data" id="user_form">
    <input type="hidden" name="user_id" value="{{$myUser.id}}" />
    <table class="infoTable">
    <tr>
    <th class="paddingT15"> 会员名:</th>
    <td class="paddingT15 wordSpacing5">
    <!-- {{if $myUser.id}} -->
      {{$myUser.userid}}
      <input type="hidden" name="name" value="{$myUser.userid}" />
      <!-- {{else}} -->
      <input class="infoTableInput2" id="user_name" type="text" name="user_name" value="{{$myUser.userid}}" autocomplete="off" />
      <label class="field_notice">用户名</label>
      <!-- {{/if}} -->        
      </td>
    </tr>
    <tr>
    <th class="paddingT15"> 密码:</th>
    <td class="paddingT15 wordSpacing5"><input class="infoTableInput2" name="password" type="text" id="password" autocomplete="off" />     
     <!--{{if $myUser.id}} -->
      <label class="field_notice">留空表示不修改密码</label>
      <!-- {{/if}} --></td>
    </tr>
    <tr>
    <th class="paddingT15"> 确认密码:</th>
    <td class="paddingT15 wordSpacing5"><input class="infoTableInput2" name="re_password" type="text" id="re_password" />
    <label class="field_notice"></label></td>
    </tr>
    {{if ($myUser.id && $myUser.role_id == 2) || (!$myUser.id)}}
    <tr>
        <th class="paddingT15">
            <label for="gid">所属组:</label></th>
        <td class="paddingT15 wordSpacing5">
            <select id="gid" name="gid">
                <option value="">请选择...</option>
                {{foreach item=item key=key from=$groupArr}}
                <option value="{{$item.gid}}"{{if $item.gid eq $myUser.gid}} selected="selected"{{/if}}>{{$item.g_name}}</option>
                {{/foreach}}
            </select>
        </td>
    </tr>
    <tr>
    	 <th class="paddingT15">组管理员</th>
         <td class="paddingT15 wordSpacing5">
         	<input type="radio" name="group_admin" value="0" {{if $myUser.id && $myUser.group_admin eq 0 }}checked="checked"{{else}}checked="checked"{{/if}}/> 否
            <input type="radio" name="group_admin" value="1" {{if $myUser.id && $myUser.group_admin eq 1 }}checked="checked"{{/if}}/> 是
         </td>
    </tr>
    {{/if}}  
    <tr>
    <th></th>
    <td class="ptb20"><input class="formbtn" type="submit" name="Submit" value="提交" />
      <input class="formbtn" type="reset" name="Reset" value="重置" />        </td>
    </tr>
    </table>
  </form>
</div>
{{include file='admin/footer.php'}}