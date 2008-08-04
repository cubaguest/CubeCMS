<!--[if !IE ]>
<{**}?xml version="1.0" encoding="UTF-8" ?>
<![endif]-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="cs" lang="cs">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta http-equiv="expires" content="{$smarty.now+600|date_format:'%a %d %b %Y %T'}" />
	<title>{$MAIN_PAGE_TITLE} - {$MAIN_CATEGORY_TITLE}</title>

	<base href="{$MAIN_WEB_DIR}" />

	<link rel="stylesheet" type="text/css" href="./stylesheet/style.css" />
	<link rel="stylesheet" type="text/nesmysl" href="./stylesheet/style_opera.css" />

	<!--[if lte IE 6]>
	<link rel="stylesheet" type="text/css" media="all" href="./stylesheet/style_ie6.css" />
	<script type="text/javascript" src="./jscripts/fix_eolas.js" defer="defer"></script>
	<![endif]-->

	<!--Menu styly -->
	<link rel="stylesheet" type="text/css" href="./stylesheet/chromestyle.css" />
	<script type="text/javascript" src="./jscripts/chromejs/chrome.js" defer="defer"></script>

{* Vypis stylesheet modulu *}
{foreach from="$MODULES_STYLESHEET" item="stylesheet"}
	<link rel="stylesheet" type="text/css" href="{$stylesheet}" />
{/foreach}
{* Vypis stylesheet panelu *}
{foreach from="$PANELS_STYLESHEET" item="stylesheet"}
{foreach from="$stylesheet" item="item"}
	<link rel="stylesheet" type="text/css" href="{$item}" />
{/foreach}
{/foreach}
{* Vypis javascriptu modulu *}
{foreach from="$MODULES_JAVASCRIPT" item="jscript"}
	<script src="{$jscript}" type="text/javascript"></script>
{/foreach}


</head>

<body onload="{foreach from=$MODULES_JAVASCRIPT_ONLOAD_FUNCTION item='function'}{$function}; {/foreach}">

<div id="bodywrap">

 <div id="headwrap">
{if $smarty.now|date_format:"%k" gte 5 AND $smarty.now|date_format:"%k" lt 8}
{assign var='HEADER_IMAGE' value='hlavrano.jpg'}
{elseif $smarty.now|date_format:"%k" gte 8 AND $smarty.now|date_format:"%k" lt 12}
{assign var='HEADER_IMAGE' value='hlavdopol.jpg'}
{elseif $smarty.now|date_format:"%k" gte 12 AND $smarty.now|date_format:"%k" lt 18}
{assign var='HEADER_IMAGE' value='hlavodpol.jpg'}
{elseif $smarty.now|date_format:"%k" gte 18 AND $smarty.now|date_format:"%k" lt 21}
{assign var='HEADER_IMAGE' value='hlavvecer.jpg'}
{elseif $smarty.now|date_format:"%k" gte 21 OR $smarty.now|date_format:"%k" lt 5}
{assign var='HEADER_IMAGE' value='hlavnoc.jpg'}
{/if}
		<div class="strankahlavicka_image" style="background: url('./images/mestysy/{$HEADER_IMAGE}') no-repeat;">
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

				<img src="./images/mestysy/{$HEADER_IMAGE}" title="header" alt="header" width="1000px" height="150px" />
			</object>
		<!-- <![endif]-->
		</div>
		<a name="start_page"></a> <!-- PRESKAKOVANI POD FLASH-->
		{include file="menu.tpl"} <!-- vlozeni menu-->
	<hr class="separator" />
</div><!-- headwrap -->

<div id="colswrap">

	<div id="col1wrap" class="column">
		<div id="col1pad" class="{if $LEFT_PANEL eq true}col1pad_plusleft {/if}{if $RIGHT_PANEL eq true}col1pad_plusright {/if}">

		{* Vypis hlasek *}
		{if $MESSAGES neq null}
		<p class="message text">
		{html_image file='./images/icons/accept.png'}
		{foreach from=$MESSAGES key=MESSAGESNO item=MESSAGE}
		{$MESSAGE}<br />
		{/foreach}
		</p>
		{/if}

		{* Vypis chybovych hlasek ENGINU *}
		{if $CORE_ERRORS_EMPTY neq true}
		<p class="error_message text">
		{html_image file='./images/icons/coreerror.png'}
		{foreach from=$CORE_ERRORS key=ERRNO item=ERROR}
		{$ERROR_NAME}: ({$ERROR.code}) {$ERROR.name}. File: {$ERROR.file}. Line: {$ERROR.line}<br />
		{/foreach}
		</p>
		{/if}

		{* Vypis chybovych hlasek MODULU *}
		{if $ERRORS neq null}
		<p class="error_message text">
		{html_image file='./images/icons/error.png'}
		{foreach from=$ERRORS key=ERRNO item=ERROR}
		{if $DEBUG_MODE == true}
		{$ERROR_MODULE_NAME} {$ERROR}<br />
		{else}
		{$ERROR}<br />
		{/if}
		{/foreach}
		</p>
		{/if}

		{* Vypis sablon modulu *}
		{foreach from="$MODULES_TEMPLATES" item="template" key="TPLKEY" name ='mtemplates'}
		{* proměné v šablonách *}
		{assign var='VARS' value=$template.VARS}
		
		{* upravit *}
		{if $ITEM_NAME.$TPLKEY eq null AND $smarty.foreach.mtemplates.first}
		<h1>&gt;&gt;{$CATEGORY_TITLE}</h1>
		{else}
		<h1>&gt;&gt;{$ITEM_NAME.$TPLKEY}</h1>
		{/if}
		
		{foreach from=$template.templates item=tplfile}
		{include file=$tplfile}
		{/foreach}

		{/foreach}

		{* patří vymazatm bude v engine pluginech *}
		{* Zobrazení hodnocení je-li zapnuto *}
		{if $RATING_SHOW.$TPLKEY eq true}
		{include file="rating.htpl"}
		{/if}
		{* Zobrazení posunovátek jsou-li zapnuty *}
		{if $SCROLL_SHOW.$TPLKEY eq true}
		<div class="text_obsah">
		{include file="scroll.tpl"}
		</div>
		{/if}
		{* Zobrazení obrázků jsou-li zapnuty *}
		{if $IMAGES_SHOW.$TPLKEY eq true}
		{include file="images.htpl"}
		{/if}
		{* Zobrazení souborů jsou-li zapnuty *}
		{if $FILES_SHOW.$TPLKEY eq true}
		{include file="files.htpl"}
		{/if}
		{* Zobrazení komentářů jsou-li zapnuty *}
		{if $COMMENTS_SHOW.$TPLKEY eq true}
		{include file="comments.htpl"}
		{/if}

		



		<hr class="separator" />
	</div><!-- col1pad -->
