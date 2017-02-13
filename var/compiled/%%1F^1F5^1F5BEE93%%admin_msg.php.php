<?php /* Smarty version 2.6.27, created on 2016-12-01 16:28:58
         compiled from _common/admin_msg.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', '_common/admin_msg.php', 40, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title> 个人站点 -- 提示信息 </title>
<link href="/css/admin/admin.css" rel="stylesheet" type="text/css" />
<style>
<!--
body {background: none}
h1 {font-size: 12px; color: #444; line-height: 55px; background: url(/css/admin/images/welcome_h1.gif); padding-left: 2%}
dl {line-height: 40px; background: url(/css/admin/images/welcome.gif) no-repeat left 10px; padding-left: 40px; margin: 35px 0 45px 15%}
dt {color: #009de6}
dd {color: #444;}
a {color: #06c}
a:hover {color: #09f}
p {color: #999; border-top: 1px solid #cbe4f5; text-align: center; padding-top: 20px;}
-->
</style>
</head>

<body>
<h1>系统消息</h1>
<dl>
    <dt><?php echo $this->_tpl_vars['message']; ?>
</dt>
    <?php if ($this->_tpl_vars['redirect']): ?>
     <a class="forward" href="<?php echo $this->_tpl_vars['redirect']; ?>
">返回上一页</a>
     <dd>如果您不做出选择，系统将自动跳转</dd>
    <?php endif; ?>
    <?php $_from = $this->_tpl_vars['links']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
    <dd><a href="<?php echo $this->_tpl_vars['item']['href']; ?>
" class="forward"><?php echo $this->_tpl_vars['item']['text']; ?>
</a></dd>
    <?php endforeach; endif; unset($_from); ?>
</dl>
<?php if ($this->_tpl_vars['redirect']): ?>
<script type="text/javascript">
<!--
window.setTimeout("location.href='<?php echo $this->_tpl_vars['redirect']; ?>
'", 5000);
//-->
</script>
<?php endif; ?>
<p>Copyright © 2004-<?php echo ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y') : smarty_modifier_date_format($_tmp, '%Y')); ?>
 mplife.com Rights Reserved</p>
</body>
</html>