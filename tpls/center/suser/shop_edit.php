<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{insert name='siteMeta'}}
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<link rel="icon" href="http://www.mplife.com/favicon.ico" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="/css/user/common.css?t={{$_CONF.WEB_VERSION}}"/>
<link rel="stylesheet" type="text/css" href="/css/user/20130627.css?t={{$_CONF.WEB_VERSION}}"/>
<link rel="stylesheet" type="text/css" href="/css/user/backstage.css?t={{$_CONF.WEB_VERSION}}"/>
<link rel="stylesheet" type="text/css" href="/js/autocomplete/autocomplete.css?t={{$_CONF.WEB_VERSION}}"  />
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/idialog.css?t={{$_CONF.WEB_VERSION}}"/>
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/autocomplete/autocomplete.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8"></script>
<script type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<script type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
<!--<script src="http://www.mplife.com/tools/cmslog/log.js"></script>-->
</head>
<body>
    <!--site-->
	{{include file='center/site.php'}}
    <!--site-end-->
    <!--外围-->
    <div class="w1187 clearfix">
        
    <!--左侧-->
	{{include file='center/left.php'}}
    <!--左侧end-->
    <!--右侧-->
    <div class="right-box">
			<div class="tab-title">
                <ul>
                    {{if $user.user_type eq 2}}
                        {{if $_CONF._A eq 'shop-edit'}}
                            <li class="sel" ><a href="javascript:void(0)" >编辑店铺</a></li>
                        {{else}}
                            <li><a href="/home/suser/shop-edit/sid/{{$sid}}" >编辑店铺</a></li>
                        {{/if}}
                    {{elseif $user.user_type eq 3}}
                        {{if in_array(5,$userPermission)}}
                            {{if $_CONF._A eq 'shop-edit'}}
                                <li class="sel" ><a href="javascript:void(0)" >编辑店铺</a></li>
                            {{else}}
                                <li><a href="/home/suser/shop-edit/sid/{{$sid}}" >编辑店铺</a></li>
                            {{/if}}
                        {{/if}}       
                    {{/if}}
                    
                    {{if $user.user_type eq 2 && $shopRow.is_flag eq 1}}
                        {{if $_CONF._A eq 'shop-decoration'}}
                            <li class="sel" ><a href="javascript:void(0)" >店铺推荐</a></li>
                        {{elseif $_CONF._A eq 'shop-decoration-add'}}
                            <li class="sel" ><a href="javascript:void(0)" >店铺推荐</a></li>
                        {{else}}
                        <li><a href="/home/suser/shop-decoration/sid/{{$sid}}">店铺推荐</a></li>
                        {{/if}}	
                    {{/if}}                    
                </ul>
            </div>
            <form action="{{$_CONF.ACTION_FORM}}" method="post">
            <input type="hidden" name="shop_id" value="{{$sid}}" />
            <div class="upFileBox">
                	<!--<p class="inputBox"><label class="label">店铺名称：</label><input type="text" name="sname" id="sname" class="text backTitInput" value="{{$shopInfo.shop_name}}"></p>
                    <p class="error"><span id="sname_error"></span></p>
                    <p class="selectBox clearfix">
					    <label class="label">店铺地址：</label>
                        <select name="region_id" id="region_id">
                            <option value="">请选择所在区</option>
                            {{foreach from=$regionArray key=key item=item}}
                            <option value="{{$key}}">{{$item}}</option>
                            {{/foreach}}
                        </select>	
                        <select name="circle_id" id="circle_id">
                        <option value="">请选择商圈</option>
                            {{foreach from=$circleArray key=key item=item}}
                            <option value="{{$item.id}}">{{$item.name}}</option>
                            {{/foreach}}
                    	</select>	
                    </p>
                    <p class="error"><span class="h2" id="region_id_error"></span><span class="h2" id="circle_id_error"></span></p>
                    <p class="inputBox"><input type="text" class="text mInput" name="address" id="address" value="{{$shopInfo.shop_address}}" placeholder="请填写详细地址，如XX路XX号" /></p>
                    <p class="error"><span id="address_error"></span></p>
                    <p class="inputBox"><label class="label">所属品牌：</label><input type="text" class="text backTitInput" name="bname" id="bname" value="{{$shopInfo.brand_name}}" placeholder="请输入品牌名称" /></p>
                    <p class="error"><span id="bname_error"></span></p>
                     <p class="selectBox clearfix">
                    	<label class="label">所属分类：</label>
                        <select name="store_id" id="store_id">
                            <option value="">请选择分类</option>
                            {{foreach from=$storeArray key=key item=item}}
                            <option value="{{$key}}" {{if $key eq $shopInfo.store_id}} selected="selected"{{/if}}>{{$item}}</option>
                            {{/foreach}}
                        </select>
                   </p>
                   <p class="error"><span class="h1" id="store_id_error"></span></p>-->
                    <div class="textareabox clearfix">
                        <label>店铺公告：</label>
                        <div class="textarea">
                            <textarea class="noticeTextarea" name="notice" id="notice" placeholder="可发布优惠信息等公告内容，100字内">{{$shopInfo.notice}}</textarea>
                        </div>
                    </div>
                     <p class="error"><span class="h1" id="notice_error"></span></p>
   					 <p class="error"><span class="h1" id="addShopError"></span></p>
                     <p class="submitBox" ><input type="button"  class="submit" value="提交修改" onClick="submitTure()"  id="editShopSubmit"></p>
            </div>
            </form>
    </div>
    </div>
    <!--外围end-->
    <!--底部-->
{{include file='center/footer.php'}}
<script type="text/javascript">
var isBrandChecked = 0;
/*$(function(){	
	$('#bname').autocomplete({ url:"/home/suser/get-brand", onItemSelect:
        function (item) {
            var text = item.value; //文本
            var num = item.data; //数字
            $('#bname').val(text);
        }
        ,cellSeparator:"|"
	});
	$('#sname').on('blur', function(){
		snameValid();
	});
	$('#region_id').on('blur', function(){
		regionIdValid();
	});
	$('#circle_id').on('blur', function(){
		circleIdValid();
	});
	$('#address').on('blur', function(){
		addressValid();
	});
	$('#bname').on('blur', function(){
		bnameValid();
	});
	$('#store_id').on('blur', function(){
		storeValid();
	});
	$('#notice').on('blur', function(){
		noticeValid();
	});
	
	$('#region_id').bind('change', function(){
		var thatFun = arguments.callee;
		var that=this; 
		$(this).unbind("change",thatFun);
		
		var _this = $('#circle_id');
		_this.empty();
		_this.append($("<option>").text('请选择商圈').val(''));
		$.post('/home/good/get-circle', {region_id:$(this).val()}, function(obj){
			var data = eval('(' + obj + ')');
			$.each(data, function(i, s){
				_this.append($("<option>").text(s.name).val(s.id));
			});
		});
		
		setTimeout(function(){$(that).bind("change", thatFun)},0);
	});
});*/

