{{include file='admin/header.php'}}
<script charset="utf-8" type="text/javascript" src="/js/jquery.validate.min.js" ></script>
<script type="text/javascript" src="/js/DatePicker/WdatePicker.js" charset="utf-8"></script>
<script type="text/javascript">
    $(function(){
        $('#active_form').validate({
            errorPlacement: function(error, element){
                $(element).next('.field_notice').hide();
                $(element).after(error);
            },
            success       : function(label){
                label.addClass('right').text('OK!');
            },
            rules : {
                keyword : {
                    required : true
                },
                keyword_searches : {
                    required : true,
                    number : true
                }
            },
            messages : {
                keyword : {
                    required : '请输入关键词名称'
                },
                keyword_searches : {
                    required : '请输入关键词次数',
                    number : '关键词搜索次数必须为数字'
                }
            }
        });

    });
</script>
<div id="rightTop">
    <p>数据配置</p>
    <ul class="subnav">
        <li><a class="btn1" href="/admin/keyword/list">关键词列表</a></li>
        <li><span>新增关键词</span></li>
    </ul>
</div>
<div class="info">
    <form method="post" id="active_form">
        <input type="hidden" name="keyword_id" value="" />
        <table class="infoTable">
            <tr>
                <th class="paddingT15"> 关键词:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="keywrod" type="text" name="keyword" value="" />
                    <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15"> 关键词搜索次数:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" style='width:30px;'id="keyword_searches" type="text" name="keyword_searches" value="" />
                    <label class="field_notice"></label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15"> 关键词类型:</th>
                <td class="paddingT15 wordSpacing5">
                    <input  id="keyword_type" type="radio" name="keyword_type" value="0" {{if $keyword.keyword_type eq 0}}checked{{/if}}  />无
                    <input  id="keyword_type" type="radio" name="keyword_type" value="1" {{if $keyword.keyword_type eq 1}}checked{{/if}} />商品
                    <input  id="keyword_type" type="radio" name="keyword_type" value="2" {{if $keyword.keyword_type eq 2}}checked{{/if}}  />店铺
                    <label class="field_notice"></label>
                </td>
            </tr>

            <tr>
                <th></th>
                <td class="ptb20"><input class="formbtn" type="submit" name="Submit" value="提交" />
                    <input class="formbtn" type="reset" name="Reset" value="重置" />        </td>
            </tr>
        </table>
    </form>
</div>
{{include file='admin/footer.php'}}