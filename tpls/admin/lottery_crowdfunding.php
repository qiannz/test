{{include file='admin/header.php'}}
<div id="rightTop">
  <p>券管理</p>
  <ul class="subnav">
    <li><a class="btn1" href="/admin/crowdfunding/list/page:{{$page}}">一元众筹</a></li>
    <li><span>抽奖</span></li>
  </ul>
</div>
<div class="info">
<form method="POST" action="{{$_CONF.FORM_ACTION}}" id="form">
<input type="hidden" name="tid" id="tid" value="{{$row.ticket_id}}" />
<input type="hidden" name="page" id="page" value="{{$page}}" />
<table class="infoTable">
	{{if $row.lottery_code}}
    <tr>
        <th class="paddingT15">中奖号码:</th>
        <td class="paddingT15 wordSpacing5">{{$row.lottery_code}}</td>
    </tr>
    <tr>
        <th class="paddingT15">中奖用户名:</th>
        <td class="paddingT15 wordSpacing5">{{$row.userInfo.user_name}}</td>
    </tr>  
    <tr>
        <th class="paddingT15">中奖手机号码:</th>
        <td class="paddingT15 wordSpacing5">{{$row.lottery_mobile}}</td>
    </tr> 
    <tr>
        <th class="paddingT15">中奖订单号:</th>
        <td class="paddingT15 wordSpacing5">{{$row.lottery_order_no}}</td>
    </tr>
    {{else}}
    <tr>
        <th class="paddingT15">开奖时间:</th>
        <td class="paddingT15 wordSpacing5">{{$row.lottery_time|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
    </tr> 
    <tr>
        <th class="paddingT15">深圳指数:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableFile" type="text" name="shenzhen_stock" id="shenzhen_stock" value="" />
          <label class="field_notice"></label>
        </td>
    </tr>  
    <tr>
        <th class="paddingT15">上证指数:</th>
        <td class="paddingT15 wordSpacing5">
          <input class="infoTableFile" type="text" name="shanghai_stock" id="shanghai_stock" value="" />
          <label class="field_notice"></label>
        </td>
    </tr> 
    <tr>
        <th class="paddingT15"> </th>
        <td class="ptb20">
            <input type="button" value="开始抽奖" name="Submit" class="formbtn1" onClick="checkSubmit()">
            <input type="reset" value="重置" name="reset" class="formbtn2">
        </td>
    </tr>
    {{/if}}            
</table>  
</form>
<script type="text/javascript">
var validArray = ['shenzhen_stock', 'shanghai_stock'];
$(function(){
	for(var i=0; i<validArray.length; i++) {
		$('#' + validArray[i]).bind('blur',function(){
			validInput($(this).attr("id"));
		});
	}
});

function checkSubmit()
{
	var len = 0;
	for(var i=0; i<validArray.length; i++) {
		if(validInput(validArray[i])) {
			len++;
		}
	}	

	if(len == validArray.length) {
		$('.formbtn1').attr("value", "抽奖中...").attr("disabled", true);
		$("#form").submit();
	}	
}

function validInput(id) {
	var _msg = '';
	switch(id) {
		case 'shenzhen_stock':
				if($('#' + id).val().length == 0) {
					_msg = '请输入深圳指数';	
				}
			break;
		case 'shanghai_stock':
				if($('#' + id).val().length == 0) {
					_msg = '请输入上证指数';	
				}
			break;
	}
	if(_msg == '') {
		$('#' + id).closest("td").children("label").attr('class', 'field_notice').html('');
	} else {
		$('#' + id).closest("td").children("label").attr('class', 'error').html(_msg);
		return false;
	}
	return true;	
}

</script>
</div>


{{include file='admin/footer.php'}}