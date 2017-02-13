{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/jquery.validate.min.js" ></script>
<div id="rightTop">
  <p>品牌管理</p>
  <ul class="subnav">
  	<li><span>APP设置</span></li>
  </ul>
</div>
<div class="info">
<form method="POST" id="app_form">
<table class="infoTable">
       <tr>  
        <th class="paddingT15"> 每日惊喜:</th>
        <td class="paddingT15 wordSpacing5">
       	 <input id="every_day_surprise" name="every_day_surprise" value="{{$every_day_surprise}}" style="width:450px;" />
        </td>
      </tr>
      <tr>
        <th class="paddingT15"> </th>
        <td class="ptb20">
    	  <input class="formbtn" type="submit" value="保存" />
      </tr>
</table>
</form>
</div>
{{include file='admin/footer.php'}}