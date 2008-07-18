<?php /* Smarty version 2.6.19, created on 2008-07-09 23:07:01
         compiled from menu.tpl */ ?>
<!-- Sekce v menu  -->
<div class="chromestyle" id="chromemenu">
<ul>
<?php $_from = $this->_tpl_vars['MAIN_MENU_SECTION_ARRAY']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['KEY'] => $this->_tpl_vars['ITEM']):
?>
	<li>
		<?php if ($this->_tpl_vars['ITEM']['submenu'] == true): ?>
		<a href="#" rel="dropmenu<?php echo $this->_tpl_vars['KEY']; ?>
" title="<?php echo $this->_tpl_vars['ITEM']['salt']; ?>
"><?php echo $this->_tpl_vars['ITEM']['slabel']; ?>
</a>
		<?php else: ?>
		<a href="<?php echo $this->_tpl_vars['ITEM']['url']; ?>
" title="<?php echo $this->_tpl_vars['ITEM']['alt']; ?>
"><?php echo $this->_tpl_vars['ITEM']['clabel']; ?>
</a>
		<?php endif; ?>
	</li>
<?php endforeach; endif; unset($_from); ?>
</ul>
</div>

<!-- Kategorie v podmenu -->
<?php $_from = $this->_tpl_vars['MAIN_MENU_CATEGORY_ARRAY']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['submenu'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['submenu']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['KEY'] => $this->_tpl_vars['ITEM']):
        $this->_foreach['submenu']['iteration']++;
?>
<div id="dropmenu<?php echo $this->_tpl_vars['KEY']; ?>
" class="dropmenudiv">
<?php $_from = $this->_tpl_vars['ITEM']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['KEY2'] => $this->_tpl_vars['MENUITEM']):
?>
<a href="<?php echo $this->_tpl_vars['MENUITEM']['url']; ?>
" title="<?php echo $this->_tpl_vars['MENUITEM']['alt']; ?>
"><?php echo $this->_tpl_vars['MENUITEM']['clabel']; ?>
</a>
<?php endforeach; endif; unset($_from); ?>
</div>
<?php endforeach; endif; unset($_from); ?>

<script type="text/javascript">
cssdropdown.startchrome("chromemenu")
</script> 