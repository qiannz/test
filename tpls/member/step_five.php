<div class="shop-enter">
      	<h2 class="shop-enter-tit">商户入驻</h2>
        <!--菜单-->
        <div class="shop-enter-menu">
        	<ul>
            	<li><a>1.注册成为名品导购网会员</a></li>
                <li><a>2.填写商户信息</a></li>
                <li><a>3.经过审核，通过认证</a></li>
                <li class="selbg"><a>4.补全资料，完成入驻</a></li>
            </ul>
        </div>
        <!--内容-->
        <div class="shop-enter-con">
        	<h2 class="register-step-tit">4.补全资料，完成入驻</h2>
            <p class="register-step-txt">
            您选择的套餐为：{{$packArray[$memberRow.pack_id].pack_name}}，
            {{if $packArray[$memberRow.pack_id].pack_logo neq 'basic'}}
            您可以通过下列支付方式完成支付：
            {{else}}
            您的补全资料已提交，请等待名品导购网审核，此过程一般不超过5个工作日
            {{/if}}
            </p>
            {{if $packArray[$memberRow.pack_id].pack_logo neq 'basic'}}
                {{if $packArray[$memberRow.pack_id].pack_logo eq 'customized'}}
                <div>
                	<p class="online-pay-ps">联系电话：021-52519666-8034。</p>
                </div>
                {{else}}
                <div class="online-pay">
                    <h2 class="online-pay-tit">支付宝在线支付</h2>
                    <p class="online-pay-ps">注：选择在线支付，请在2小时内完成支付，过期未支付将取消本次入驻申请。</p>
                    {{if $packArray[$memberRow.pack_id].pack_logo eq 'comfort'}}
                    <a class="online-pay-btn" href="http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=97de9ee7-72be-424c-9b7c-925d7822cea6">确认支付</a>
                    {{elseif $packArray[$memberRow.pack_id].pack_logo eq 'luxury'}}
                    <a class="online-pay-btn" href="http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=086b0a9c-5e17-4079-a8c1-2f0191e7d413">确认支付</a>
                    {{elseif $packArray[$memberRow.pack_id].pack_logo eq 'distinguished'}}
                    <a class="online-pay-btn" href="http://superbuy.mplife.com/Pay/Order.aspx?ActivityID=e464df22-2b1a-47c3-b07d-d4598736dd1f">确认支付</a>
                    {{/if}}
                </div>
                <div class="offline-pay">
                    <h2 class="offline-pay-tit">线下银行转账支付</h2>
                    <p class="offline-pay-bank">开户银行：中国银行</p>
                    <p class="offline-pay-id">账号：1234 5678 9876 3654 876</p>
                    <p class="online-pay-ps">注：选择银行在线支付，请在24小时内完成支付，过期未支付将取消本次入驻申请。<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;转账完成后请联系客服确认，联系电话：021-52519666-8034。</p>
                </div>
                {{/if}}         
            {{/if}}
            <a class="return-pre" href="/home/member/join/sid/{{$sid}}/step/6">返回上一步重新选择套餐</a>
        </div>
      </div>