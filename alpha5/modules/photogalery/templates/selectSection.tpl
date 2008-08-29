<fieldset>
<legend>{$VARS.SELECT_SECTION_NAME}</legend>
	{html_options name=section_id options=$VARS.SELECT_SECTION selected=$VARS.SELECTED_SECTION}<br />
	<br />
	<label>{$VARS.CREATE_NEW_SECTION_NAME}</label><br />
	{foreach from=$VARS.CREATE_NEW_SECTION key='LANG' item='SECTION' name='sectionLang'}
	{html_image file=$MAIN_LANG_IMAGES_PATH|cat:$LANG|cat:'.png'}<br />
	{$VARS.SECTION_LABEL_NAME}{if $smarty.foreach.sectionLang.first}*{/if}:<br />
	<input type="text" size="40" maxlength="50" name="section_name_{$LANG}" value="" /><br />
	<br />
{/foreach}
</fieldset>