</div><!-- col1wrap -->

{if $LEFT_PANEL eq true}
<div id="col2wrap" class="column">
	<div id="col2pad">
        {* Výpis menu s odkazy *}
		{if $LEFT_MENU_PANEL eq true}
		{foreach from=$LEFT_MENU_PANEL_ARRAY key=KEY item=ITEM}
		<div class="menu_bar">
			<h3>{$ITEM.section}</h3>
			<div class="menu_bar_obsah">
			{foreach from=$ITEM item=MENUITEM}
			{if is_array($MENUITEM)}
				{if $MENUITEM.url neq null}
				<a href="{$MENUITEM.url}" title="{$MENUITEM.name}">{$MENUITEM.name}</a><br />
				{else}
				<span>{$MENUITEM.name}</span>
				{/if}
			{/if}
			{/foreach}
			</div>
		</div>
		{/foreach}
		{/if}

		{* Výpis panelů modulů *}
		{if $MODULES_LEFT_PANEL eq true}
		{foreach from=$MODULES_LEFT_PANEL_ARRAY item=VALUE}
		<div class="menu_bar">
			<h3>{$VALUE.panel_name}</h3>
			{if $VALUE.values.module neq null}
			{include file=$VALUE.values.module_tpl_path}
			{/if}
		</div>
		{/foreach}
		{/if}
    <hr class="separator" />
	</div><!-- col2pad -->
</div><!-- col2wrap -->
{/if}

{if $RIGHT_PANEL eq true}
<div id="col3wrap" class="column">
	<div id="col3pad">
		{* Vypis svatku*}
		<div class="menu_bar">
			<div class="menu_bar_obsah">
			{$TODAY_IS} - {$DATE}<br />
			{$NAME_DAY_NAME} {$NAME_DAY}
			</div>
		</div>

		{* Výpis menu s odkazy *}
		{if $RIGHT_MENU_PANEL eq true}
		{foreach from=$RIGHT_MENU_PANEL_ARRAY key=KEY item=ITEM}
		<div class="menu_bar">
			<h3>{$ITEM.section}</h3>
			<div class="menu_bar_obsah">
			{foreach from=$ITEM item=MENUITEM}
			{if is_array($MENUITEM)}
				{if $MENUITEM.url neq null}
				<a href="{$MENUITEM.url}" title="{$MENUITEM.name}">{$MENUITEM.name}</a><br />
				{else}
				<span>{$MENUITEM.name}</span>
				{/if}
			{/if}
			{/foreach}
			</div>
		</div>
		{/foreach}
		{/if}

		{* Výpis panelů modulů *}
		{if $MODULES_RIGHT_PANEL eq true}
		{foreach from=$MODULES_RIGHT_PANEL_ARRAY item=VALUE}
		<div class="menu_bar">
			<h3>{$VALUE.panel_name}</h3>
			{if ($VALUE.values.module neq null and $VALUE.values.module_tpl_path neq null)}
			{include file=$VALUE.values.module_tpl_path}
			{/if}
		</div>
		{/foreach}
		{/if}
    <hr class="separator" />

	</div><!-- col3pad -->
</div><!-- col3wrap -->
{/if}

<div class="reseter">&nbsp;</div>

</div><!-- colswrap -->

<div class="reseter">&nbsp;</div>

<div id="footwrap">
		{include file="login.tpl"}
		Generated:	{$smarty.now|date_format:'%H:%M:%S %d.%m.%Y'} Powered by <a href="http://www.gentoo.org" title="Gentoo">GENTOO</a>,
		<a href="http://www.vypecky.info" title="Vepřové Výpečky">Vypecky.info engine ver. {$ENGINE_VERSION}</a>, <a href="http://www.php.net" title="PHP">PHP</a>,
		<a href="http://www.mysql.com" title="MySQL">MySQL</a>, <a href="http://www.smarty.net" title="Smarty Templates">Smarty</a>.
		Script generated by {$MAIN_EXEC_TIME}s, SQL queries: {$COUNT_ALL_SQL_QUERY}
		<!-- ABZ rychle pocitadlo -->
			<a href="http://pocitadlo.abz.cz/" title="počítadlo přístupů: pocitadlo.abz.cz"><img src="http://pocitadlo.abz.cz/aip.php?tp=di" alt="počítadlo.abz.cz" border="0" /></a>
		<!-- http://pocitadlo.abz.cz/ -->
	<hr class="separator" />
</div><!-- footwrap -->

</div><!-- bodywrap /-->

  </body>

</html>