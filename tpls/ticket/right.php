<div class="nyRight">
	{{if $shopInfo}}
    <!--百度地图-->
    <div class="shopMessage">
        <h3><a href="/home/shop/show/sid/{{$shopInfo.shop_id}}" target="_blank">{{$shopInfo.shop_name}}</a></h3>
        {{if $shopInfo.brand_name}}<p>品牌：
        <a href="{{if $shopInfo.brand_detail && $shopInfo.brand_detail.is_enable eq 1}}/home/brand/show/bid/{{$shopInfo.brand_id}}{{else}}/home/good/list/sid/0_{{$shopInfo.brand_id}}_0_0_0_0{{/if}}" target="_blank">{{$shopInfo.brand_name}}</a></p>
        {{/if}}
        {{if $shopInfo.circle_name}}<p>商圈：{{$shopInfo.circle_name}}</p>{{/if}}
        <p>详细地址：{{$shopInfo.shop_address}}</p>
        <div id="allmap" class="baiduMap"></div>
    </div>
    {{/if}}
    <!--其他商品-->
    {{if $goodShowHotList}}
    <div class="other">    	
        <div class="otherTit">
          <h3>热门商品</h3>
        </div>
        <div class="otherList">
                <ul>
                	{{foreach from=$goodShowHotList key=key item=item}}
                    <li>
                        <a href="{{$item.www_url}}" target="_blank"><img src="{{$item.img_url}}" width="160" height="210"></a>
                        <a href="{{$item.www_url}}" target="_blank">{{$item.title}}</a>
                    </li>
                    {{/foreach}}
                </ul>
        </div>
    </div>
    {{/if}}
</div>