{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/jquery.validate.min.js" ></script>
<script type="text/javascript">
$(function(){
    $('#module_from').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
        success : function(label){
            label.addClass('right').text('OK!');
        },
        onkeyup    : false,
        rules : {
            m_name : {
                required : true,
                remote   : {
                url :'/admin/module/check-mod',
                type:'post',
                data:{
                    m_name : function(){
                        return $('#m_name').val();
                    },
                    pid : function() {
                        return $('#pid').val();
                    },
                    mid : '{{$moduleRow.mid}}'
                  }
                }
            },
            sequence : {
                number   : true
            }
        },
        messages : {
            m_name : {
                required : '模块名称不能为空',
				remote   : '该分类模块名称已经存在了，请您换一个'
            },
            sequence  : {
                number   : '此项必须为数字'
            }
        }
    });
});
</script>
<div id="rightTop">
    <p>模块管理</p>
    <ul class="subnav">
        <li><a class="btn1" href="/admin/module/list">管理</a></li>
        <li><!-- {{if $moduleRow.mid}} --><a class="btn1" href="/admin/module/add">新增</a><!-- {{else}} --><span>新增</span><!-- {{/if}} --></li>
    </ul>
</div>

<div class="info">
    <form method="post" id="module_from">
    	<input type="hidden" name="mid" value="{{$moduleRow.mid}}" />
    	<input type="hidden" name="cont" value="{{$cont}}" />
        <table class="infoTable">
            <tr>
                <th class="paddingT15">
                    模块名称:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput" id="m_name" type="text" name="m_name" value="{{$moduleRow.m_name}}" />
                    <label class="field_notice">模块分类名称</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">
                    <label for="pid">所属分类:</label></th>
                <td class="paddingT15 wordSpacing5">
                    <select id="pid" name="pid">
                        <option value="">请选择...</option>
                        {{foreach item=item key=key from=$moduleArr}}
                        <option value="{{$item.mid}}"{{if $item.mid eq $pid}} selected="selected"{{/if}}>{{$item.m_name}}</option>
                        {{/foreach}}
                    </select>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">模块路径:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput" id="m_path" type="text" name="m_path" value="{{$moduleRow.m_path}}" />
                </td>
            </tr>
            <tr>
                <th class="paddingT15">模块标记:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput" id="mark" type="text" name="mark" value="{{$moduleRow.mark}}" />
                </td>
            </tr>
            <tr>
                <th class="paddingT15">排序:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="sort_order" id="sequence" type="text" name="sequence" value="{{if $moduleRow.sequence}}{{$moduleRow.sequence}}{{else}}99{{/if}}" />
                </td>
            </tr>
            <tr>
                <th></th>
                <td class="ptb20">
                    <input class="formbtn" type="submit" name="Submit" value="提交" />
                    <input class="formbtn" type="reset" name="Submit2" value="重置" />
                </td>
            </tr>
        </table>
    </form>
</div>
{{include file='admin/footer.php'}}