<?php /* Smarty version 2.6.27, created on 2016-02-19 09:40:58
         compiled from _common/front_msg.php */ ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>超级购-名品导购网</title>
<link href="/css/reset.css?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
" rel="stylesheet" type="text/css">
<link href="/css/common.css?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
" rel="stylesheet" type="text/css">
<link href="/css/ny.css?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/js/jquery.js?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
"></script>
<script type="text/javascript" src="/js/index.js?t=<?php echo $this->_tpl_vars['_CONF']['WEB_VERSION']; ?>
"></script>
</head>
<body>
	<div class="w1210">
      <!--top-->
      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'top.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
      <!--nav-->
      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'nav.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
      <!--内页-->
      <div class="nyWaper">
          <div class="register-box">
                <!--头-->
                <div class="register-title">
                    <h2>提示<span class="en">Prompt</span></h2>
                </div>
                <!--头end-->
                <!--提示-->
                <div class="reg-prompt">
                    <p>
                        <?php echo $this->_tpl_vars['message']; ?>

                        <?php if ($this->_tpl_vars['redirect']): ?><br />
                        <a href="<?php echo $this->_tpl_vars['redirect']; ?>
">返回上一页</a>
                        <script type="text/javascript">
                            setTimeout(function(){window.location = '<?php echo $this->_tpl_vars['redirect']; ?>
'}, 3000);
                        </script>
                        <?php endif; ?>
                    </p>
                    <?php if ($this->_tpl_vars['links']): ?>
                    <p>您可以 <?php $_from = $this->_tpl_vars['links']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?><a href="<?php echo $this->_tpl_vars['item']['href']; ?>
"> <?php echo $this->_tpl_vars['item']['text']; ?>
</a>　<?php endforeach; endif; unset($_from); ?></p>  
                    <?php endif; ?>                 
                </div>
                <!--提示end-->
            </div>
      </div>
          <!--关于超级购-->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'bottom.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript">
$(function(){
	FnHover('allBtn','allBox');
})
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'footer.php', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>