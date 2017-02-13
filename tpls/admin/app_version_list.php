{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/inline_edit.js"></script>
<div id="rightTop">
    <p>数据配置</p>
    <ul class="subnav">
        <li><span>APP版本控制</span></li>
        <li><a class="btn4" href="/admin/appversion/add">新增APP版本</a></li>
    </ul>
</div>

<div class="mrightTop">
    <div class="fontl">
        <form method="post" action="{{$_CONF.FORM_ACTION}}">
            <div class="left">
                手机型号：
           <select name="phone_type" class="querySelect">
            	<option value="">全部</option>
                <option value="ios" {{if $request.phone_type eq ios}}selected="selected"{{/if}}>IOS</option>
                <option value="android" {{if $request.phone_type eq android}}selected="selected"{{/if}}>ANDROID</option>
            </select>
               强制更新：
                <input class="querySelect" type="radio" name="is_update" value="1" {{if $request.is_update eq 1}}checked="checked"{{/if}} />
                <input type="submit" class="formbtn" value="查询" />
            </div>
            <a class="left formbtn1" href="/admin/appversion/list">撤销检索</a>

        </form>
    </div>
    <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
    <input type="hidden" name="page" id="page" value="{{$page}}" />
    <table width="100%" cellspacing="0" class="dataTable">
        {{if $data}}
        <tr class="tatr1">
            <td>手机来源</td>
            <td>APP版本号</td>
            <td>渠道地址</td>
            <td>备注</td>
            <td>是否强制更新</td>
            <td>安卓来源频道</td>
            <td>创建时间</td>
            <td>操作</td>
        </tr>
        {{/if}}
        {{foreach from=$data item=item}}
        <tr class="tatr2">
            <td>{{$item.type}}</td>
            <td>{{$item.version}}</td>
            <td>{{$item.url}}</td>
            <td>{{$item.content}}</td>
            <td>{{if $item.is_update eq 1}}是{{else}}否{{/if}}</td>
            <td>{{$item.channel}}</td>
			<td>{{$item.created|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
            <td> <a href="/admin/appversion/edit/id:{{$item.id}}">编辑</a>
                 | <a href="/admin/appversion/del/id:{{$item.id}}">删除</a>
            </td>
        </tr>
        {{foreachelse}}
        <tr class="no_data">
            <td colspan="8">暂无活动记录</td>
        </tr>
        {{/foreach}}
    </table>
    <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
</div>
{{include file='admin/footer.php'}}