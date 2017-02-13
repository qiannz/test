<div class="shop-enter">
      	<h2 class="shop-enter-tit">商户入驻</h2>
        <!--菜单-->
        <div class="shop-enter-menu">
        	<ul>
            	<li><a>1.注册成为名品导购网会员</a></li>
                <li><a>2.填写商户信息</a></li>
                <li class="selbg"><a>3.经过审核，通过认证</a></li>
                <li><a>4.补全资料，完成入驻</a></li>
            </ul>
        </div>
        <!--内容-->
        <div class="shop-enter-con">
        	<h2 class="register-step-tit">3.经过审核，通过认证</h2>
            <p class="register-step-txt">
            		{{if $step eq 2}}
                    您的申请已提交，请等待名品导购网审核，此过程一般不超过5个工作日。<br/>
                    {{elseif $step eq 3}}
                    您的入驻申请审核不通过，返回<a href="/home/member/join/step/1/sid/{{$sid}}">重新提交</a>。<br/>
                    {{elseif $step eq 4}}
                    恭喜，您的入驻申请已通过审核，请点击进入下一步：<a href="/home/member/join/step/5/sid/{{$memberRow.shop_id}}">补全资料，完成入驻</a>。<br/>
                    {{/if}}
                    </p>

        </div>
      </div>