{include file='admin/header.php'}
<script charset="utf-8" type="text/javascript" src="/js/admin/inline_edit.js" ></script>
<div id="rightTop">
  <p>帖子明细</p>
     <ul class="subnav">
        <li><a class="btn1" href="/admin/posts/list">帖子管理</a></li>
    </ul>
  <!--  
  <ul class="subnav">
    <li><a class="btn1" href="/admin/posts/del/id:{$threadRow.tid}">删除</a></li>
    <li><a class="btn1" href="/admin/posts/jh/tid:{$threadRow.tid}">{if $threadRow.displayorder eq 0}置顶{else}取消置顶{/if}</a></li>
    <li><a class="btn1" href="/admin/posts/move/tid:{$threadRow.tid}">帖子移动</a></li>
    {if $threadRow.tj eq 2}
    <li><a class="btn1" href="/admin/posts/cancel/tid:{$threadRow.tid}">取消首页推荐</a>
    {else}
    <li><a class="btn1" href="/admin/posts/home-recommend/tid:{$threadRow.tid}">首页推荐</a>
    {/if}
    
    {if $threadRow.tj eq 1}
    <li><a class="btn1" href="/admin/posts/cancel/tid:{$threadRow.tid}">取消右侧推荐</a></li>
    {else}
    <li><a class="btn1" href="/admin/posts/right-recommend/tid:{$threadRow.tid}">右侧推荐</a></li>
    {/if}
  </ul>
  -->
</div>

