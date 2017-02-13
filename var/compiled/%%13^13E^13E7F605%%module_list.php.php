<?php /* Smarty version 2.6.27, created on 2016-12-01 16:30:55
         compiled from admin/module_list.php */ ?>
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
<div id="rightTop">
    <p>模块管理</p>
    <ul class="subnav">
        <li><span>管理</span></li>
        <li><a class="btn1" href="/admin/module/add">新增</a></li>
    </ul>
</div>
<div class="tdare">
    <table class="distinction">
        <!-- <?php if ($this->_tpl_vars['acategories']): ?> -->
        <thead>
            <tr>
                <th class="w30"><input id="checkall_1" type="checkbox" class="checkall" /></th>
                <th width="20%"><span class="all_checkbox">
                    <label for="checkall_1">全选</label>
                    </span>分类名称</th>
                <th>分类标记</th>
                <th width="20%">模块路径</th>
                <th>排序</th>
                <th class="handler">操作</th>
            </tr>
        </thead>
        <tbody id="treet1">
            <!-- <?php endif; ?> -->
            <!--<?php $_from = $this->_tpl_vars['acategories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['acategory']):
?>-->
            <tr>
                <td class="align_center w30"><input type="checkbox" class="checkitem" value="<?php echo $this->_tpl_vars['acategory']['mid']; ?>
" /></td>
                <td class="node"><span ectype="inline_edit" fieldname="m_name" fieldid="<?php echo $this->_tpl_vars['acategory']['mid']; ?>
" required="1" class="node_name editable"><?php echo $this->_tpl_vars['acategory']['m_name']; ?>
</span></td>
                <td><?php echo $this->_tpl_vars['acategory']['mark']; ?>
</td>
                <td><?php if ($this->_tpl_vars['acategory']['layer'] > 1): ?>/<?php echo $this->_tpl_vars['_CONF']['Default_Manager_Module_Path']; ?>
/<?php echo $this->_tpl_vars['acategory']['m_path']; ?>
<?php endif; ?></td>
                <td class="align_center">
                	<?php if ($this->_tpl_vars['acategory']['pid'] != 0): ?>
                    　　　　
                    <?php endif; ?>
                    <span ectype="inline_edit" fieldname="sequence" fieldid="<?php echo $this->_tpl_vars['acategory']['mid']; ?>
" datatype="pint" maxvalue="99" class="editable"><?php echo $this->_tpl_vars['acategory']['sequence']; ?>
</span></td>
                <td class="handler"><span>
                    <a href="/admin/module/edit/id:<?php echo $this->_tpl_vars['acategory']['mid']; ?>
">编辑</a> | <a href="javascript:if(confirm('删除该分类将会同时删除该分类的所有下级分类，您确定要删除吗'))window.location = '/admin/module/drop/id:<?php echo $this->_tpl_vars['acategory']['mid']; ?>
';">删除</a>
                    <!-- <?php if ($this->_tpl_vars['acategory']['layer'] < $this->_tpl_vars['max_layer'] && $this->_tpl_vars['acategory']['parent_children_valid']): ?> -->
                    |
                    <a href="/admin/module/add/pid:<?php echo $this->_tpl_vars['acategory']['mid']; ?>
">新增下级</a>
                    <!--<?php endif; ?>-->
                    </span> </td>
            </tr>
            <!--<?php endforeach; else: ?>-->
            <tr class="no_data">
                <td colspan="6">暂无模块</td>
            </tr>
            <!--<?php endif; unset($_from); ?>-->
            <!-- <?php if ($this->_tpl_vars['acategories']): ?> -->
        </tbody>
        <!-- <?php endif; ?> -->
        <tfoot>
            <tr class="tr_pt10">
                <!-- <?php if ($this->_tpl_vars['acategories']): ?> -->
                <td class="align_center"><label for="checkall1">
                    <input id="checkall_2" type="checkbox" class="checkall">
                    </label></td>
                <td colspan="3" id="batchAction"><span class="all_checkbox">
                    <label for="checkall_2">全选</label>
                    </span>&nbsp;&nbsp;
                    <input class="formbtn batchButton" type="button" value="删除" name="id" uri="drop" presubmit="confirm('删除该分类将会同时删除该分类的所有下级分类，您确定要删除吗');" />
                    <!--<input class="formbtn batchButton" type="button" value="lang.update_order" name="id" presubmit="updateOrder(this);" />-->
                </td>
                <!--<?php endif; ?>-->
            </tr>
        </tfoot>
    </table>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>