function submitTure() {
	var /*sname_status = region_status = circle_status = address_status = bname_status = store_status = */notice_status = false;
/*	sname_status = snameValid();
	region_status = regionIdValid();
	circle_status = circleIdValid();
	address_status = addressValid();
	bname_status = bnameValid();
	store_status = storeValid();*/
	notice_status = noticeValid();
	if(/*sname_status && region_status && circle_status && address_status && bname_status && store_status &&*/ notice_status) {
		$("#editShopSubmit").attr('value', '提交中。。。').attr('disabled', true);
 		$.ajax({
			url:'/home/suser/shop-edit', 
			type:'POST',
			dataType:'json',
			data: {
				shop_id : {{$sid}},
/*				sname : $('#sname').val(),
				rid : $('#region_id').val(),
				cid : $('#circle_id').val(),
				ad : $('#address').val(),
				bname : $('#bname').val(),
				stid : $('#store_id').val(),*/
				not : $('#notice').val()
			}, 
			success: function(json){
				if(json.res == 100) {
					$.dialog({
						title: '提示',
						content: json.msg,
						ok: function () {
							location.href = '/home/suser/shop-edit/sid/' + json.extra.sid;
						},
						cancel:false
					});
				} else if(json.res == 300) {
					$('#addShopError').html(json.msg);
				}
			},
			error: function(){
			}
		});
	} else {
		setTimeout('submitTure()',1000);
	}
}

