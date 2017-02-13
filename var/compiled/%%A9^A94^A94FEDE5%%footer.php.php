<?php /* Smarty version 2.6.27, created on 2016-12-01 16:30:55
         compiled from admin/footer.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'admin/footer.php', 1, false),)), $this); ?>
<p id="page_footer">Copyright © 2004-<?php echo ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y') : smarty_modifier_date_format($_tmp, '%Y')); ?>
 mplife.com Rights Reserved</p>
</body>
</html>