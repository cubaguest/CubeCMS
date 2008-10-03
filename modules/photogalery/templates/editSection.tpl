<div>
<ul id="langstabs" class="shadetabs">
{foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
<li><a href="#" rel="lang{$smarty.foreach.lang.iteration}" {if $smarty.foreach.lang.first}class="selected"{/if}>{html_engine_image file=$MAIN_LANG_IMAGES_PATH|cat:$KEYLANG|cat:'.png'}{$LANG}</a></li>
{/foreach}
</ul>
<div class="tabsContent">
<form action="{$THIS_PAGE_LINK}" method="post">
	{foreach from=$VARS.SECTION_ARRAY key='LANG' item='SECTION' name='sectionsLang'}
	<p id="lang{$smarty.foreach.sectionsLang.iteration}" class="tabcontent">
	<label>{$VARS.SECTION_LABEL_NAME}:{if $smarty.foreach.sectionsLang.first}*{/if}</label><br />
	<input type="text" name="section_label_{$LANG}" value="{$SECTION.label}" size="40" maxlength="50" /><br />
	</p>
	{/foreach}
	<input type="submit" name="section_send" value="{$VARS.BUTTON_SEND}" />
</form>
</div>
<script type="text/javascript">
var langs=new ddtabcontent("langstabs");
langs.setpersist(true);
langs.setselectedClassTarget("link"); //"link" or "linkparent"
langs.init();
</script>
</div>