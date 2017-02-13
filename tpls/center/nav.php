<div class="tab-title">
	<ul>
		{{if $_CONF._A eq 'my-good'}}
		<li  class="sel" ><a href="javascript:void(0)">店铺商品管理</a></li>
		{{else}}
		<li><a href="/home/suser/my-good/sid/{{$sid}}">店铺商品管理</a></li>
		{{/if}}
		
        {{if $_CONF._A eq 'add'}}
            <li class="sel"><a href="javascript:void(0)">上传商品</a></li>
        {{else}}
            <li><a href="/home/suser/add/sid/{{$sid}}">上传商品</a></li>
        {{/if}}	
		
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
 
        {{if $user.user_type eq 2}}
            {{if $_CONF._A eq 'valid-record'}}
            <li class="sel" ><a href="javascript:void(0)" >验证记录</a></li>
            {{else}}
            <li><a href="/home/suser/valid-record/sid/{{$sid}}">验证记录</a></li>
            {{/if}}
        {{/if}}
                
        {{if $_CONF._A eq 'good-edit'}}
		<li class="sel"><a href="javascript:void(0)">编辑商品</a></li>
        {{/if}}
            
        {{if $_CONF._A eq 'coupon-edit'}}
        <li class="sel" ><a href="javascript:void(0)" >券编辑</a></li>
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
        
        {{if $user.user_type eq 2}}
            {{if $_CONF._A eq 'my-account'}}
            <li class="sel" ><a href="javascript:void(0)" >账户记录</a></li>
            {{else}}
            <li><a href="/home/suser/my-account/sid/{{$sid}}">账户记录</a></li>
            {{/if}}
        {{/if}}
	</ul>
</div>