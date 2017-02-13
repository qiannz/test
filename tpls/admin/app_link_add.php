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
            name : {
                required : true,
                remote   : {
                url :'/admin/link/check-name',
                type:'post',
                data:{
                    name : function(){
                        return $('#name').val();
                    },
                    pid : function() {
                        return $('#pid').val();
                    },
                    id : '{{$moduleRow.id}}'
                  }
                }
            },
            mark : {
            	required : true,	
            },
            sequence : {
                number   : true
            }
        },
        messages : {
            name : {
                required : '链接名称不能为空',
				remote   : '该链接名称已经存在了，请您换一个'
            },
            mark : {
            	required : '链接标记不能为空',	
            },
            sequence  : {
                number   : '此项必须为数字'
            }
        }
    });
});
</script>
<div id="rightTop">
    <p>推荐管理</p>
    <ul class="subnav">
        <li><a class="btn1" href="/admin/link/list">链接设置</a></li>
        <li><!-- {{if $moduleRow.id}} --><a class="btn1" href="/admin/link/add">链接新增</a><!-- {{else}} --><span>链接新增</span><!-- {{/if}} --></li>
    </ul>
</div>

<div class="info">
    <form method="post" id="module_from">
    	<input type="hidden" name="id" value="{{$moduleRow.id}}" />
    	<input type="hidden" name="cont" value="{{$cont}}" />
        <table class="infoTable">
            <tr>
                <th class="paddingT15">
                               链接名称:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput" id="name" type="text" name="name" value="{{$moduleRow.name}}" />
                    <label class="field_notice">链接分类名称</label>
                </td>
            </tr>
            {{if $pid gt 0}}
            <tr>
                <th class="paddingT15">
                    <label for="pid">所属分类:</label></th>
                <td class="paddingT15 wordSpacing5">
                    <select id="pid" name="pid">
                        <option value="">请选择...</option>
                        {{foreach item=item key=key from=$moduleArr}}
                        <option value="{{$item.id}}"{{if $item.id eq $pid}} selected="selected"{{/if}}>{{$item.name}}</option>
                        {{/foreach}}
                    </select>
                </td>
            </tr>
			{{/if}}
            <tr>
                <th class="paddingT15">链接标记:</th>
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