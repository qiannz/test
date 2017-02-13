<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{insert name='siteMeta'}}
<link href="/css/reset.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<link href="/css/common.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<link href="/css/index.css?t={{$_CONF.WEB_VERSION}}" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="http://www.mplife.com/favicon.ico" type="image/x-icon" />
<script type="text/javascript" src="/js/jquery.js?t={{$_CONF.WEB_VERSION}}"></script>
<script type="text/javascript" src="/js/index.js?t={{$_CONF.WEB_VERSION}}"></script>
<script src="http://www.mplife.com/tools/cmslog/log.js"></script>
</head>
<body>
	  <div class="w1210">
      <!--top-->
      {{include file='top.php'}}
      </div>
      <!--nav-->
      {{include file='nav.php'}}
      <div class="w1210">
      <!--商户入驻-->
      {{if $merchantNum gt 1}}
        <div id="popupShop">
            <div class="popupBox">
                    <h3 class="popupTit">选择店铺<a class="popupclose" onClick="document.getElementById('popupShop').style.display = 'none'">&times;</a></h3>
                    <div class="popupShopTxt">店铺列表</div>
                    <div class="popupShopCol">
                            <select name="sid" id="sid" class="shop-enter-select">
                              <option value="">请选择店铺</option>
                              {{foreach from=$merchantArray key=key item=item}}
                              <option value="{{$item.shop_id}}">{{$item.shop_name}}</option>
                              {{/foreach}}
                            </select>
                    </div>
                    <div class="popupShopCol">
                            <a class="confirmBtn" href="javascript:joinJumpTo()">确定</a>
                    </div>
            </div>
            <div class="shade"></div>
        </div> 
        <script>
        	function joinJumpTo() {
				if($('#sid').val().length == 0) {
					alert('请选择一个店铺');
				} else {
					window.location.href = '/home/member/join/sid/' + $('#sid').val();
				}
			}
        </script>     
      {{else}}
          {{if $step eq 0}}
            {{include file='member/step_one.php}}
          {{elseif $step eq 1}}
            {{include file='member/step_two.php}}
          {{elseif $step eq 2 || $step eq 3 || $step eq 4}}
            {{include file='member/step_three.php}}
          {{elseif $step eq 5}}
            {{include file='member/step_four.php}}
          {{elseif $step eq 6}}
            {{include file='member/step_five.php}}
          {{elseif $step eq 7}}
            {{include file='member/step_six.php}}
          {{/if}}
	  {{/if}}
     <!--关于超级购-->
{{include file='bottom.php'}}
</div>
<script type="text/javascript">
$(function(){
    FnHover('allBtn','allBox');
})
</script>
{{include file='footer.php'}}