<?php /* Smarty version 2.6.19, created on 2008-07-09 23:07:01
         compiled from index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'index.tpl', 10, false),)), $this); ?>
<!--[if !IE ]>
<?php echo '<?' ?>
xml version="1.0" encoding="UTF-8" <?php echo '?>'; ?>

<![endif]-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="cs" lang="cs">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta http-equiv="expires" content="<?php echo ((is_array($_tmp=time()+600)) ? $this->_run_mod_handler('date_format', true, $_tmp, '%a %d %b %Y %T') : smarty_modifier_date_format($_tmp, '%a %d %b %Y %T')); ?>
" />
	<title><?php echo $this->_tpl_vars['PAGE_TITLE']; ?>
 -- <?php echo $this->_tpl_vars['CATEGORY_TITLE']; ?>
</title>

	<base href="http://www.dev.vypecky.info/vypeckyengine_3/" />

	<link rel="stylesheet" type="text/css" href="./stylesheet/style.css" />
	<link rel="stylesheet" type="text/nesmysl" href="./stylesheet/style_opera.css" />

	<!--[if lte IE 6]>
	<link rel="stylesheet" type="text/css" media="all" href="./stylesheet/style_ie6.css" />
	<script type="text/javascript" src="./jscripts/fix_eolas.js" defer="defer"></script>
	<![endif]-->

	<!--Menu styly -->
	<link rel="stylesheet" type="text/css" href="./stylesheet/chromestyle.css" />
	<script type="text/javascript" src="./jscripts/chromejs/chrome.js" defer="defer"></script>

<?php $_from = ($this->_tpl_vars['MODULES_STYLESHEET']); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['stylesheet']):
?>
	<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['stylesheet']; ?>
" />
<?php endforeach; endif; unset($_from); ?>
<?php $_from = ($this->_tpl_vars['PANELS_STYLESHEET']); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['stylesheet']):
?>
<?php $_from = ($this->_tpl_vars['stylesheet']); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
	<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['item']; ?>
" />
<?php endforeach; endif; unset($_from); ?>
<?php endforeach; endif; unset($_from); ?>
<?php $_from = ($this->_tpl_vars['MODULES_JAVASCRIPT']); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['jscript']):
?>
	<script src="<?php echo $this->_tpl_vars['jscript']; ?>
" type="text/javascript"></script>
<?php endforeach; endif; unset($_from); ?>


</head>

<body onload="<?php $_from = $this->_tpl_vars['MODULES_JAVASCRIPT_ONLOAD_FUNCTION']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['function']):
?><?php echo $this->_tpl_vars['function']; ?>
; <?php endforeach; endif; unset($_from); ?>">

<div id="bodywrap">

 <div id="headwrap">
<?php if (((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%k") : smarty_modifier_date_format($_tmp, "%k")) >= 5 && ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%k") : smarty_modifier_date_format($_tmp, "%k")) < 8): ?>
<?php $this->assign('HEADER_IMAGE', 'hlavrano.jpg'); ?>
<?php elseif (((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%k") : smarty_modifier_date_format($_tmp, "%k")) >= 8 && ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%k") : smarty_modifier_date_format($_tmp, "%k")) < 12): ?>
<?php $this->assign('HEADER_IMAGE', 'hlavdopol.jpg'); ?>
<?php elseif (((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%k") : smarty_modifier_date_format($_tmp, "%k")) >= 12 && ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%k") : smarty_modifier_date_format($_tmp, "%k")) < 18): ?>
<?php $this->assign('HEADER_IMAGE', 'hlavodpol.jpg'); ?>
<?php elseif (((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%k") : smarty_modifier_date_format($_tmp, "%k")) >= 18 && ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%k") : smarty_modifier_date_format($_tmp, "%k")) < 21): ?>
<?php $this->assign('HEADER_IMAGE', 'hlavvecer.jpg'); ?>
<?php elseif (((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%k") : smarty_modifier_date_format($_tmp, "%k")) >= 21 || ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%k") : smarty_modifier_date_format($_tmp, "%k")) < 5): ?>
<?php $this->assign('HEADER_IMAGE', 'hlavnoc.jpg'); ?>
<?php endif; ?>
		<div class="strankahlavicka_image" style="background: url('./images/mestysy/<?php echo $this->_tpl_vars['HEADER_IMAGE']; ?>
') no-repeat;">
		<!--[if !IE]> -->
			<object type="application/x-shockwave-flash" data="./images/flash/header.swf" width="1000px" height="150px">

		<!-- <![endif]-->

		<!--[if IE]>
			<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="1000px" height="150px">
				<param name="movie" value="./images/flash/header.swf" />
				<!--><!--dgx-->
  				<param name="loop" value="true" />
  				<param name="menu" value="false" />
				<param name="wmode" value="transparent" />
				<param name="scale" value="noborder" />

		<!--  		Alternativni obsah-->

				<img src="./images/mestysy/<?php echo $this->_tpl_vars['HEADER_IMAGE']; ?>
" title="header" alt="header" width="1000px" height="150px" />
			</object>
		<!-- <![endif]-->
		</div>
		<a name="start_page"></a> <!-- PRESKAKOVANI POD FLASH-->
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "menu.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> <!-- vlozeni menu-->
	<hr class="separator" />
</div><!-- headwrap -->

<div id="colswrap">

	<div id="col1wrap" class="column">
		<div id="col1pad" class="<?php if ($this->_tpl_vars['LEFT_PANEL'] == true): ?>col1pad_plusleft <?php endif; ?><?php if ($this->_tpl_vars['RIGHT_PANEL'] == true): ?>col1pad_plusright <?php endif; ?>">

		<!-- <h1>&gt;&gt;<?php echo $this->_tpl_vars['CATEGORY_TITLE']; ?>
</h1> -->

				<?php if (count ( $this->_tpl_vars['MESSAGES'] ) > 0): ?>
		<div class="container">
		<p class="message text">
		<?php $_from = $this->_tpl_vars['MESSAGES']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['MESSAGESNO'] => $this->_tpl_vars['MESSAGE']):
?>
		<?php echo $this->_tpl_vars['MESSAGE']; ?>
<br />
		<?php endforeach; endif; unset($_from); ?>
		</p>
		</div>
		<?php endif; ?>
				<?php if (count ( $this->_tpl_vars['CORE_ERRORS'] ) > 0): ?>
		<div class="container">
		<p class="error_message text">
		<?php $_from = $this->_tpl_vars['CORE_ERRORS']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['ERRNO'] => $this->_tpl_vars['ERROR']):
?>
		<?php echo $this->_tpl_vars['ERROR_NAME']; ?>
: (<?php echo $this->_tpl_vars['ERROR']['code']; ?>
) <?php echo $this->_tpl_vars['ERROR']['name']; ?>
. File: <?php echo $this->_tpl_vars['ERROR']['file']; ?>
. Line: <?php echo $this->_tpl_vars['ERROR']['line']; ?>
<br />
		<?php endforeach; endif; unset($_from); ?>
		</p>
		</div>
		<?php endif; ?>
				<?php if (count ( $this->_tpl_vars['MODULE_ERRORS'] ) > 0): ?>
		<div class="container">
		<p class="error_message text">
		<?php $_from = $this->_tpl_vars['MODULE_ERRORS']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['ERRNO'] => $this->_tpl_vars['ERROR']):
?>
		<?php if ($this->_tpl_vars['DEBUG_MODE'] == true): ?>
		<?php echo $this->_tpl_vars['ERROR_MODULE_NAME']; ?>
 <?php echo $this->_tpl_vars['ERROR']['prefix']; ?>
:(<?php echo $this->_tpl_vars['ERRNO']; ?>
): <?php echo $this->_tpl_vars['ERROR']['value']; ?>
<br />
		<?php else: ?>
		<?php echo $this->_tpl_vars['ERROR']; ?>
<br />
		<?php endif; ?>
		<?php endforeach; endif; unset($_from); ?>
		</p>
		</div>
		<?php endif; ?>



				<?php $_from = ($this->_tpl_vars['MODULES_TEMPLATES']); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['mtemplates'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['mtemplates']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['TPLKEY'] => $this->_tpl_vars['template']):
        $this->_foreach['mtemplates']['iteration']++;
?>
		<?php if ($this->_tpl_vars['ITEM_NAME'][$this->_tpl_vars['TPLKEY']] == null && ($this->_foreach['mtemplates']['iteration'] <= 1)): ?>
		<h1>&gt;&gt;<?php echo $this->_tpl_vars['CATEGORY_TITLE']; ?>
</h1>
		<?php else: ?>
		<h1>&gt;&gt;<?php echo $this->_tpl_vars['ITEM_NAME'][$this->_tpl_vars['TPLKEY']]; ?>
</h1>
		<?php endif; ?>

		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['template']), 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

				<?php if ($this->_tpl_vars['RATING_SHOW'][$this->_tpl_vars['TPLKEY']] == true): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "rating.htpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php endif; ?>

				<?php if ($this->_tpl_vars['SCROLL_SHOW'][$this->_tpl_vars['TPLKEY']] == true): ?>
		<div class="text_obsah">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "scroll.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>
		<?php endif; ?>
				<?php if ($this->_tpl_vars['IMAGES_SHOW'][$this->_tpl_vars['TPLKEY']] == true): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "images.htpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php endif; ?>
				<?php if ($this->_tpl_vars['FILES_SHOW'][$this->_tpl_vars['TPLKEY']] == true): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "files.htpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php endif; ?>

				<?php if ($this->_tpl_vars['COMMENTS_SHOW'][$this->_tpl_vars['TPLKEY']] == true): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "comments.htpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php endif; ?>

		<?php endforeach; endif; unset($_from); ?>



		<hr class="separator" />
	</div><!-- col1pad -->
</div><!-- col1wrap -->

<?php if ($this->_tpl_vars['LEFT_PANEL'] == true): ?>
<div id="col2wrap" class="column">
	<div id="col2pad">
        		<?php if ($this->_tpl_vars['LEFT_MENU_PANEL'] == true): ?>
		<?php $_from = $this->_tpl_vars['LEFT_MENU_PANEL_ARRAY']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['KEY'] => $this->_tpl_vars['ITEM']):
?>
		<div class="menu_bar">
			<h3><?php echo $this->_tpl_vars['ITEM']['section']; ?>
</h3>
			<div class="menu_bar_obsah">
			<?php $_from = $this->_tpl_vars['ITEM']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['MENUITEM']):
?>
			<?php if (is_array ( $this->_tpl_vars['MENUITEM'] )): ?>
				<?php if ($this->_tpl_vars['MENUITEM']['url'] != null): ?>
				<a href="<?php echo $this->_tpl_vars['MENUITEM']['url']; ?>
" title="<?php echo $this->_tpl_vars['MENUITEM']['name']; ?>
"><?php echo $this->_tpl_vars['MENUITEM']['name']; ?>
</a><br />
				<?php else: ?>
				<span><?php echo $this->_tpl_vars['MENUITEM']['name']; ?>
</span>
				<?php endif; ?>
			<?php endif; ?>
			<?php endforeach; endif; unset($_from); ?>
			</div>
		</div>
		<?php endforeach; endif; unset($_from); ?>
		<?php endif; ?>

				<?php if ($this->_tpl_vars['MODULES_LEFT_PANEL'] == true): ?>
		<?php $_from = $this->_tpl_vars['MODULES_LEFT_PANEL_ARRAY']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['VALUE']):
?>
		<div class="menu_bar">
			<h3><?php echo $this->_tpl_vars['VALUE']['panel_name']; ?>
</h3>
			<?php if ($this->_tpl_vars['VALUE']['values']['module'] != null): ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['VALUE']['values']['module_tpl_path'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php endif; ?>
		</div>
		<?php endforeach; endif; unset($_from); ?>
		<?php endif; ?>
    <hr class="separator" />
	</div><!-- col2pad -->
</div><!-- col2wrap -->
<?php endif; ?>

<?php if ($this->_tpl_vars['RIGHT_PANEL'] == true): ?>
<div id="col3wrap" class="column">
	<div id="col3pad">
				<div class="menu_bar">
			<div class="menu_bar_obsah">
			<?php echo $this->_tpl_vars['TODAY_IS']; ?>
 - <?php echo $this->_tpl_vars['DATE']; ?>
<br />
			<?php echo $this->_tpl_vars['NAME_DAY_NAME']; ?>
 <?php echo $this->_tpl_vars['NAME_DAY']; ?>

			</div>
		</div>

				<?php if ($this->_tpl_vars['RIGHT_MENU_PANEL'] == true): ?>
		<?php $_from = $this->_tpl_vars['RIGHT_MENU_PANEL_ARRAY']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['KEY'] => $this->_tpl_vars['ITEM']):
?>
		<div class="menu_bar">
			<h3><?php echo $this->_tpl_vars['ITEM']['section']; ?>
</h3>
			<div class="menu_bar_obsah">
			<?php $_from = $this->_tpl_vars['ITEM']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['MENUITEM']):
?>
			<?php if (is_array ( $this->_tpl_vars['MENUITEM'] )): ?>
				<?php if ($this->_tpl_vars['MENUITEM']['url'] != null): ?>
				<a href="<?php echo $this->_tpl_vars['MENUITEM']['url']; ?>
" title="<?php echo $this->_tpl_vars['MENUITEM']['name']; ?>
"><?php echo $this->_tpl_vars['MENUITEM']['name']; ?>
</a><br />
				<?php else: ?>
				<span><?php echo $this->_tpl_vars['MENUITEM']['name']; ?>
</span>
				<?php endif; ?>
			<?php endif; ?>
			<?php endforeach; endif; unset($_from); ?>
			</div>
		</div>
		<?php endforeach; endif; unset($_from); ?>
		<?php endif; ?>

				<?php if ($this->_tpl_vars['MODULES_RIGHT_PANEL'] == true): ?>
		<?php $_from = $this->_tpl_vars['MODULES_RIGHT_PANEL_ARRAY']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['VALUE']):
?>
		<div class="menu_bar">
			<h3><?php echo $this->_tpl_vars['VALUE']['panel_name']; ?>
</h3>
			<?php if (( $this->_tpl_vars['VALUE']['values']['module'] != null && $this->_tpl_vars['VALUE']['values']['module_tpl_path'] != null )): ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['VALUE']['values']['module_tpl_path'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php endif; ?>
		</div>
		<?php endforeach; endif; unset($_from); ?>
		<?php endif; ?>
    <hr class="separator" />

	</div><!-- col3pad -->
</div><!-- col3wrap -->
<?php endif; ?>

<div class="reseter">&nbsp;</div>

</div><!-- colswrap -->

<div class="reseter">&nbsp;</div>

<div id="footwrap">
		Generated:	<?php echo ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, '%H:%M:%S %d.%m.%Y') : smarty_modifier_date_format($_tmp, '%H:%M:%S %d.%m.%Y')); ?>
 Powered by <a href="http://www.gentoo.org" title="Gentoo">GENTOO</a>,
		<a href="http://www.vypecky.info" title="Vepřové Výpečky">Vypecky.info engine ver. <?php echo $this->_tpl_vars['ENGINE_VERSION']; ?>
</a>, <a href="http://www.php.net" title="PHP">PHP</a>,
		<a href="http://www.mysql.com" title="MySQL">MySQL</a>, <a href="http://www.smarty.net" title="Smarty Templates">Smarty</a>.
		Script generated by <?php echo $this->_tpl_vars['MAIN_EXEC_TIME']; ?>
s, SQL queries: <?php echo $this->_tpl_vars['COUNT_ALL_SQL_QUERY']; ?>

		<!-- ABZ rychle pocitadlo -->
			<a href="http://pocitadlo.abz.cz/" title="počítadlo přístupů: pocitadlo.abz.cz"><img src="http://pocitadlo.abz.cz/aip.php?tp=di" alt="počítadlo.abz.cz" border="0" /></a>
		<!-- http://pocitadlo.abz.cz/ -->
	<hr class="separator" />
</div><!-- footwrap -->

</div><!-- bodywrap /-->

  </body>

</html>