function snameValid() {
	var _this = $('#sname');
	var _msg = '';
	if(_this.val().length == 0) {
		_msg = '请输入店铺名称';
	} else if(_this.val().length > 30) {
		_msg = '店铺名称最多30个字符，汉字算一个字符';
	}
	
	if(_msg == '') {
		$('#sname_error').html('<s class="true"></s>');
	} else {
		$('#sname_error').html('<s class="false"></s>' + _msg);
		return false;
	}
	return true;	
}

function regionIdValid() {
	var _this = $('#region_id');
	if(_this.length > 0) {
		if(_this.val().length == 0) {
			$('#region_id_error').html('<s class="false"></s>请选择所在区');
			return false;
		} else {
			$('#region_id_error').html('<s class="true"></s>');
		}
	}
	return true;
}

function circleIdValid() {
	var _this = $('#circle_id');
	if(_this.length > 0) {
		if(_this.val().length == 0) {
			$('#circle_id_error').html('<s class="false"></s>请选择商圈');
			return false;
		} else {
			$('#circle_id_error').html('<s class="true"></s>');
		}
	}
	return true;
}

function addressValid() {
	var _this = $('#address');
	if(_this.length > 0) {
		if(_this.val().length == 0) {
			$('#address_error').html('<s class="false"></s>请输入店铺名称');
			return false;
		} else {
			$('#address_error').html('<s class="true"></s>');
		}
	}
	return true;		
}

function bnameValid() {
	if(isBrandChecked == 1){
		return true;
	}
	var _this = $('#bname');
	if(_this.val().length == 0) {
		$('#bname_error').html('<s class="false"></s>请输入品牌名称');
		return false;
	} else {
		$.post('/home/suser/check-brand-name', {bname:_this.val()}, function(flag){
			if(flag == 'false') {
				$('#bname_error').html('<s class="false"></s>你输入的品牌不存在');
				return false;
			} else {
				$('#bname_error').html('<s class="true"></s>');
				isBrandChecked = 1;
				return true;
			}
		});	
	}
}

function storeValid() {
	var _this = $('#store_id');
	if(_this.val().length == 0) {
		$('#store_id_error').html('<s class="false"></s>请选择所属分类');
		return false;
	} else {
		$('#store_id_error').html('<s class="true"></s>');
	}
	return true;	
}

function noticeValid() {
	var _this = $('#notice');
	var _msg = '';
	
	if(_this.val().length > 0) {
		if(_this.val().length > 100) {
			_msg = '店铺公告最多100个字符，汉字算一个字符';
		}
	
		if(_msg == '') {
			$('#notice_error').html('<s class="true"></s>');
		} else {
			$('#notice_error').html('<s class="false"></s>' + _msg);
			return false;
		}
	}
	return true;
}

function loadRegion() {
	var _this = $('#region_id');
	_this.append($("<option>").text('请选择所在区').val(''));
	$.post('/home/good/get-region', {}, function(obj){
		var data = eval('(' + obj + ')');
		$.each(data, function(i, s){
			_this.append($("<option>").text(s.name).val(s.id));
		});
		$('#region_id').val({{$shopInfo.region_id}});
		$('#circle_id').val({{$shopInfo.circle_id}});
	});
}
//loadRegion();
</script>