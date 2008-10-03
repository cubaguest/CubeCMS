<div>
<ul id="langstabs" class="shadetabs">
{foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
<li><a href="#" rel="lang{$smarty.foreach.lang.iteration}" {if $smarty.foreach.lang.first}class="selected"{/if}>{html_engine_image file=$MAIN_LANG_IMAGES_PATH|cat:$KEYLANG|cat:'.png'}{$LANG}</a></li>
{/foreach}
</ul>
<div class="tabsContent">
<form action="{$THIS_PAGE_LINK}" method="post">
	
	{foreach from=$VARS.GALERY_ARRAY key='LANG' item='GALERY' name='galeryLang'}
	<p id="lang{$smarty.foreach.galeryLang.iteration}" class="tabcontent">
	{$VARS.GALERY_LABEL_NAME}{if $smarty.foreach.galeryLang.first}*{/if}:<br />
	<input type="text" size="40" maxlength="50" name="galery_label_{$LANG}" value="{$GALERY.label}" /><br />
	{$VARS.GALERY_TEXT_NAME}:<br />
	<textarea rows="5" cols="60" name="galery_text_{$LANG}">{$GALERY.text}</textarea>
	</p>
	
	{/foreach}
	<br />
	{$VARS.SECTIONS_SELECT_NAME}:
	{html_options name=galery_section_id options=$VARS.SECTIONS selected=$VARS.SECTION_SELECT}<br />
	
	{if $VARS.GALERY_ID neq null}
	<input name="galery_id" type="hidden" value="{$VARS.GALERY_ID}" />
	{/if}
	<br />
	<input name="galery_reset" type="reset" value="{$VARS.BUTTON_RESET}" />
	<input name="galery_send" type="submit" value="{$VARS.BUTTON_SEND}" />
	
</form>
</div>
<script type="text/javascript">
var langs=new ddtabcontent("langstabs");
langs.setpersist(true);
langs.setselectedClassTarget("link"); //"link" or "linkparent"
langs.init();
</script>
</div>