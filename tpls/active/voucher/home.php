<div class="border">
    <div class="win-txt">
        <p>恭喜您获得名品街{{$mobileRow.award}}元现金券</p>
    </div>

    <div class="money">
        <img src="/images/active/voucher/money_{{$mobileRow.award}}.png">
    </div>

<p class="font_30">已经放入您的手机账户{{$mobileRow.mobile}}</p>
<p class="font_30">{{$mobileRow.award}}元红包放入名品街，手机登陆即可使用</p>
<a class="rule-btn"  onclick="$('#rulePop').show()"></a>
</div>

<div class="log-wrap">
    <h3>好友领取记录：</h3>
    <table width="100%" class="table">
    	{{foreach from=$mobileRow.awardInfoList key=key item=item}}
        <tr>
            <td>{{$item.mobile}}</td>
            <td>{{$item.award}}元</td>
            <td>{{$item.created}}</td>
        </tr>
        {{/foreach}}
    </table>
</div>