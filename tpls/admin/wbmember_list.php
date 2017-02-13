{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/DatePicker/WdatePicker.js" ></script>
<div id="rightTop">
  <p>微商管理</p>
  <ul class="subnav">
    <li><span>会员管理</span></li>
<!--     <li><a class="btn1" href="/admin/wbmember/add">新建会员</a></li> -->
  </ul>
</div>
<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="{{$_CONF.FORM_ACTION}}">
       <div class="left">
                                   用户姓名：
            <input class="queryInput" type="text" name="realname" value="{{$request.realname}}" />
                                 手机号码：
            <input class="queryInput" type="text" name="mobile" value="{{$request.mobile}}" />
                                  用户类型：
            <select name="ut" class="querySelect">
            	<option value="">全部</option>
                <option value="1" {{if $request.ut eq 1}}selected="selected"{{/if}}>微商</option>
                <option value="2" {{if $request.ut eq 2}}selected="selected"{{/if}}>代购</option>
                <option value="3" {{if $request.ut eq 3}}selected="selected"{{/if}}>切货</option>
                <option value="4" {{if $request.ut eq 4}}selected="selected"{{/if}}>游客VIP</option>
            </select>
                                审核状态：
			<select name="st" class="querySelect">
            	<option value="">全部</option>
                <option value="1" {{if $request.st eq 1}}selected="selected"{{/if}}>未审核</option>
                <option value="2" {{if $request.st eq 2}}selected="selected"{{/if}}>审核通过</option>
                <option value="3" {{if $request.st eq 3}}selected="selected"{{/if}}>审核拒绝</option>
            </select>
            <input type="submit" class="formbtn" value="查询" />
      </div>
      <a class="left formbtn1" href="/admin/wbmember/list">撤销检索</a>
    </form>
  </div>
<div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>

<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    <tr class="tatr1">
      <td>ID</td>
      <td width="15%">手机号</td>
      <td>姓名</td>
      <td>申请类型</td>
	  <td>申请说明</td>
	  <td>申请时间</td>
	  <td>审核状态</td>
      <td width="250">操作</td>
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
      <td width="250">
      		<a href="/admin/wbmember/edit/uid:{{$item.user_id}}/page:{{$page}}">编辑</a> |
            {{if $item.user_status eq 0}}
            <a href="javascript:audit({{$item.user_id}},{{$item.user_type}})">审核</a> |
            {{/if}}
      		<a href="javascript:jumpToLog('wbmember', '{{$item.user_id}}')">记录</a>
      </td>
    </tr>
   {{foreachelse}}
    <tr class="no_data">
      <td colspan="9">暂无数据</td>
    </tr>
    {{/foreach}}
  </table>
  <div id="dataFuncs">
    <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
    <div class="clear"></div>
  </div>
</div>
<script type="text/javascript">
function audit( user_id , user_type ){
	var content = '用户类型：<select id="user_type">'
					+'<option '+(user_type==1?'checked="checked"':'')+' value="1">微商</option>'
					+'<option '+(user_type==2?'checked="checked"':'')+' value="2">代购</option>'
					+'<option '+(user_type==3?'checked="checked"':'')+' value="3">切货</option>'
					+'<option '+(user_type==4?'checked="checked"':'')+' value="4">游客VIP</option>'
					+'</select>';
	
	$.dialog({
		title:'警告',
		content: content,
		button : [
					{
						value : '审核通过',
						callback:function(){
							$.ajax({
				                type:'POST',
				                url:'/admin/wbmember/audit',
				                data:{uid:user_id,user_type:$("#user_type").val(),user_status:1},
				                dataType:'json',
				                success:function(data){
				                    if( 1== data ){
					                    alert("审核通过成功");
					                    window.location.reload();
					                }else{
										alert("审核通过失败");
							        }
				                }
				            });				
						}
					},
					{
						value : '审核拒绝',
						callback:function(){
							$.ajax({
				                type:'POST',
				                url:'/admin/wbmember/audit',
				                data:{uid:user_id,user_type:$("#user_type").val(),user_status:-1},
				                dataType:'json',
				                success:function(data){
				                    if( 1== data ){
					                    alert("审核拒绝成功");
					                    window.location.reload();
					                }else{
										alert("审核拒绝失败");
							        }
				                }
				            });
						}
					},
					{
						value: '关闭'
					}
				]
	});
}
</script>
{{include file='admin/footer.php'}}