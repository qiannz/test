<!--{{if $page_info.page_count gt 1}}-->
<div class="page mtr10">
  <a class="stat">共 {{$page_info.item_count}}条记录</a>
  <!--{{if $page_info.prev_link}}-->
  <a class="former" href="{{$page_info.prev_link}}"></a>
  <!--{{else}}-->
  <span class="formerNull"></span>
  <!--{{/if}}-->
  <!--{{if $page_info.first_link}}-->
 <a class="page_link" href="{{$page_info.first_link}}">1&nbsp;<!--{{$page_info.first_suspen}}--></a>
 <!--{{/if}}-->
  <!--{{foreach from=$page_info.page_links key=page item=link}}-->
  <!--{{if $page_info.curr_page eq $page}}-->
  <a class="page_hover" href="{{$link}}">{{$page}}</a>
  <!--{{else}}-->
  <a class="page_link" href="{{$link}}">{{$page}}</a>
  <!--{{/if}}-->
  <!--{{/foreach}}-->
  <!--{{if $page_info.last_link}}-->
  <a class="page_link" href="{{$page_info.last_link}}"><!--{{$page_info.last_suspen}}-->&nbsp;<!--{{$page_info.page_count}}--></a>
  <!--{{/if}}-->
  <span class="page_hover"><input type="text" id="jumpTo" size="3" value="{{$page_info.curr_page}}" /></span>
  <a class="page_hover" href="javascript:jumpTo('{{$_CONF.FORM_ACTION}}', '{{$page_info.page_str}}')">GO</a>
  <a class="nonce">{{$page_info.curr_page}} / {{$page_info.page_count}}</a>
  <!--{{if $page_info.next_link}}-->
  <a class="down" href="{{$page_info.next_link}}">下一页</a>
  <!--{{else}}-->
  <span class="downNull">下一页</span>
  <!--{{/if}}-->
</div>
<!--{{/if}}-->