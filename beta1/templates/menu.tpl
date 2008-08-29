<!-- Sekce v menu  -->
<div class="chromestyle" id="chromemenu">
<ul>
{foreach from=$MAIN_MENU.SECTION_ARRAY key=KEY item=ITEM}
	<li>
		{if $ITEM.submenu eq true}
		<a href="#" rel="dropmenu{$KEY}" title="{$ITEM.salt}">{$ITEM.slabel}</a>
		{else}
		<a href="{$ITEM.url}" title="{$ITEM.alt}">{$ITEM.clabel}</a>
		{/if}
	</li>
{/foreach}
</ul>
</div>

<!-- Kategorie v podmenu -->
{foreach from=$MAIN_MENU.CATEGORY_ARRAY key=KEY item=ITEM name=submenu}
<div id="dropmenu{$KEY}" class="dropmenudiv">
{foreach from=$ITEM key=KEY2 item=MENUITEM}
<a href="{$MENUITEM.url}" title="{$MENUITEM.alt}">{$MENUITEM.clabel}</a>
{/foreach}
</div>
{/foreach}

<script type="text/javascript">
cssdropdown.startchrome("chromemenu")
</script> 
