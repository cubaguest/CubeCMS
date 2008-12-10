
<div>
<ul id="langstabs" class="shadetabs">
{foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
<li><a href="#" rel="lang{$smarty.foreach.lang.iteration}" {if $smarty.foreach.lang.first}class="selected"{/if}>{html_engine_image file=$MAIN_LANG_IMAGES_PATH|cat:$KEYLANG|cat:'.png'}{$LANG}</a></li>
{/foreach}
</ul>
<div class="tabsContent">
<form method="post" action="{$THIS_PAGE_LINK}">
	{foreach from=$VARS.TEXT_EDIT_ARRAY key='LANG' item='TEXT' name='textLang'}
	<p id="lang{$smarty.foreach.textLang.iteration}" class="tabcontent">
		{$VARS.TEXT_NAME}{if $smarty.foreach.textLang.first}*{/if}:<br />
		<textarea class="textarea_tinymce textarea" rows="5" cols="60" name="text_text_{$LANG}">{$TEXT.text}</textarea>
	</p>
	{* html_engine_image file=$MAIN_LANG_IMAGES_PATH|cat:$LANG|cat:'.png' *}
	{/foreach}
	<input name="text_in_db" type="hidden" value="{$VARS.TEXT_IN_DB}" />
	<br />
	<input name="text_reset" type="reset" value="{$VARS.BUTTON_RESET}" />
	<input name="text_send" type="submit" value="{$VARS.BUTTON_TEXT_SEND}" />
	
</form>
</div>
<script type="text/javascript">
var langs=new ddtabcontent("langstabs");
langs.setpersist(true);
langs.setselectedClassTarget("link"); //"link" or "linkparent"
langs.init();
</script>
</div>