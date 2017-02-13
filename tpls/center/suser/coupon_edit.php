<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{insert name='siteMeta'}}
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
<link rel="icon" href="http://www.mplife.com/favicon.ico" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="/css/user/common.css?t={{$_CONF.WEB_VERSION}}"/>
<link rel="stylesheet" type="text/css" href="/css/user/backstage.css?t={{$_CONF.WEB_VERSION}}"/>
<link rel="stylesheet" type="text/css" href="/css/user/20130627.css?t={{$_CONF.WEB_VERSION}}"/>
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}" charset="utf-8" ></script>
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
                {{if $_CONF._A eq 'coupon-list'}}
                    <li class="sel" ><a href="javascript:void(0)" >券管理</a></li>
                {{else}}
                    <li><a href="/home/suser/coupon-list/sid/{{$sid}}">券管理</a></li>
                {{/if}}
                
                {{if $_CONF._A eq 'add-coupon'}}
                    <li class="sel" ><a href="javascript:void(0)" >发券</a></li>
                {{else}}
                    <li><a href="/home/suser/add-coupon/sid/{{$sid}}">发券</a></li>
                {{/if}}
            {{/if}}
            
            {{if $user.user_type eq 2}}
                {{if $_CONF._A eq 'valid'}}
                <li class="sel" ><a href="javascript:void(0)" >券验证</a></li>
                {{else}}
                <li><a href="/home/suser/valid/sid/{{$sid}}">券验证</a></li>
                {{/if}}
            {{elseif $user.user_type eq 3}}
                {{if in_array(4,$userPermission)}}
                    {{if $_CONF._A eq 'valid'}}
                    <li class="sel" ><a href="javascript:void(0)" >券验证</a></li>
                    {{else}}
                    <li><a href="/home/suser/valid/sid/{{$sid}}">券验证</a></li>
                    {{/if}}
                {{/if}}
            {{/if}}
            
            {{if $_CONF._A eq 'coupon-edit'}}
            	<li class="sel" ><a href="javascript:void(0)" >券编辑</a></li>
            {{/if}}                     
            </ul>
        </div> 
        
        <form action="{{$_CONF.FORM_ACTION}}/tid/{{$tid}}/sid/{{$sid}}" method="post" id="editCouponForm" enctype="multipart/form-data">
        <input type="hidden" name="gids" id="gids" value="{{$ticketRow.gids}}" />
        <input type="hidden" name="sid" id="sid" value="{{$sid}}" />
        <input type="hidden" name="tid" id="sid" value="{{$tid}}" />
        <div class="upFileBox">
            <p class="inputBox">
            <label class="label">总数量：</label>
            <input type="text" class="text priceInput" name="total" id="total" value="{{$ticketRow.total}}" placeholder="请输入券数量" /><font>*</font>
            </p>
            <p class="error">
            <span id="total_error"></span>
            </p>
            
            <div class="uploadpic">
                <label class="label">封面图片：</label>
                <div class="upfile-img-cover">
                    <input type="file" class="file" name="file_img" id="file_img" onchange="$('#loadNumFileImg>em').text($(this).val())">
                </div>
                <font>*</font>                 
                <span id="loadNumFileImg" class="exp"><em></em></span>
                <span class="exp">图片尺寸 640 * 300 </span>
            </div>
            {{if $ticketRow.cover_img}}
            <p class="inputBox"><label class="label"></label><img src="{{$_CONF.IMG_URL}}/cover/{{$ticketRow.cover_img}}" /></p>
            {{/if}}
            
            <p class="error"></p>    
            <p class="inputBox"><label class="label">适用商品：</label><a class="setShop" id="setShop">点击设置适用商品</a></p>
            <p class="choiceBox" id="choiceBox"></p>
            <p class="submitBox" ><input type="button"  class="submit" value="编辑券" onClick="submit_check()" id="submitCheck"></p>
        </div>
        </form>
    </div>
</div>

<div class="Popup" style="display:none"  id="selectPopup">
    <div class="selectPopup">
            <h2>设置优惠券适用商品<a class="close">&times;</a></h2>
            <div class="select-con">
                <p class="selectAll"><input type="checkbox" class="check" id="selectAll" checked="false"><label>选择所有</label></p>
                <div class="selectTable">
                </div>
                <p class="center" id="selectBox"><input type="submit" value="提交设置" class="selectBtn"/></p>
            </div>
    </div>
   <div class="shade"></div>
</div>
<!--外围end-->
<!--底部-->  
{{include file='center/footer.php'}}  
<script type="text/javascript" src="/js/setshop.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript">
var validArray = ['total'];
var setGood;
$(function(){
	setGood = new SelectShop();
	for(var i=0; i<validArray.length; i++) {
		$('#' + validArray[i]).bind('blur',function(){
			validInput($(this).attr("id"));
		});
	}
});

function submit_check() {
	var len = 0;
	$('#gids').val(setGood.getCache().join(','));
	for(var i=0; i<validArray.length; i++) {
		if(validInput(validArray[i])) {
			len++;
		}
	}
	if(len == validArray.length) {
		$("form#editCouponForm").submit();
		$("form#editCouponForm .submit").attr('value', '提交中...').attr('disabled', true);		
	}
	return false;
}

function validInput(id) {
	var _msg = '';
	switch(id) {
		case 'total':
				if($('#' + id).val().length == 0) {
					_msg = '请输入券数量';	
				}
				
				if(!/[1-9][0-9]*$/.test($('#' + id).val())) {
					_msg = '券数量为正整数';
				}			
			break;
	}
	
	if(_msg == '') {
		$('#' + id + '_error').html('<s class="true"></s>');
	} else {
		$('#' + id + '_error').html('<s class="false"></s>' + _msg);
		return false;
	}
	return true;	
	
}
</script> 
</body>
</html>