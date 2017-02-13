<?php /* Smarty version 2.6.27, created on 2016-02-17 14:14:00
         compiled from admin/position_list.php */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script charset="utf-8" type="text/javascript" src="/js/jqtreetable.js" ></script>
<script charset="utf-8" type="text/javascript" src="/js/inline_edit.js" ></script>
<link rel="stylesheet" type="text/css" href="/css/admin/jqtreetable.css"  />
<script type="text/javascript">
$(function()
{
    var map = <?php echo $this->_tpl_vars['map']; ?>
;
    if (map.length > 0)
    {
        var option = {openImg: "/css/admin/images/treetable/tv-collapsable.gif", shutImg: "/css/admin/images/treetable/tv-expandable.gif", leafImg: "/css/admin/images/treetable/tv-item.gif", lastOpenImg: "/css/admin/images/treetable/tv-collapsable-last.gif", lastShutImg: "/css/admin/images/treetable/tv-expandable-last.gif", lastLeafImg: "/css/admin/images/treetable/tv-item-last.gif", vertLineImg: "/css/admin/images/treetable/vertline.gif", blankImg: "/css/admin/images/treetable/blank.gif", collapse: false, column: 1, striped: false, highlight: true, state:false};
        $("#treet1").jqTreeTable(map, option);
    }
});
</script>
<script charset="utf-8" type="text/javascript" src="/js/inline_edit.js"></script>
<div id="rightTop">
    <p>推荐管理</p>
    <ul class="subnav">
        <li><span>推荐位列表</span></li>
        <li><a class="btn3" href="/admin/position/add">新增推荐位</a></li>
    </ul>
</div>
<div class="info2">
    <table class="distinction">
        <thead>
            <tr>
                <th><input id="checkall_1" type="checkbox" class="checkall" /></th>
                <th><span class="all_checkbox"><label for="checkall_1">全选</label></span>推荐位名称</th>
                <th class="table-left">推荐位标记</th>
                <th class="table-left">链接地址</th>
                <th>图片宽度</th>
                <th>图片高度</th>
                <th>排序</th>
                <th width="200">操作</th>
            </tr>
        </thead>
        <tbody id="treet1">        
            <?php $_from = $this->_tpl_vars['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
            <tr>
                <td class="align_center w30"><input type="checkbox" class="checkitem"  value="<?php echo $this->_tpl_vars['item']['pos_id']; ?>
" /></td>
                <td ><?php echo $this->_tpl_vars['item']['pos_name']; ?>
</td>
                <td class="table-left"><?php echo $this->_tpl_vars['item']['identifier']; ?>
</td>
                <td class="table-left"><?php echo $this->_tpl_vars['item']['pos_url']; ?>
</td>
                <td class="align_center"><?php if ($this->_tpl_vars['item']['width']): ?><?php echo $this->_tpl_vars['item']['width']; ?>
<?php endif; ?> </td>
                <td class="align_center"><?php if ($this->_tpl_vars['item']['height']): ?><?php echo $this->_tpl_vars['item']['height']; ?>
<?php endif; ?></td>
                <td class="align_center">
                <?php if ($this->_tpl_vars['item']['pos_pid'] > 0): ?>　　　　<?php endif; ?>
                <span ectype="inline_edit" fieldname="sequence" fieldid="<?php echo $this->_tpl_vars['item']['pos_id']; ?>
" required="1" class="node_name editable"><?php echo $this->_tpl_vars['item']['sequence']; ?>
</span></td>
                <td class="align_left">
                    <a href="/admin/position/edit/pid:<?php echo $this->_tpl_vars['item']['pos_pid']; ?>
/id:<?php echo $this->_tpl_vars['item']['pos_id']; ?>
">编辑</a>　|　
                    <a href="javascript:if(confirm('您确定要删除该版块吗'))window.location = '/admin/position/del/id:<?php echo $this->_tpl_vars['item']['pos_id']; ?>
';">删除</a>                  
                    <?php if ($this->_tpl_vars['item']['layer'] < $this->_tpl_vars['max_layer'] && $this->_tpl_vars['item']['parent_children_valid']): ?>
                    　|　<a href="/admin/position/add/pid:<?php echo $this->_tpl_vars['item']['pos_id']; ?>
">新增下级</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; else: ?>
            <tr class="no_data">
                <td colspan="8">暂无记录</td>
            </tr>
            <?php endif; unset($_from); ?>
        </tbody>
        <tfoot>
            <tr class="tr_pt10">
                <td class="align_center"><label for="checkall1">
                    <input id="checkall_2" type="checkbox" class="checkall">
                    </label></td>
                <td colspan="3" id="batchAction">
                	<span class="all_checkbox">
                    <label for="checkall_2">全选</label>
                    </span>&nbsp;&nbsp;
                    <input class="formbtn batchButton" type="button" value="删除" name="id" uri="del" presubmit="confirm('删除该分类将会同时删除该分类的所有下级分类，您确定要删除吗');" />
                </td>
            </tr>
        </tfoot>
    </table>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>