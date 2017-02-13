{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<div id="rightTop">
  <p>活动管理</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/active/list">活动列表</a></li>
  	<li><span>活动验证</span></li>
  </ul>
</div>
<div class="info">
	<input type="hidden" name="act_id" id="act_id" value="{{$act_id}}" />
    <input type="hidden" name="act_name" id="act_name" value="{{$act_name}}" />
    <table class="infoTable">
      <tr>
        <th class="paddingT15"> 活动名称:</th>
        <td class="paddingT15 wordSpacing5">{{$smarty.request.act_name}}</td>
      </tr>
      <tr>
        <th class="paddingT15"> 中奖类型:</th>
        <td class="paddingT15 wordSpacing5">
        	<input type="radio" name="winning" value="1" {{if $winning eq 1}}checked="checked"{{/if}} /> 50元中奖名单
            <input type="radio" name="winning" value="2" {{if $winning eq 2}}checked="checked"{{/if}}/> 10元中奖名单
        </td>
      </tr>
	  <tr>
        <th class="paddingT15"> 中奖手机号码:</th>
        <td class="paddingT15 wordSpacing5"><input type="text" name="mobile" id="mobile" value="" /></td>
      </tr>
      <tr>
        <th></th>
        <td class="ptb20"><input class="formbtn" type="button" name="Submit" value="查询" onClick="query()"/></td>
      </tr>
    </table>
    
</div>

<script>
	function query() {
		var act_id = $('#act_id').val();
		var act_name = $('#act_name').val();
		var winning = $('input[name=winning]:checked').val();
		var mobile = $('#mobile').val();
		if(!/^1[0-9]{10}$/.test(mobile)) {
			$.dialog.alert('请输入正确的手机号码');
			return false;	
		}
		
		$.post('/admin/active/query', {act_id: act_id, winning:winning, mobile:mobile}, function(obj){
			var data = eval('(' + obj + ')');
			switch(data.res) {
				case 100:
					$.dialog({
						title: '开始兑换',
						content: '要兑换的手机号码为：' + data.extra,
						okValue: '确定',
						ok: function () {			
							$.post('/admin/active/convert', {act_id:act_id, winning:winning, mobile:mobile}, function(data){
								if(data == 'ok'){
									$.dialog({
										title : '结果',
										content : '兑换成功!', 
										ok : function () {location.href = '/admin/active/verify/act_id:' + act_id + '/act_name:' + act_name + '/winning:' + winning;},
										cancel : false
									});
								}
							});
						},
						cancelValue: '取消',
						cancel : true
					});						
					break;
				case 300:
					$.dialog.alert(data.msg);
					break;
			}
		});
	}
</script>
{{include file='admin/footer.php'}}