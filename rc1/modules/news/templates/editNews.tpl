<div>
<form method="post" action="{$THIS_PAGE_LINK}">
	{foreach from=$VARS.NEWS_EDIT_ARRAY key='LANG' item='NEWS' name='newsLang'}
	{html_image file=$MAIN_LANG_IMAGES_PATH|cat:$LANG|cat:'.png'}<br />
	{$VARS.NEWS_LABEL_NAME}{if $smarty.foreach.newsLang.first}*{/if}:<br />
	<input type="text" size="40" maxlength="50" name="news_label_{$LANG}" value="{$NEWS.label}" /><br />
	{$VARS.NEWS_TEXT_NAME}{if $smarty.foreach.newsLang.first}*{/if}:<br />
	<textarea rows="5" cols="60" name="news_text_{$LANG}">{$NEWS.text}</textarea>
	<br />
	<br />
	
	{/foreach}
	{if $VARS.SELECTED_ID_NEWS neq null}
	<input name="news_id" type="hidden" value="{$VARS.SELECTED_ID_NEWS}" />
	{/if}
	
	<input name="news_reset" type="reset" value="{$VARS.BUTTON_RESET}" />
	<input name="news_send" type="submit" value="{$VARS.BUTTON_SEND}" />
	
</form>
</div>