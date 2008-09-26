<div>
<form method="post" action="{$THIS_PAGE_LINK}">
	{foreach from=$VARS.TEXT_EDIT_ARRAY key='LANG' item='TEXT' name='textLang'}
	{html_engine_image file=$MAIN_LANG_IMAGES_PATH|cat:$LANG|cat:'.png'}<br />
	{$VARS.TEXT_NAME}{if $smarty.foreach.textLang.first}*{/if}:<br />
	<textarea rows="5" cols="60" name="text_text_{$LANG}">{$TEXT}</textarea>
	<br />
	<br />
	
	{/foreach}
	<input name="text_in_db" type="hidden" value="{$VARS.TEXT_IN_DB}" />
	
	<input name="text_reset" type="reset" value="{$VARS.BUTTON_RESET}" />
	<input name="text_send" type="submit" value="{$VARS.BUTTON_TEXT_SEND}" />
	
</form>
</div>