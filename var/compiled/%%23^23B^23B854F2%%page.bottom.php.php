<?php /* Smarty version 2.6.27, created on 2016-12-01 16:30:39
         compiled from admin/page.bottom.php */ ?>
<!--<?php if ($this->_tpl_vars['page_info']['page_count'] > 1): ?>-->
<div class="page mtr10">
  <a class="stat">共 <?php echo $this->_tpl_vars['page_info']['item_count']; ?>
条记录</a>
  <!--<?php if ($this->_tpl_vars['page_info']['prev_link']): ?>-->
  <a class="former" href="<?php echo $this->_tpl_vars['page_info']['prev_link']; ?>
"></a>
  <!--<?php else: ?>-->
  <span class="formerNull"></span>
  <!--<?php endif; ?>-->
  <!--<?php if ($this->_tpl_vars['page_info']['first_link']): ?>-->
 <a class="page_link" href="<?php echo $this->_tpl_vars['page_info']['first_link']; ?>
">1&nbsp;<!--<?php echo $this->_tpl_vars['page_info']['first_suspen']; ?>
--></a>
 <!--<?php endif; ?>-->
  <!--<?php $_from = $this->_tpl_vars['page_info']['page_links']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['page'] => $this->_tpl_vars['link']):
?>-->
  <!--<?php if ($this->_tpl_vars['page_info']['curr_page'] == $this->_tpl_vars['page']): ?>-->
  <a class="page_hover" href="<?php echo $this->_tpl_vars['link']; ?>
"><?php echo $this->_tpl_vars['page']; ?>
</a>
  <!--<?php else: ?>-->
  <a class="page_link" href="<?php echo $this->_tpl_vars['link']; ?>
"><?php echo $this->_tpl_vars['page']; ?>
</a>
  <!--<?php endif; ?>-->
  <!--<?php endforeach; endif; unset($_from); ?>-->
  <!--<?php if ($this->_tpl_vars['page_info']['last_link']): ?>-->
  <a class="page_link" href="<?php echo $this->_tpl_vars['page_info']['last_link']; ?>
"><!--<?php echo $this->_tpl_vars['page_info']['last_suspen']; ?>
-->&nbsp;<!--<?php echo $this->_tpl_vars['page_info']['page_count']; ?>
--></a>
  <!--<?php endif; ?>-->
  <span class="page_hover"><input type="text" id="jumpTo" size="3" value="<?php echo $this->_tpl_vars['page_info']['curr_page']; ?>
" /></span>
  <a class="page_hover" href="javascript:jumpTo('<?php echo $this->_tpl_vars['_CONF']['FORM_ACTION']; ?>
', '<?php echo $this->_tpl_vars['page_info']['page_str']; ?>
')">GO</a>
  <a class="nonce"><?php echo $this->_tpl_vars['page_info']['curr_page']; ?>
 / <?php echo $this->_tpl_vars['page_info']['page_count']; ?>
</a>
  <!--<?php if ($this->_tpl_vars['page_info']['next_link']): ?>-->
  <a class="down" href="<?php echo $this->_tpl_vars['page_info']['next_link']; ?>
">下一页</a>
  <!--<?php else: ?>-->
  <span class="downNull">下一页</span>
  <!--<?php endif; ?>-->
</div>
<!--<?php endif; ?>-->