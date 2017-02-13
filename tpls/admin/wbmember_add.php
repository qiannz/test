{{include file='admin/header.php'}}
<style type="text/css">
	.img img{max-width:160px;max-height:160px;}
	.choiceBox a{position:relative;}
	.choiceBox a img {position: absolute;top: -6px;right: -6px;width: 20px;cursor:pointer;}
</style>
<div id="rightTop">
  <p>微商系统</p>
  <ul class="subnav">
  	{{if $from neq 'add' }}
    <li><a class="btn4" href="/admin/wbmember/list/page:{{$page}}">会员管理</a></li>
    {{/if}}
    <li><span>{{if $row.user_id}}编辑会员{{else}}新建会员{{/if}}</span></li>
  </ul>
</div>
<div class="info">
<form method="POST" enctype="multipart/form-data" action="{{$_CONF.FORM_ACTION}}" id="form">
	<input type="hidden" name="page" id="page" value="{{$page}}" />
	<input type="hidden" name="uid" id="uid" value="{{$row.user_id}}" />
	<table class="infoTable">
		      <tr>
		        <th class="paddingT15">姓名:</th>
		        <td class="paddingT15 wordSpacing5">
		          <input class="infoTableInput2" type="text" name=realname id="realname" value="{{$row.realname}}" style="width:140px;" />
		          <label class="field_notice">必填项</label>
		        </td>
		      </tr>
		      <tr>
		        <th class="paddingT15">手机号:</th>
		        <td class="paddingT15 wordSpacing5">
		          <input class="infoTableFile2" type="text" name="mobile" id="mobile" value="{{$row.mobile}}" style="width:140px;"/>
		          <label class="field_notice">必填项</label>
		        </td>
		      </tr>
		      <tr>
		        <th class="paddingT15">会员类型:</th>
		        <td class="paddingT15 wordSpacing5">
					<select name="user_type" id="user_type">
		            	<option value="">请选择会员类型</option>
		            	<option value="1" {{if $row.user_type eq 1}}selected="selected"{{/if}}>微商</option>
		                <option value="2" {{if $row.user_type eq 2}}selected="selected"{{/if}}>代购</option>
		                <option value="3" {{if $row.user_type eq 3}}selected="selected"{{/if}}>切货</option>
		                <option value="4" {{if $row.user_type eq 4}}selected="selected"{{/if}}>游客VIP</option>
		            </select>	
		            <label class="field_notice"></label>
		        </td>
		      </tr> 
	
		      <tr>
		        <th class="paddingT15">申请说明:</th>
		        <td class="paddingT15 wordSpacing5">
		          <textarea class="infoTableFile2" style="width:200px; height:100px;" name="apply_reason" id="apply_reason" value="{{$row.apply_reason}}"></textarea>
				  <label class="field_notice">最多50个字符，汉字算一个字符</label>
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
{{if $from eq 'add' }}
<div class="tdare">
	<h1>最新的会员列表</h1>
	<table width="100%" cellspacing="0" class="dataTable">
	    <tr class="tatr1">
	      <td>ID</td>
	      <td width="15%">手机号</td>
	      <td>姓名</td>
	      <td>申请类型</td>
		  <td>申请说明</td>
		  <td>申请时间</td>
		  <td>审核状态</td>
	    </tr>
	    {{foreach from=$data key=key item=item}}
	    <tr class="tatr2">
	      <td>{{$item.user_id}}</td>
	      <td>{{$item.mobile}}</td>
	      <td>{{$item.realname}}</td>
	      <td>
	      		{{if $item.user_type eq '1'}}微商
	      		{{elseif $item.user_type eq '2'}}代购
	      		{{elseif $item.user_type eq '3'}}切货
	      		{{elseif $item.user_type eq '4'}}游客VIP
	      		{{/if}}
	      </td>
	      <td>{{$item.apply_reason}}</td>
	      <td>{{if $item.created}}{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}{{/if}}</td>
	      <td>
	      		{{if $item.user_status eq '-1'}}审核拒绝
	      		{{elseif $item.user_status eq '0'}}未审核
				{{elseif $item.user_status eq '1'}}审核通过
	            {{/if}}
	      </td>
	    </tr>
	   {{foreachelse}}
	    <tr class="no_data">
	      <td colspan="9">暂无数据</td>
	    </tr>
	    {{/foreach}}
	</table>
</div>
{{/if}}
</div>
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<link rel="stylesheet" type="text/css" href="/css/admin/upload.css" />
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
			realname : {
				required : true
			},
			mobile : {
				required : true,
				isMobile : true,
				remote: {
                    type: "post",
                    url: "/admin/wbmember/mobile-is-exist",
                    data: {
                        mobile: function() {
                            return $("#mobile").val();
                        },
                        uid: function(){
							return $("#uid").val();
                        }
	                },
	                dataType: "html",
	                dataFilter: function(data, type) {
	                    if (data == 1)
	                        return true;
	                    else
	                        return false;
	                }
				}
			},
			user_type:{
				required : true
			}
        },
        messages : {
        	realname : {
				required : '请输入用户姓名'
			},
			mobile : {
				required : '请输入手机号码',
				remote   : '该手机号码已存在'
			},
			user_type : {
				required : '请选择用户类型'
			}
        }
    });
});
function checkSubmit()
{
	if($("#form").valid())
	{
		$("#content").val(editor.html());
		$('.formbtn1').attr("value", "提交中...").attr("disabled", true);
		$("#form").submit();
	}
}
</script>
{{include file='admin/footer.php'}}
