{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/inline_edit.js"></script>
<div id="rightTop">
    <p>数据配置</p>
    <ul class="subnav">
        <li><span>搜索管理</span></li>
        <li><a class="btn1" href="/admin/keyword/add">新增关键词</a></li>
    </ul>
</div>

<div class="mrightTop">
    <div class="fontl">
        <form method="post" action="{{$_CONF.FORM_ACTION}}">
            <div class="left">
                关键词：
                <input class="queryInput" type="text" name="keyword" value="{{$request.keyword}}" />
                热门
                <input class="querySelect" type="radio" name="is_hot" value="1" {{if $request.is_hot eq 1}}checked="checked"{{/if}} />
                <input type="submit" class="formbtn" value="查询" />
            </div>
            <a class="left formbtn1" href="/admin/keyword/list">撤销检索</a>

        </form>
    </div>
    <div class="fontr">{{include file='admin/page.top.php'}}</div>
</div>
<div class="tdare">
    <input type="hidden" name="page" id="page" value="{{$page}}" />
    <table width="100%" cellspacing="0" class="dataTable">
        {{if $data}}
        <tr class="tatr1">
            <td>关键词</td>
            <td>关键词搜索次数</td>
            <td>关键词类型</td>
            <td>是否热门</td>
            <td>排序</td>
            <td>操作</td>
        </tr>
        {{/if}}
        {{foreach from=$data item=item}}
        <tr class="tatr2">
            <td>{{$item.keyword_name}}</td>
            <td>{{$item.keyword_searches}}</td>
            <td>{{if $item.keyword_type eq 1}}商品{{elseif $item.keyword_type eq 2}}店铺{{elseif  $item.keyword_type eq 0}}无{{/if}}</td>
            <td>{{if $item.is_hot eq 1}}是{{else}}否{{/if}}</td>
            <td><span ectype="inline_edit" fieldname="sequence" fieldid="{{$item.keyword_id}}" required="1" class="node_name editable">{{$item.sequence}}</span></td>
            <td><a href="/admin/keyword/recommend/id:{{$item.keyword_id}}/page:{{$page}}">热门</a>
                 | <a href="/admin/keyword/edit/id:{{$item.keyword_id}}">编辑</a>
                 | <a href="/admin/keyword/del/id:{{$item.keyword_id}}">删除</a>
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