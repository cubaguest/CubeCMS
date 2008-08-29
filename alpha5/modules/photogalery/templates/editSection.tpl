<div>
<form action="{$THIS_PAGE_LINK}" method="post">
	<fieldset>
	<legend>{$VARS.SECTION_FIELDSET_NAME}</legend>
	{foreach from=$VARS.SECTION_ARRAY key='LANG' item='SECTION' name='sectionsLang'}
	{html_image file=$MAIN_LANG_IMAGES_PATH|cat:$LANG|cat:'.png'}<br />
	<label>{$VARS.SECTION_LABEL_NAME}:{if $smarty.foreach.sectionsLang.first}*{/if}</label><br />
	<input type="text" name="section_name_{$LANG}" value="{$SECTION}" size="40" maxlength="50" /><br />
	{/foreach}
	<input type="submit" name="section_send" value="{$VARS.BUTTON_SEND}" />
	</fieldset>
</form>
</div>