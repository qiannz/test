<div data-sid="{{$commodity.0.shop_id}}" data-cid="{{$commodity.0.ticket_id}}" class="commodity imgs-big">
    <div class="imgs-title">
        <p class="imgs-shopname">{{$commodity.0.ticket_title}}</p>
        <p class="imgs-price">{{$commodity.0.selling_price}}</p>
    </div>
    <a class="imgs-pic" style="background-image: url({{$commodity.0.thumb_img.0.W640.img_url}})" href='{{$_CONF.SITE_URL}}/home/ticket/wap/tid/{{$commodity.0.ticket_id}}/from/ios#json={"type":"commodity","id":"{{$commodity.0.ticket_id}}"}'></a>
    <img  src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAoAAAAKACAMAAAA7EzkRAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAGUExURf///wAAAFXC034AAAAZdEVYdFNvZnR3YXJlAEFkb2JlIEltYWdlUmVhZHlxyWU8AAABpElEQVR4Xu3BgQAAAADDoPlTH+AKVQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABwakLaAAE+pQU0AAAAAElFTkSuQmCC"/>
</div>