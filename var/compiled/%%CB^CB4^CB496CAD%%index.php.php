<?php /* Smarty version 2.6.27, created on 2016-12-01 16:20:50
         compiled from admin/index.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'admin/index.php', 47, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<!-- Use IE7 mode -->
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7 charset=utf-8" />
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<title><?php echo $this->_tpl_vars['_CONF']['ADMIN_PAGE_TITLE']; ?>
</title>
<link href="/css/admin/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/js/jquery.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/admin/buy.js" charset="utf-8"></script>
<script type="text/javascript">
var menu = <?php echo $this->_tpl_vars['menu_json']; ?>
;

$(function(){
	$("#city").bind('change',function(){
		$.post("/admin/index/change-city", {city:$("#city").val()} , function(data){
			if(data == 'ok') {
				window.location = '/admin';
			}
		});
	
	});
});
</script>
<script type="text/javascript" src="/js/admin/index.js" charset="utf-8"></script>
</head>
<body>   
<div class="back_nav">
    <div class="back_nav_list">
    <!--<?php $_from = $this->_tpl_vars['back_nav']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['menu']):
?>-->
        <dl>
            <dt><?php echo $this->_tpl_vars['menu']['text']; ?>
</dt>
            <!--<?php $_from = $this->_tpl_vars['menu']['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['sub_key'] => $this->_tpl_vars['sub_menu']):
?>-->
            <dd><a href="javascript:;" onclick="openItem('<?php echo $this->_tpl_vars['sub_key']; ?>
','<?php echo $this->_tpl_vars['key']; ?>
');none_fn();"><?php echo $this->_tpl_vars['sub_menu']['text']; ?>
</a></dd>
            <!--<?php endforeach; endif; unset($_from); ?>-->
        </dl>
    <!--<?php endforeach; endif; unset($_from); ?>-->
    </div>
    <div class="shadow"></div>
    <div class="close_float"><img src="/css/admin/images/close2.gif" /></div>
</div>
<div id="head">
    <div id="logo"><a href="/admin"><img src="/css/admin/images/logo.png" alt=""  /></a></div>
    <div id="menu">
    <span>你好<strong> <?php echo $this->_tpl_vars['user']['userid']; ?>
 </strong><a href="/admin/index/logout">[退出]</a></span>
    <span><a target="_blank" href="/">[网站首页]</a></span>
    <span>切换城市：<select name="city" id="city"><?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['city_options'],'selected' => $this->_tpl_vars['city_selected']), $this);?>
</select></span>
    <a href="javascript:;" class="menu_btn1" id="iframe_refresh">刷新</a>
    <?php if ($this->_tpl_vars['user']['role_id'] == 1): ?>
    <a href="javascript:;" class="menu_btn2" id="clear_cache">更新缓存</a>
    <?php endif; ?>
    </div>
    <ul id="nav"></ul>
    <div id="headBg"></div>
</div>
<div id="content">
    <div id="left">
        <div id="leftMenus">
            <dl id="submenu">
                <dt><a class="ico1" id="submenuTitle" href="javascript:;"></a></dt>
            </dl>
            <dl id="history" class="history">
                <dt>
                    <a class="ico2" id="historyText" href="#">操作历史</a>
                </dt>
            </dl>
         </div>
    </div>
    <div id="right">
        <iframe frameborder="0" style="display:none;" width="100%" id="workspace"></iframe>
    </div>
</div>
</body>
</html>