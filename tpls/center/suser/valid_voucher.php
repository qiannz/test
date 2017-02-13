<div class="verifyBox">
    <p class="inputTxt">
    <label class="ylabel">请输入验证码：</label>
    <input type="text" class="text" name="captcha" id="captcha">
    <a href="javascript:inquireTicket({{$sid}})" id="inquire">查询</a>
    </p>
    <div id="show" style="display:none"></div>
	<!--    
	<div class="txtGreen txtRed">
        <p> 验证码 1234 4567 9876 971 有效</p>
        <p>发券店铺：XXXX店铺；</p>
        <p>优惠券名称：优惠券标题优惠券标题；</p>
    </div>-->
    <div class="codelist" id="codelist"><ul></ul></div>
    <div id="use"></div>
</div>
<script type="text/javascript">
	function allcheck(obj){
		var list = document.getElementById('codelist').getElementsByTagName('input');
			for(var n in list)
			{
				if(obj.checked)
				{
					list[n].checked = true;
				}else{
					list[n].checked = false;
				}
			}
	}
	
	$("#captcha").focus(function(){
	  $(this).keyup(function(){
		var num=$(this).val();
		num=num.replace(/\s+/g,"");
		$(this).next().val(num);
		num=num.replace(/(.{4})/g,"$1 ");
		$(this).val(num);
	  });
	})

	function inquireTicket(sid) {
		var captcha = $('#captcha').val();
		captcha = captcha.replace(/\s+/g,"");	
		if(captcha == '') {
			$.dialog.alert('请输入验证码');
			$('#codelist ul').html('');
			$('#use').html('');
		} else {
			$('#codelist ul').html('');			
			$('#show').removeAttr('class').html('').hide();	
			$('#use').html('');
			$('#inquire').html('查询中...').attr('disabled', true).removeAttr('href');
			$.getJSON('/home/suser/inquire-ticket?t=' + new Date().getTime(), { sid:sid, captcha:captcha}, function(json){
				switch(json.res) {
					case 100:
						var _html = '';
						var _shtml = '<p class="allchange" ><input type="checkbox" onClick="allcheck(this)"><span>全选</span></p>' +
									 '<p class="txtGreen"><a class="userBtn" href="javascript:useTicket({{$sid}})">现在验证</a></p>';
						
						$.each(json.extra, function(k, v){
							_html += '<li><input name="coupon" type="checkbox" value="' + v.VcodeID + '" data-tid="'+v.ProudctId+'" data-sid="'+v.MerchantCommonId+'"';
							if(v.Status == -1 || v.Status == 1) {
								_html += ' disabled="disabled"';
							} 
							else if(v.Status == 0) {
							}							
							_html += '><span>'+v.ProductName+'</span></li>';
						});
						$('#show').html('');
						$('#codelist ul').html(_html);
						$('#use').html(_shtml);
						break;
					default:
						$('#show').addClass('txtRed').show().html('<p>' + json.msg + '</p>');
						$('#codelist ul').html('');
						$('#use').html('');
						break;
				}
				$('#inquire').html('查询').attr('disabled', false).attr('href', 'javascript:inquireTicket(' + sid + ')');
			});	
		}
	}
	
	function useTicket(sid) {
		var items = sidStr = tidStr = '';
		var captcha = $('#captcha').val();
		captcha = captcha.replace(/\s+/g,"");
		
		$('input[name=coupon]:checked:enabled').each(function(){
			items += $(this).attr('value') + ',';
			sidStr +=  $(this).attr('data-sid') + ',';
			tidStr +=  $(this).attr('data-tid') + ',';
		});
		items = items.substr(0, (items.length - 1));
		sidStr = sidStr.substr(0, (sidStr.length - 1));
		tidStr = tidStr.substr(0, (tidStr.length - 1));
		
		if(items == '') {
			$.dialog.alert('请选择要验证的现金券');
		} else {
			$('.userBtn').html('验证中...').attr('href', 'javascript:void(0)');
			$.getJSON('/home/suser/vaild-voucher-ticket?t=' + new Date().getTime(), { sid:sid, items:items, sidStr:sidStr, tidStr:tidStr, captcha:captcha}, function(json){
				if(json.res == 100) {
					$('#codelist ul li').each(function() {
						if($.inArray($(this).children(':checkbox').val(), json.extra) !== -1) {
							$(this).children(':checkbox').attr('disabled', true);
						}
						$('.userBtn').html('现在验证').attr('href', 'javascript:useTicket('+ sid +')');
					});
					$.dialog.alert(json.msg);
				} else if(json.res == 300) {
					$.dialog.alert(json.msg);
				}
			});
		}
	}
</script>