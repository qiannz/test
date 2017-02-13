<?php /* Smarty version 2.6.27, created on 2016-12-01 16:30:39
         compiled from admin/page.top.php */ ?>
<!--<?php if ($this->_tpl_vars['page_info']['page_count'] > 1): ?>-->
<div class="page">
  <div class="flip_over">翻页: </div>
  <!--<?php if ($this->_tpl_vars['page_info']['prev_link']): ?>-->
  <a class="former" href="<?php echo $this->_tpl_vars['page_info']['prev_link']; ?>
"></a>
  <!--<?php else: ?>-->
  <span class="formerNull"></span>
  <!--<?php endif; ?>-->
  <!--<?php if ($this->_tpl_vars['page_info']['next_link']): ?>-->
  <a class="down" href="<?php echo $this->_tpl_vars['page_info']['next_link']; ?>
">下一页</a>
  <!--<?php else: ?>-->
  <span class="downNull">下一页</span>
  <!--<?php endif; ?>-->
</div>
<!--<?php endif; ?>-->