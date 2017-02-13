{{include file='admin/header.php'}}
<link rel="stylesheet" type="text/css" href="/js/artDialog/skins/green.css"  />
<script charset="utf-8" type="text/javascript" src="/js/artDialog/jquery.artDialog.min.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/artDialog/artDialog.plugins.min.js" ></script>
<div id="rightTop">
    <p>新手包</p>
    <ul class="subnav">

        <li><a class="btn1" href="/admin/gift/list">领奖列表</a></li>
        <li><span>验证码管理</span></li>
        <li><a class="btn1" href="/admin/gift/add">新增验证码</a></li>
        <li><a class="btn1" href="/admin/gift/prize-list">奖品列表</a></li>
        <li><a class="btn1" href="/admin/gift/prize-add">新增奖品</a></li>
    </ul>
</div>

<div class="tdare">
    <input type="hidden" name="page" id="page" value="{{$page}}" />
    <table width="100%" cellspacing="0" class="dataTable">
        <tr class="tatr1">
            <td>验证码</td>
            <td>备注</td>
            <td>绑定人数</td>
            <td>是否启用</td>
            <td>操作</td>
        </tr>
        {{foreach from=$data key=key item=item}}
        <tr class="tatr2">
            <td>{{$item.captcha}}</td>
            <td>{{$item.remark}}</td>
            <td>{{$item.total}}</td>
            <td>{{if $item.is_enable eq 1}}已启用{{else}}未启用{{/if}}</td>
            <td><a href="/admin/gift/list/gift_id:{{$item.gift_id}}" >查看名单</a> || {{if $item.is_enable eq 1}}<a href="javascript:changDiable({{$item.gift_id}},'off')">停用</a>{{else}}<a href="javascript:changDiable({{$item.gift_id}},'on')">启用</a>{{/if}}</td>
        </tr>
        {{foreachelse}}
        <tr class="no_data">
            <td colspan="7">暂无数据</td>
        </tr>
        {{/foreach}}
    </table>
    <div id="dataFuncs">
        <div class="pageLinks">{{include file='admin/page.bottom.php'}}</div>
        <div class="clear"></div>
    </div>
</div>
<script type="text/javascript">
function changDiable(gift_id , _switch){
    $.dialog({
                    title: '提示',
                    content: '确定修改奖品状态？',
                    okValue: '确定',
                    ok: function () {
                        $.ajax({
                            url:'/admin/gift/disable',
                            type:'post',
                            dataType:'json',
                            data:{gift_id:gift_id ,_switch:_switch},
                            success:function(json){
                                if(json.status){
                                    $.dialog({
                                        title: '提示',
                                        content: '奖品状态修改成功',
                                        okValue: '确定',
                                        ok: function () {
                                            location.href = location;
                                        }
                                    })
                                }
                            }
                        })
                    },
                    cancelValue:'取消',
                    cancel:function(){}
    })




}
</script>
{{include file='admin/footer.php'}}