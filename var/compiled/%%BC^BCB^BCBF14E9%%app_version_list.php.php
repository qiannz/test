<?php /* Smarty version 2.6.27, created on 2016-02-02 15:45:17
         compiled from admin/app_version_list.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'admin/app_version_list.php', 54, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
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
        <form method="post" action="<?php echo $this->_tpl_vars['_CONF']['FORM_ACTION']; ?>
">
            <div class="left">
                手机型号：
           <select name="phone_type" class="querySelect">
            	<option value="">全部</option>
                <option value="ios" <?php if ($this->_tpl_vars['request']['phone_type'] == ios): ?>selected="selected"<?php endif; ?>>IOS</option>
                <option value="android" <?php if ($this->_tpl_vars['request']['phone_type'] == android): ?>selected="selected"<?php endif; ?>>ANDROID</option>
            </select>
               强制更新：
                <input class="querySelect" type="radio" name="is_update" value="1" <?php if ($this->_tpl_vars['request']['is_update'] == 1): ?>checked="checked"<?php endif; ?> />
                <input type="submit" class="formbtn" value="查询" />
            </div>
            <a class="left formbtn1" href="/admin/appversion/list">撤销检索</a>

        </form>
    </div>
    <div class="fontr"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/page.top.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
</div>
<div class="tdare">
    <input type="hidden" name="page" id="page" value="<?php echo $this->_tpl_vars['page']; ?>
" />
    <table width="100%" cellspacing="0" class="dataTable">
        <?php if ($this->_tpl_vars['data']): ?>
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
        <?php endif; ?>
        <?php $_from = $this->_tpl_vars['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
        <tr class="tatr2">
            <td><?php echo $this->_tpl_vars['item']['type']; ?>
</td>
            <td><?php echo $this->_tpl_vars['item']['version']; ?>
</td>
            <td><?php echo $this->_tpl_vars['item']['url']; ?>
</td>
            <td><?php echo $this->_tpl_vars['item']['content']; ?>
</td>
            <td><?php if ($this->_tpl_vars['item']['is_update'] == 1): ?>是<?php else: ?>否<?php endif; ?></td>
            <td><?php echo $this->_tpl_vars['item']['channel']; ?>
</td>
			<td><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M:%S') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M:%S')); ?>
</td>
            <td> <a href="/admin/appversion/edit/id:<?php echo $this->_tpl_vars['item']['id']; ?>
">编辑</a>
                 | <a href="/admin/appversion/del/id:<?php echo $this->_tpl_vars['item']['id']; ?>
">删除</a>
            </td>
        </tr>
        <?php endforeach; else: ?>
        <tr class="no_data">
            <td colspan="8">暂无活动记录</td>
        </tr>
        <?php endif; unset($_from); ?>
    </table>
    <div class="pageLinks"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/page.bottom.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>