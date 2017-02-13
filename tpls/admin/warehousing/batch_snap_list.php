{{include file='admin/header.php'}}
<div id="rightTop">
  <p>仓储管理</p>
  <ul class="subnav">
    <li><span>入库记录</span></li>
    <li><a class="btn4" href="/admin/batch/choose-shop">新增入库</a></li>
    <li><span>入库校验</span></li>
  </ul>
</div>
<div class="tdare">
<table width="100%" cellspacing="0" class="dataTable">
    <tr class="tatr1">
        <td>品类</td>
        <td>品名</td>
        <td>货号</td>
        <td>条形码</td>
        <td>质地</td>
        <td>适合人群</td>
        <td>年份</td>
        <td>季节</td>
        <td>颜色</td>
        <td>尺码</td>
        <td>数量</td>
        <td>原价</td>
        <td>现价</td>
        <td>卖点</td>
        <td>简介</td>
        <td>是否包邮</td>
        <td>详情</td>
    </tr>
    {{foreach from=$data.data key=key item=item}}
    <tr class="tatr2">
    	{{foreach from=$item key=skey item=sitem}}
      	<td {{if $sitem.mark eq 1}}style="background-color: #FFB6C1"{{/if}}>{{$sitem.value}}</td>
        {{/foreach}}
    </tr>
    {{/foreach}}
</table>
</div>

<div class="info">
<form method="POST" action="{{$_CONF.FORM_ACTION}}">
<input type="hidden" name="sid" id="sid" value="{{$data.detail.sid}}" />
<input type="hidden" name="sname" id="sname" value="{{$data.detail.sname}}" />
<input type="hidden" name="batch" id="batch" value="{{$data.detail.good_batch}}" />
<input type="hidden" name="stime" id="stime" value="{{$data.detail.stime}}" />
<input type="hidden" name="etime" id="etime" value="{{$data.detail.etime}}" />
<input type="hidden" name="action" id="action" value="on" />
<table class="infoTable">
	<tr>
        <th class="paddingT15">当前批次:</th>
        <td class="paddingT15 wordSpacing5">{{$data.detail.good_batch}}</td>
    </tr>
    <tr>
        <th class="paddingT15">所在店铺:</th>
        <td class="paddingT15 wordSpacing5">{{$data.detail.sname}}</td>
    </tr>  
    <tr>
        <th class="paddingT15">销售时间:</th>
        <td class="paddingT15 wordSpacing5">{{$data.detail.stime}} - {{$data.detail.etime}}</td>    	
    </tr>
    <tr>
        <th class="paddingT15"> </th>
        <td class="ptb20">
            <input type="submit" value="确认提交" name="Submit" class="formbtn1" {{if $data.can_sub eq 1}} disabled="disabled"{{/if}} />
        </td>
    </tr>    
</table>
</form>
</div>
{{include file='admin/footer.php'}}