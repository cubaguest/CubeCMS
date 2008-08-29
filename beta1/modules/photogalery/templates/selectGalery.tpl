<fieldset>
<legend>{$VARS.SELECT_GALERY_NAME}</legend>
	{html_options name=section_id options=$VARS.SELECT_GALERY selected=$VARS.SELECTED_GALERY}<br />
	<br />
	<label>{$VARS.CREATE_NEW_GALERY_NAME}</label><br />
	{foreach from=$VARS.CREATE_NEW_GALERY key='LANG' item='GALERY' name='galeryLang'}
	{html_image file=$MAIN_LANG_IMAGES_PATH|cat:$LANG|cat:'.png'}<br />
	{$VARS.GALERY_LABEL_NAME}{if $smarty.foreach.galeryLang.first}*{/if}:<br />
	<input type="text" size="40" maxlength="50" name="galery_label_{$LANG}" value="{$GALERY.galerylabel}" /><br />
	{$VARS.GALERY_TEXT_NAME}{if $smarty.foreach.galeryLang.first}*{/if}:<br />
	<textarea rows="5" cols="60" name="galery_text_{$LANG}">{$GALERY.galerytext}</textarea>
	<br />
{/foreach}
</fieldset>