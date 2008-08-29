<div>
<form action="{$THIS_PAGE_LINK}" method="post" enctype="multipart/form-data">
	{foreach from=$VARS.SPONSOR_EDIT_ARRAY key='LANG' item='SPONSOR' name='sponsorLang'}
	{html_image file=$MAIN_LANG_IMAGES_PATH|cat:$LANG|cat:'.png'}<br />
	{$VARS.SPONSOR_NAME}{if $smarty.foreach.sponsorLang.first}*{/if}:<br />
	<input type="text" size="40" maxlength="50" name="sponsor_name_{$LANG}" value="{$SPONSOR.name}" /><br />
	{$VARS.SPONSOR_LABEL}:<br />
	<textarea rows="5" cols="60" name="sponsor_label_{$LANG}">{$SPONSOR.label}</textarea>
	<br />
	{/foreach}
	{$VARS.SPONSOR_URL_NAME}:<br />
	<input type="text" size="40" maxlength="50" name="sponsor_url" value="{$VARS.SPONSOR_URL}" /><br />
	{$VARS.SPONSOR_LOGO_IMAGE}:<br />
	<input type="file" size="40" maxlength="50" name="sponsor_logo_file" /><br />
	
	{if $VARS.SELECTED_ID_SPONSOR neq null}
	<input name="sponsor_id" type="hidden" value="{$VARS.SELECTED_ID_SPONSOR}" />
	{/if}
	
	<input name="sponsor_reset" type="reset" value="{$VARS.BUTTON_RESET}" />
	<input name="sponsor_send" type="submit" value="{$VARS.BUTTON_SEND}" />
	
</form>
{if $VARS.SPONSOR_IMAGE neq null}
{html_image file=$VARS.DIR_TO_IMAGES|cat:$VARS.SPONSOR_IMAGE}
{/if}

</div>