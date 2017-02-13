<?php /* Smarty version 2.6.27, created on 2016-12-01 16:30:07
         compiled from admin/welcome.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'admin/welcome.php', 4, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/header.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="rightTop">
<p>
    您好，<b><?php echo $this->_tpl_vars['user']['userid']; ?>
</b>，欢迎使用 【后台系统】。您上次登录的时间是  <?php echo ((is_array($_tmp=$this->_tpl_vars['user']['logintime'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d %H:%M:%S') : smarty_modifier_date_format($_tmp, '%Y-%m-%d %H:%M:%S')); ?>
 ，IP 是 <?php echo $this->_tpl_vars['user']['loginip']; ?>

</p>
</div>
<dl id="rightCon">
<dt>系统信息</dt>
<dd>
    <table>
        <tr>
            <th>服务器操作系统:</th>
            <td class="td"><?php echo $this->_tpl_vars['welcome']['PHP_OS']; ?>
</td>
            <th>WEB 服务器:</th>
            <td class="td"><?php echo $this->_tpl_vars['welcome']['SERVER_SOFTWARE']; ?>
</td>
        </tr>
        <tr>
            <th>PHP 版本:</th>
            <td class="td"><?php echo $this->_tpl_vars['welcome']['PHP_VERSION']; ?>
</td>
            <th>MYSQL 版本:</th>
            <td class="td"><?php echo $this->_tpl_vars['welcome']['MYSQL_VERSION']; ?>
</td>
        </tr>
    </table>
</dd>
</dl>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin/footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>