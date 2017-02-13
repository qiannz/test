<div class="win-popup">
    <a class="popup-close" onClick="shoutWinPopup()">关闭</a>
    <div class="win-box">
        {{if $scratchData.res eq 100}}
        <p class="win-txt">恭喜您！中奖啦！</p>
        <p class="win-con">您刮到了<span>{{$scratchData.award}}元</span>现金</p>
        {{else $scratchData.res eq 101 || $scratchData.res eq 102 || $scratchData.res eq 103}}
        <p class="win-txt">很遗憾！没有中奖。</p>
        <p class="win-con">再接再厉！下次一定有好运！</p>
        {{/if}}
        <a class="popup-btn" href="javascript:shoutWinPopup()">确定</a>
    </div>    
</div>
<div class="shade"></div>