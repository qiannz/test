{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css" />
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js"></script>
<div id="rightTop">
  <p>用户管理</p>
  <ul class="subnav">
  	<li><a class="btn4" href="/admin/user/list/page:{{$page}}">用户列表</a></li>
    <li><span>店铺权限</span></li>
    <li><span>{{$uname}}</span></li>
  </ul>
</div>

<div class="tdare">
<input type="hidden" name="page" id="page" value="{{$page}}" />
<input type="hidden" name="uname" id="uname" value="{{$uname}}" />

<table width="1000" cellspacing="0" class="dataTable">
<tr>
	<td width="800" valign="top">
      <table width="100%" cellspacing="0" class="dataTable">
        <tr class="tatr1">
          <td>店铺名称</td>
          <td width="50%">权限</td>
          <td>操作</td>
        </tr>
        <!--{{foreach from=$userRelationShopArray key=key item=item}}-->
        <tr class="tatr2">
          <td>{{$item.shop_name}}</td>
          <td>{{$item.purview}}</td>
          <td>
          <span style="width: 100px">
             <a href="javascript:setUp({{$uid}}, {{$item.shop_id}}, '{{$item.competence}}')">设置</a>
          </span>
          </td>
        </tr>
        <!--{{foreachelse}}-->
        <tr class="no_data">
          <td colspan="3">暂无店铺记录</td>
        </tr>
        <!--{{/foreach}}-->
      </table>    
    </td>
    <td width="50"></td>
    <td valign="top" id="purview">
    	
    </td>
</tr>
</table>
<script type="text/javascript">
var competenceJSON = {{$competenceJSON}};

function setUp(uid, sid, userJSON) {	
	var userJSONArray = new Array();	
	if(!!userJSON) {
		userJSONArray = userJSON.split(',');
	}
	
	var uname = $('#uname').val();
	var page = $('#page').val();
	var _html = _chtml = '';
		_html += '<table width="200" cellspacing="0" class="dataTable"><tr><th width="20" class="firstCell"><input type="checkbox" class="checkall" onclick="selected()" /></th><th>对应权限</th></tr>';
		$.each(competenceJSON, function(i, v){
			_chtml += '<tr><td class="firstCell"><input type="checkbox" name="competence[]" class="checkitem" value="'+v.value+'"';			
			if($.inArray(String(v.value), userJSONArray) != -1) {
				_chtml += ' checked="checked"';
			}
			_chtml += ' /></td><td>' + v.name + '</td></tr>'
		});		
		_html += _chtml;
		_html += '<tr><td></td><td><input type="botton" value="确定" name="Submit" class="formbtn1" onclick="grant(' + uid + ', ' + sid + ', \'' +  uname + '\', ' + page + ')"></td></tr>';
		_html += '</table>';
	$('#purview').html(_html);	
}

function selected() {
	$('.checkitem').each(function(index, element) {
		if($(this).is(':checked')){
			$(this).attr('checked', false);
		}
		else{
			$(this).attr('checked', true);
		}
	});
}

function grant(uid, sid, uname, page) {
	var purviewStr = '';
	$('input[name^=competence]').each(function(index, element) {
			if(this.checked) {
				purviewStr += element.value + ','
			}
		});
		
	if(purviewStr != '') {
		purviewStr = purviewStr.substring(0,purviewStr.length -1);
	}
	
	$.post("/admin/user/user-purview-save" , {uid:uid, sid:sid, purviewStr:purviewStr}, function(data){
		if(data == 'ok') {
			window.location = '/admin/user/user-purview/uid:' + uid + '/uname:' +  uname + '/page:' + page
		}
	});

}
</script>
</div>
{{include file='admin/footer.php'}}