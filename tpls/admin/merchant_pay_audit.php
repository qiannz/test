{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/jquery.validate.min.js" ></script>
<div id="rightTop">
  <p>店铺管理</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/merchant/list">商户入驻</a></li>
    <li><span>商户入驻资料是否齐全</span></li>
  </ul>
</div>

<div class="info">
<form method="post" >
<input type="hidden" name="uid" id="uid" value="{{$row.user_id}}" >
<input type="hidden" name="uname" id="uname" value="{{$row.user_name}}" >
<input type="hidden" name="shop_id" id="shop_id" value="{{$row.shop_id}}" >
<table class="infoTable">
	<tr>
        <th class="paddingT15">所属分类：</th>
        <td class="paddingT15 wordSpacing5">ID:{{$row.store_id}} 分类名称:{{$row.store_name}}</td>
    </tr>
    	<tr>
        <th class="paddingT15">主营品牌：</th>
        <td class="paddingT15 wordSpacing5">{{$row.brand_name}}</td>
    </tr>
    <tr>
        <th class="paddingT15">品牌授权书：</th>
        <td class="paddingT15 wordSpacing5"><img src="{{$_CONF.SITE_URL}}/data/verify/{{$row.brand_img}}" class="makesmall" max_width="800" max_height="600" /></td>
    </tr>
	<tr>
        <th class="paddingT15">套餐类型：</th>
        <td class="paddingT15 wordSpacing5">{{$row.pack_name}}</td>
    </tr>
    <tr>
        <th class="paddingT15">结算账户：</th>
        <td class="paddingT15 wordSpacing5">{{$row.alipay_acount}}</td>
    </tr>
    <tr>
        <th class="paddingT15">账户户主姓名：</th>
        <td class="paddingT15 wordSpacing5">{{$row.alipay_name}}</td>
    </tr>
    <tr>
        <th></th>
        <td class="ptb20"><input class="formbtn" type="button" id="sbmit" value="确定" />
    </tr>
</table>
</form>
</div>
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css" />
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js"></script>
<script type="text/javascript">
$('#sbmit').click(function(){
	setSubmit();
});

function setSubmit()
{
	var uid = $('#uid').val();
	var pack_id = $('#pack_id').val();
	var uname = $('#uname').val();
	var shop_id = $('#shop_id').val();
	$.dialog({
		title: '商户入驻设置',
		content: '确定该商户入驻成功？',
		okValue: '确定',
		ok: function () {			
			$.post('/admin/merchant/pay', {uid:uid, pack_id:pack_id, uname:uname, shop_id:shop_id}, function(data){
				if(data == 'ok'){
					$.dialog({
						title : '结果',
						content : '设置成功!', 
						ok : function () {location.href = '/admin/merchant/list'},
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