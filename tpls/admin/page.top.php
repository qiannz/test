<!--{{if $page_info.page_count gt 1}}-->
<div class="page">
  <div class="flip_over">翻页: </div>
  <!--{{if $page_info.prev_link}}-->
  <a class="former" href="{{$page_info.prev_link}}"></a>
  <!--{{else}}-->
  <span class="formerNull"></span>
  <!--{{/if}}-->
  <!--{{if $page_info.next_link}}-->
  <a class="down" href="{{$page_info.next_link}}">下一页</a>
  <!--{{else}}-->
  <span class="downNull">下一页</span>
  <!--{{/if}}-->
</div>
<!--{{/if}}-->
