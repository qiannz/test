{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/inline_edit.js"></script>
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css" />
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js"></script>
<div id="rightTop">
  <p>套餐设置</p>
  <ul class="subnav">
    <li><span>套餐列表</span></li>
    <li><a class="btn1" href="/admin/pack/add">新增套餐</a></li>
  </ul>
</div>

<div class="mrightTop">
  <div class="fontl">
    <form method="post" action="{{$_CONF.FORM_ACTION}}">
    <input type="hidden" name="act_id" value="{{$act_id}}" />
       <div class="left">
          套餐名：
           <input class="queryInput" type="text" name="pack_name" value="{{$smarty.request.pack_name}}" />
          <input type="submit" class="formbtn" value="查询" />
      </div>
      
      <a class="left formbtn1" href="/admin/pack/list">撤销检索</a>
      
    </form>
  </div>
  <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
  <input type="hidden" name="page" id="page" value="{{$page}}" />
  <table width="100%" cellspacing="0" class="dataTable">
    {{if $packList}}
    <tr class="tatr1">
      <td width="20" class="firstCell"></td>
      <td>套餐名称</td>
      <td>套餐标识</td>
      <td>券说明</td>
      <td>商品数量限制</td>
      <td>券限制数量</td>
      <td>排序</td>
      <td>操作</td>
    </tr>
    {{/if}}
    {{foreach from=$packList item=item}}
    <tr class="tatr2">
      <td class="firstCell"><input type="radio" name="packId" id="packId" value="{{$item.pack_id}}"  {{if $item.is_default eq 1}} checked="checked" {{/if}}/></td> 
      <td>{{$item.pack_name}}</td>
      <td>{{$item.pack_logo}}</td>
      <td>{{$item.pack_explan}}</td>
      <td>{{$item.good_num}}</td>
      <td>{{$item.ticket_num}}</td>
      <td><span ectype="inline_edit" fieldname="sequence" fieldid="{{$item.pack_id}}" required="1" class="node_name editable">{{$item.sequence}}</span></td>
      <td>      	
      	<a href="/admin/pack/edit/pack_id:{{$item.pack_id}}">编辑</a> | 
      	<a href="javascript:drop_confirm('你确定要删除它吗？', '/admin/pack/del/pack_id:{{$item.pack_id}}/pack_name:{{$item.pack_name}}');">删除</a> 
      </td>  
    </tr>
    {{foreachelse}}
    <tr class="no_data">
      <td colspan="6">暂无参与人员</td>
    </tr>
  {{/foreach}}
  </table>
  <div id="batchAction" class="left paddingT15"> &nbsp;&nbsp;
  <input class="formbtn1" type="button" value="设置默认" name="set_default" id="set_default" uri="set_default"  />
  </div>
  <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
</div>
<script type="text/javascript">
$(".dataTable tr.tatr2").mouseover(function(){  
	$(this).addClass("over");
})

$(".dataTable tr.tatr2").mouseout(function(){
	$(this).removeClass("over");
})

	//设置禁言
$('#set_default').click(function(){
	setDefault();
});

function setDefault()
{
	if($('input[name="packId"]:checked').length == 0){
		alert('请选择套餐');
		return false;
	}
	var page = $('#page').val();
	var packId = $('input[name="packId"]:checked').val();
	$.dialog({
		title: '设置默认套餐',
		content: '确定设置该套餐为默认套餐？',
		okValue: '确定',
		ok: function () {			
			$.post('/admin/pack/set-default', {packId:packId}, function(data){
				if(data == 'ok'){
					$.dialog({
						title : '结果',
						content : '设置成功!', 
						ok : function () {location.href = '/admin/pack/list/page:' + page;},
						cancel : false
					});
				}
			});
		},
		cancelValue: '取消',
		cancel : true
	});	
		
}



</script>
{{include file='admin/footer.php'}}