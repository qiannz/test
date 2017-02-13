{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<div id="rightTop">
  <p>任务管理</p>
  <ul class="subnav">
    <li><span>APP大转盘摇奖记录</span></li>
  </ul>
</div>

<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="{{$_CONF.FORM_ACTION}}">
       <div class="left">
      中奖类型：
			<select name="wheel_type" class="querySelect">
            	<option value="">全部</option>
                <option value="star" {{if $request.wheel_type eq star}}selected="selected"{{/if}}>幸运星</option>
                <option value="virtual" {{if $request.wheel_type eq virtual}}selected="selected"{{/if}}>券</option>
                <option value="real" {{if $request.wheel_type eq real}}selected="selected"{{/if}}>实物</option>
                <option value="call" {{if $request.wheel_type eq call}}selected="selected"{{/if}}>5元话费</option>
            </select>
       	  手机号码：
          <input class="queryInput" type="text" name="mobile" value="{{$request.mobile}}" />
          发放状态：
          <select name="is_valid" class="querySelect">
            	<option value="">全部</option>
                <option value="1" {{if $request.is_valid eq 1}}selected="selected"{{/if}}>未发放</option>
                <option value="2" {{if $request.is_valid eq 2}}selected="selected"{{/if}}>已发放</option>
          </select>
          奖品名称：
           <input class="queryInput" type="text" name="award_name" value="{{$request.award_name}}" />
          用户名：
           <input class="queryInput" type="text" name="user_name" value="{{$request.user_name}}" />       
          <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/appwheel/list">撤销检索</a>
    </form>
  </div>
  <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    {{if $appWheels}}
    <tr class="tatr1">
      <td width="20" class="firstCell"></td>
      <td>中奖类型</td>
      <td>用户名</td>
      <td>奖品名称</td>
      <td>数量</td>
      <td>手机号码</td>
      <td>中奖日期</td>
      <td></td>
    </tr>
    {{/if}}
    {{foreach from=$appWheels item=item}}
    <tr class="tatr2">
      <td class="firstCell"></td>
      <td>
      {{if $item.type eq star}}幸运星{{/if}}
      {{if $item.type eq virtual}}券{{/if}}
      {{if $item.type eq real}}实物{{/if}}
      {{if $item.type eq call}}5元话费{{/if}}
      </td>
      <td>{{$item.user_name}}{{if $item.is_valid eq 1}} <span style="color:red">[已发]</span>{{/if}}</td>
      <td>{{$item.award_name}}</td>
      <td>{{$item.number}}</td>
      <td>{{$item.mobile}}</td>
      <td>{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
 	  <td>
      {{if $item.is_valid eq 0}}
     	 <a href="javascript:valid({{$item.id}})" id="wheel_valid_{{$item.id}}">验证</a>
      {{/if}}
      </td>
    </tr>
    {{foreachelse}}
    <tr class="no_data">
      <td colspan="6">暂无品牌记录</td>
    </tr>
  {{/foreach}}
  </table>
  <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
</div>
<script type="text/javascript">
function valid(id) {
	var page = $("#page").val();
	$.dialog({
		title: '验证确认',
		content: '确定验证？' ,
		okValue: '确定',
		ok: function () {			
		$.post('/admin/appwheel/valid', {id:id}, function(data){
			if(data == 'ok') {
				$("#wheel_valid_" + id).remove();
			}
		});
	},
	cancelValue: '取消',
	cancel : true
	});		
}
</script>
{{include file='admin/footer.php'}}