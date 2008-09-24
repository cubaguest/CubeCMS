<div>
<form method="post" action="{$THIS_PAGE_LINK}">
	{foreach from=$VARS.PHOTO_ARRAY key='LANG' item='PHOTO' name='photoLang'}
	{html_image file=$MAIN_LANG_IMAGES_PATH|cat:$LANG|cat:'.png'}<br />
	{$VARS.PHOTO_LABEL_NAME}{if $smarty.foreach.photoLang.first}*{/if}:<br />
	<input type="text" size="40" maxlength="50" name="photo_label_{$LANG}" value="{$PHOTO.photolabel}" /><br />
	{$VARS.PHOTO_TEXT_NAME}:<br />
	<textarea rows="5" cols="60" name="photo_text_{$LANG}">{$PHOTO.phototext}</textarea>
	<br />
	<br />
	
	{/foreach}
	<input name="photo_id" type="hidden" value="{$VARS.PHOTO_ID}" />
	
	<input name="photo_reset" type="reset" value="{$VARS.BUTTON_RESET}" />
	<input name="photo_send" type="submit" value="{$VARS.BUTTON_SEND}" />
	
</form>
<img src="{$VARS.GALERY_DIR_TO_SMALL_PHOTOS}{$VARS.PHOTO_FILE}" title="{$VARS.PHOTO_NAME}"/>
</div>