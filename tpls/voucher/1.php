{{foreach from=$voucher item=item}}
<div data-tid="{{$item.ticket_id}}" class="coupon-box">
                    <a class="coupon-box-link" href='{{$_CONF.SITE_URL}}/home/ticket/wap/tid/{{$item.ticket_id}}/from/ios#json={"type":"voucher","id":"{{$item.ticket_id}}"}'></a>
                    <div class="coupon-box-pic">
                        <div class="coupon-box-tag">
                            <p class="coupon-box-now">{{$item.selling_price}}</p>
                            <p class="coupon-box-old">{{$item.par_value}}</p>
                        </div>
                        <div class="coupon-box-img" style="background-image: url('{{$item.cover_img}}')"></div>
                        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAlgAAAEYCAMAAAC3CHgfAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAGUExURf///wAAAFXC034AAAAZdEVYdFNvZnR3YXJlAEFkb2JlIEltYWdlUmVhZHlxyWU8AAAAuklEQVR4Xu3BMQEAAADCoPVPbQhfIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAIBLDZF2AAEIMDFqAAAAAElFTkSuQmCC">
                    </div>
                    <div class="coupon-box-txt">
                        {{$item.ticket_title}}
                    </div>

             </div>
{{/foreach}}