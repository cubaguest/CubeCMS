<div>
<form action="{$THIS_PAGE_LINK}" method="post" enctype="multipart/form-data">
	<fieldset>
	<legend>{$VARS.ADD_TO_GALERY}</legend>
	{html_options name=galery_id options=$VARS.SELECT_GALERY selected=$VARS.SELECTED_GALERY_ID}<br />
	</fieldset>

	<fieldset>
	<legend>{$VARS.ADD_PHOTO}</legend>
{foreach from=$VARS.PHOTO_ARRAY key='LANG' item='PHOTO' name='photoLang'}
	{html_image file=$MAIN_LANG_IMAGES_PATH|cat:$LANG|cat:'.png'}<br />
	{$VARS.PHOTO_LABEL_NAME}{if $smarty.foreach.photoLang.first}*{/if}:<br />
	<input type="text" size="40" maxlength="50" name="photo_label_{$LANG}" value="{$PHOTO.photolabel}" /><br />
	{$VARS.PHOTO_TEXT_NAME}:<br />
	<textarea rows="5" cols="60" name="photo_text_{$LANG}">{$PHOTO.phototext}</textarea>
	<br />
{/foreach}
	<label>{$VARS.PHOTO_FILE_NAME}:</label><br />
	<input type="file" name="photo_file" value="" size="40" />
	</fieldset>
	
	<input name="photo_reset" type="reset" value="{$VARS.BUTTON_RESET}" />
	<input name="photo_send" type="submit" value="{$VARS.BUTTON_SEND}" />

<fieldset>
	<legend>{$VARS.ADD_NEW_SECTION_OR_GALERY}</legend>
	<fieldset>
	<legend>{$VARS.SELECT_SECTION_NAME}</legend>
	<label>{$VARS.ADD_TO_EXIST_SECTION}:</label><br />
	{html_options name=section_id options=$VARS.SELECT_SECTION selected=$VARS.SELECTED_SECTION}<br />
	<br />
	<label>{$VARS.CREATE_NEW_SECTION_NAME}</label><br />
{foreach from=$VARS.CREATE_NEW_SECTION key='LANG' item='SECTION' name='sectionLang'}
	{html_image file=$MAIN_LANG_IMAGES_PATH|cat:$LANG|cat:'.png'}<br />
	{$VARS.SECTION_LABEL_NAME}{if $smarty.foreach.sectionLang.first}*{/if}:<br />
	<input type="text" size="40" maxlength="50" name="section_name_{$LANG}" value="" /><br />
{/foreach}
	<br />
	</fieldset>
	<fieldset>
	<legend>{$VARS.CREATE_NEW_GALERY_NAME}</legend><br />
{foreach from=$VARS.CREATE_NEW_GALERY key='LANG' item='GALERY' name='galeryLang'}
	{html_image file=$MAIN_LANG_IMAGES_PATH|cat:$LANG|cat:'.png'}<br />
	{$VARS.GALERY_LABEL_NAME}{if $smarty.foreach.galeryLang.first}*{/if}:<br />
	<input type="text" size="40" maxlength="50" name="galery_label_{$LANG}" value="{$GALERY.galerylabel}" /><br />
	{$VARS.GALERY_TEXT_NAME}:<br />
	<textarea rows="5" cols="60" name="galery_text_{$LANG}">{$GALERY.galerytext}</textarea>
	<br />
{/foreach}
	</fieldset>
</fieldset>

</form>
</div>