{if $threadRow}
<div class="info">
        <table class="infoTable">
		<tbody>
            <tr>
                <th class="paddingT15"> 发帖人:</th>
                <td class="paddingT15 wordSpacing5">
                  {$threadRow.author}
                </td>
            </tr>
            <tr>
                <th class="paddingT15"> 帖子标题:</th>
                <td class="paddingT15 wordSpacing5">
                {$threadRow.subject}
                </td>
            </tr>
            <tr>
                <th class="paddingT15"> 帖子简介:</th>
                <td class="paddingT15 wordSpacing5">
				{$threadRow.summary}     
                 </td>
            </tr>
            
                        <tr>
                <th class="paddingT15"> 最后回帖时间:</th>
                <td class="paddingT15 wordSpacing5">
				{$threadRow.lastpost|date:complete}
                 </td>
            </tr>
            
                        <tr>
                <th class="paddingT15"> 最后回帖人:</th>
                <td class="paddingT15 wordSpacing5">
				{$threadRow.lastposter}
                 </td>
            </tr>
            
                        <tr>
                <th class="paddingT15"> 访问数:</th>
                <td class="paddingT15 wordSpacing5">
				{$threadRow.views}
                 </td>
            </tr>
            
            <tr>
                <th class="paddingT15"> 回帖数:</th>
                <td class="paddingT15 wordSpacing5">
				{$threadRow.replies}
                 </td>
            </tr>
            
           <tr>
                <th class="paddingT15"> 帖子状态:</th>
                <td class="paddingT15 wordSpacing5">
				{if $threadRow.displayorder eq 0}默认{/if}{if $threadRow.displayorder eq 1}本版置顶{/if}
                 </td>
            </tr>
            
            <tr>
                <th class="paddingT15"> 帖子状态来源:</th>
                <td class="paddingT15 wordSpacing5">
				{if $threadRow.come_from eq 1}MP论坛{else}MP手机客户{/if}
                 </td>
            </tr>
            
                        <tr>
                <th class="paddingT15">发帖时间:</th>
                <td class="paddingT15 wordSpacing5">
				{$threadRow.dateline|date:complete}
                 </td>
            </tr>
            
                        <tr>
                <th class="paddingT15"> 回帖最大楼层:</th>
                <td class="paddingT15 wordSpacing5">
				{$threadRow.maxposition}
                 </td>
            </tr>
            
                        <tr>
                <th class="paddingT15"> 删除标记:</th>
                <td class="paddingT15 wordSpacing5">
				{if $threadRow.status eq 0}正常{else}已删除{/if}
                 </td>
            </tr>
            
                        <tr>
                <th class="paddingT15"> 类别:</th>
                <td class="paddingT15 wordSpacing5">
                {if $threadRow.category}
				{$threadRow.category}
				{/if}
                 </td>
            </tr>
                 
                        <tr>
                <th class="paddingT15"> 品牌:</th>
                <td class="paddingT15 wordSpacing5">
				{$threadRow.brand}
                 </td>
            </tr>
            
                        <tr>
                <th class="paddingT15"> 价格:</th>
                <td class="paddingT15 wordSpacing5">
				{$threadRow.price}
                 </td>
            </tr>
            
                        <tr>
                <th class="paddingT15"> 商圈:</th>
                <td class="paddingT15 wordSpacing5">
				{if $threadRow.district}
				{$threadRow.district}
				{/if}
                 </td>
            </tr>
            
                        <tr>
                <th class="paddingT15"> 商场:</th>
                <td class="paddingT15 wordSpacing5">
                {if $threadRow.market}
				{$threadRow.market}
                {/if}
                </td>
            </tr>
            
                        <tr>
                <th class="paddingT15"> 开始时间:</th>
                <td class="paddingT15 wordSpacing5">
                {if $threadRow.start_time}
				{$threadRow.start_time|date:complete}
                {/if}
                </td>
            </tr>
            
                        <tr>
                <th class="paddingT15"> 结束时间:</th>
                <td class="paddingT15 wordSpacing5">
                {if $threadRow.end_time}
				{$threadRow.end_time|date:complete}
				{/if}
                 </td>
            </tr>
            
                        <tr>
                <th class="paddingT15"> 地点:</th>
                <td class="paddingT15 wordSpacing5">
				{$threadRow.address}
                 </td>
            </tr>
            
                        <tr>
                <th class="paddingT15"> 经度:</th>
                <td class="paddingT15 wordSpacing5">
				{$threadRow.longitude}
                 </td>
            </tr>
            
            <tr>
                <th class="paddingT15"> 纬度:</th>
                <td class="paddingT15 wordSpacing5">
				{$threadRow.latitude}
                 </td>
            </tr>
            
            {if $threadRow.tj neq 0}            
                <tr>
                    <th class="paddingT15">推荐照片:</th>
                    <td class="paddingT15 wordSpacing5">
    				<img src="/{$threadRow.img_url}">
                     </td>
                </tr>
			{/if}
			</table>
        </tbody>

</div>
{if $imgInfo}
<div class="info">
<form method="post" >
<input type="hidden" name="tid" value="{$tid}">
        <table class="infoTable" width="100%">
         <tbody>
         	<tr>
         		{foreach item=item key=key from=$imgInfo}
         		{if $key mod 4 == 0}
         	    </tr>
         		<tr>
         		{/if}
                <td style="width:25%;text-align:center">
                <img src="{$_CONF.FTP_Img_Url}/nj/forum/small/{$item.filename}" width="150px" height="150px" border="1" />
                <br>
                <input type="radio" name="aid" value="{$item.aid}" {if $item.first eq 1} checked {/if} />
                </td>
         		{/foreach}
         	</tr>
           <tr>
            <th></th>
            <td class="ptb20">
            <input class="formbtn" type="submit" name="Submit" value="设置"  /> <span><label class="error">调整哪张图片为列表展示</label>
            {if $threadRow.image_type_id eq $imgtypeid}
            <input class="formbtn" type="submit" name="Submit" value="取消推荐"  /> <span><label class="error">已推荐至手机
            {else}
            <input class="formbtn" type="submit" name="Submit" value="推荐"  /> <span><label class="error">推荐至手机
            {/if}
            </label>
            </td>
           </tr>
         </tbody>
        </table>
</form>
</div>
{/if}
{/if}
{include file='admin/footer.php'}