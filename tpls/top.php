<div class="top">
    <div class="logo">
        <h1>
            <a href="/" class="superbuy-logo"><img src="/images/superbuy-logo.png" width="155" height="66"></a>
       </h1>
    </div>
    <div class="header-ad">
        <img src="/images/top-ad.jpg" width="180" height="60">
    </div>
    <form action="/home/search/list" method="post">
    <div class="sreach">
        <input type="text" class="txt" name="keyword" onblur="if (this.value=='') this.value='搜索关键字搜索商品';" onfocus="if(this.value=='搜索关键字搜索商品')this.value='';" value="{{if $key}}{{$key}}{{else}}搜索关键字搜索商品{{/if}}">
        <input type="submit" class="btn" value="搜索">
    </div>
    </form>
</div>