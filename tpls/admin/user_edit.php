{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css" />
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js"></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js"></script>
<div id="rightTop">
  <p>用户管理</p>
  <ul class="subnav">
    <li><a class="btn4" href="/admin/user/list/page:{{$page}}">用户列表</a></li>
    <li><span>用户编辑</span></li>
  </ul>
</div>
<div class="info">
<form method="POST" action="{{$_CONF.FORM_ACTION}}" id="form">
<input type="hidden" name="uid" id="uid" value="{{$uid}}" />
<input type="hidden" name="utype" id="utype" value="{{$row.user_type}}" />
<input type="hidden" name="page" id="page" value="{{$page}}" />
<table class="infoTable">
      <tbody><tr>
        <th class="paddingT15"> 用户名:</th>
        <td class="paddingT15 wordSpacing5">{{$row.user_name}}</td>
      </tr>
      <tr>
        <th class="paddingT15"> 用户分类:</th>
        <td class="paddingT15 wordSpacing5"><p>
            <label><input type="radio" value="1" name="user_type" {{if $row.user_type eq '1'}}checked="checked"{{/if}} /> 普通用户</label>
            <label><input type="radio" value="2" name="user_type" {{if $row.user_type eq '2'}}checked="checked"{{/if}} /> 认证商户</label>
            <label><input type="radio" value="3" name="user_type" {{if $row.user_type eq '3'}}checked="checked"{{/if}} /> 营业员</label>
          </p>       
        </td>
      </tr>
      <tr>
        <th class="paddingT15"> 名下店铺:</th>
        <td class="paddingT15 wordSpacing5">
        	<select name="region_id" id="region_id">
            	<option value="">请选择所在区</option>
                {{foreach from=$regionArray key=key item=item}}
                <option value="{{$key}}">{{$item}}</option>
                {{/foreach}}
            </select>
            <select name="circle_id" id="circle_id"><option value="">请选择商圈</option></select>
            <select name="shop_id" id="shop_id"><option value="">请选择店铺</option></select>
            <input type="checkbox" name="ck" id="ck" value="1" /> 忽略商圈    
        </td>
      </tr>
      <tbody id="user_competence">
      {{if $row.user_type eq '2' && $userShopArray}}
      <tr>
        <th></th>
        <td>
        	<table width="300">
            	{{foreach from=$userShopArray key=key item=item}}
                <tr>
                	<td class="paddingT15">{{$item.shop_name}}</td>
                    <td class="paddingT15 wordSpacing5"><img src="/images/x_small.png" style="cursor:pointer" onClick="shopDel({{$item.shop_id}}, '{{$item.shop_name}}')" id="shop{{$item.shop_id}}" /></td>
                </tr>
                {{/foreach}}                
            </table>
        </td>
      </tr>
      {{elseif $row.user_type eq '3' && $competenceArray}}
		<tr>
        	<th></th>
            <td>
            	<table width="600">
                    <tr>
                        <td width="100" class="paddingT15">{{$shop_name}}</td>
                        <td class="paddingT15 wordSpacing5"></td>
                    </tr>
                    <tr>
                    	<td class="paddingT15">对应权限</td>
                        <td class="paddingT15 wordSpacing5">
                        {{foreach from=$competenceArray key=key item=item}}
                        <input type="checkbox" value="{{$item.value}}" name="competence[]"> {{$item.name}}
                        {{/foreach}}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
      {{/if}}
      </tbody>
      
      <!--
      <tr>
        <th class="paddingT15"> 用户积分</th>
        <td class="paddingT15 wordSpacing5"><input type="text" value="" id="integral" name="integral" class="infoTableInput2">
          <label class="field_notice">暂时留空</label> 
        </td>
      </tr>
      <tr>
        <th class="paddingT15"> 用户等级</th>
        <td class="paddingT15 wordSpacing5">
        <select name="grade" id="grade">
            <option value="">请选择用户等级</option>
        </select>
        <label class="field_notice">暂时留空</label>
        </td>
      </tr>
      <tr>
        <th class="paddingT15"> 用户状态:</th>
        <td class="paddingT15 wordSpacing5">
        <select name="user_status" id="user_status">
            <option value="">请选择用户状态</option>
            <option value = "1" {{if $row.user_status eq '0'}}selected="selected"{{/if}}>正常</option>
            <option value = "2" {{if $row.user_status eq '1'}}selected="selected"{{/if}}>黑名单</option>
            <option value = "3" {{if $row.user_status eq '2'}}selected="selected"{{/if}}>禁言</option>
        </select>
        <label class="field_notice"></label>
        </td>
      </tr>
      <tr{{if $row.user_status neq '2'}} style="display:none"{{/if}}>
      	<th class="paddingT15"></th>
        <td class="paddingT15 wordSpacing5">
        	<input type="radio" name="gag" value="1"  /> 3天
            <input type="radio" name="gag" value="2"  /> 一周
            <input type="radio" name="gag" value="3"  /> 1个月
            <input type="radio" name="gag" value="4"  /> 半年
        </td>
      </tr>-->
      <tr>
        <th></th>
        <td class="ptb20"><input type="submit" value="提交" name="Submit" class="formbtn">
          <input type="reset" value="重置" name="Reset" class="formbtn">        
        </td>
      </tr>
    </tbody></table>
</form>
</div>
<script type="text/javascript">
var competenceJSON = {{$competenceJSON}};
var userJSON = '{{$userJSON}}';
$(function(){
	$('#region_id').val('');
	
	$('#region_id').change(function(){
		if($("#ck:checked").val() == 1) {
			var _this = $('#shop_id');
			_this.attr("disabled", false);
			_this.empty();
			_this.append($("<option>").text('请选择店铺').val(''));
			if(this.value) {
				$.post('/admin/user/get-region-shop', {id:$(this).val()}, function(obj){
					var data = eval('(' + obj + ')');
					$.each(data, function(i, s){
						_this.append($("<option>").text(s.name).val(s.id));
					});
				});	
			} else {
				_this.empty();
				_this.append($("<option>").text('请选择店铺').val(''));
			}
		} else {
			var _this = $('#circle_id');
			_this.empty();
			_this.append($("<option>").text('请选择商圈').val(''));
			$('#shop_id').empty();
			$('#shop_id').append($("<option>").text('请选择店铺').val(''));
			$.post('/admin/good/get-circle', {id:$(this).val()}, function(obj){
				var data = eval('(' + obj + ')');
				$.each(data, function(i, s){
					_this.append($("<option>").text(s.name).val(s.id));
				});
			});
		}
	});
	
	$('#circle_id').change(function(){
		var _this = $('#shop_id');
		var user_type = $('input[name=user_type]:checked').val();
		_this.empty();
		_this.append($("<option>").text('请选择店铺').val(''));
		$.post('/admin/good/get-shop', {region_id:$('#region_id').val(), circle_id:$('#circle_id').val(), user_type:user_type, master:true}, function(obj){
			var data = eval('(' + obj + ')');
			$.each(data, function(i, s){
				_this.append($("<option>").text(s.name).val(s.id));
			});
		});
	});	
	
	$('#shop_id').change(function(){
		var user_type = $('input[name=user_type]:checked').val();
		if(user_type == 3) {
			if($('#utype').val() == 2) {
				$.dialog.alert('<b style="color:red">认证商户</b>不能转变成<b style="color:red">营业员</b>');
				return false;
			}
			var _html = _chtml = '';
			    _html += '<tr><th></th><td>';
				_html += '<table width="600">';
				_html += '<tr><td class="paddingT15" width="100">'+ $(this).find("option:selected").text() +'</td>';
				_html += '<td class="paddingT15 wordSpacing5"></td></tr><tr><td class="paddingT15">对应权限</td><td class="paddingT15 wordSpacing5">';
				$.each(competenceJSON, function(i, v){
					_chtml += ' <input type="checkbox" name="competence[]" value="'+v.value+'" /> ' + v.name;
				});
				_html += _chtml;
				_html += '</td></tr></table></td></tr>';
			$('tbody#user_competence').html(_html);
		} else {
			if(user_type != 2) {
				$('tbody#user_competence').html('');
			}
		}
	});
	
	if(userJSON != '') {
		var userJSONArray = userJSON.split(',');
		$('input[name^=competence]').each(function(index, element) {
			if($.inArray(element.value, userJSONArray) != -1) {
				$(this).attr('checked', true);
			}
		});
	}

	$("#ck").click(function(){
		if(this.checked) {
			$('#circle_id, #shop_id').attr("disabled", true);
		} else {
			$('#circle_id, #shop_id').attr("disabled", false);
		}
	});
});


function shopDel(sid, sname) {
	var uid = $('#uid').val();
	$.dialog({
		title: '提示',
		content: '确定删除店铺： <b>' + sname + '</b>？' ,
		okValue: '确定',
		ok: function () {			
		$.post('/admin/user/del-shop', {uid:uid, sid:sid}, function(data){
			var obj = eval('(' + data + ')');
			if(obj.status == 'ok') {
				$('#shop' + sid).parent().parent().remove();
			}
		});
	},
	cancelValue: '取消',
	cancel : true
	});	
}
</script>
{{include file='admin/footer.php'}}