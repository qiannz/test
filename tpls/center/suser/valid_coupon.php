<div class="verifyBox">
    <p class="inputTxt">
    <label class="ylabel">请输入手机号码：</label>
    <input type="text" class="text" name="phone" id="phone" />
    <a href="javascript:searchTicket({{$sid}})" id="inquire">查询</a>
    </p>
    <div class="txtRed" style="display:none"><p></p></div>
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
	
	function searchTicket(sid) {
		var phone = $('#phone').val();
		var phoneReg = /^1[3|4|5|6|7|8|9][0-9]{9}$/;
		
		if(!phoneReg.test(phone)) {
			$.dialog.alert('请输入正确的手机号码');
			$('#codelist ul').html('');
			$('#use').html('');
		} else {
			$('#codelist ul').html('');
			$('#use').html('');
			$('.txtRed p').html('');
			$('#inquire').html('查询中...').attr('disabled', true).removeAttr('href');
			$.getJSON('/home/suser/search-ticket?t=' + new Date().getTime(), { sid:sid, phone:phone}, function(json){
				switch(json.res) {
					case 100:
						var _html = '';
						var _shtml = '<p class="allchange" ><input type="checkbox" onClick="allcheck(this)"><span>全选</span></p>' +
									 '<p class="txtGreen"><a class="userBtn" href="javascript:useTicket({{$sid}})">现在使用</a></p>';
						$.each(json.extra, function(k, v){
							_html += '<li><input name="coupon" type="checkbox" value="'+v.detail_id+'"><span>'+v.ticket_title+'</span></li>';
						});
						$('.txtRed').hide().find('p').html('');
						$('#codelist ul').html(_html);
						$('#use').html(_shtml);
						break;
					case 300:
						$('.txtRed').show().find('p').html(json.msg);
						$('#codelist ul').html('');
						$('#use').html('');
						break;
				}
				$('#inquire').html('查询').attr('disabled', false).attr('href', 'javascript:searchTicket(' + sid + ')');
			});	
		}
	}
	
	function useTicket(sid) {
		var phone = $('#phone').val();
		var phoneReg = /^1[3|4|5|6|7|8|9][0-9]{9}$/;
		
		if(!phoneReg.test(phone)) {
			$.dialog.alert('请输入正确的手机号码');
			return false;
		} 
		
		var items = '';
		$('input[name=coupon]:checked:enabled').each(function(){
			items += $(this).attr('value') + ',';
		});
		items = items.substr(0, (items.length - 1));
		
		if(items == '') {
			$.dialog.alert('请选择要使用的优惠券');
		} else {
			$('.userBtn').html('验证中...').attr('href', 'javascript:void(0)');
			$.getJSON('/home/suser/use-ticket?t=' + new Date().getTime(), { sid:sid, phone:phone, items:items}, function(json){
				if(json.res == 100) {
					$('#codelist ul li').each(function() {
						if($.inArray($(this).children(':checkbox').val(), json.extra) !== -1) {
							$(this).children(':checkbox').attr('disabled', true);
						}
						$('.userBtn').html('现在使用').attr('href', 'javascript:useTicket('+ sid +')');
					});
					$.dialog.alert(json.msg);
				} else if(json.res == 300) {
					$.dialog.alert(json.msg);
				}
			});
		}
	}
</script>