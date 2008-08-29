	<fieldset><legend>{$VARS.GALERY_NEW_NAME}</legend>
	{foreach from=$VARS.GALERY_ARRAY key='LANG' item='GALERY' name='galeryLang'}
	{html_image file=$MAIN_LANG_IMAGES_PATH|cat:$LANG|cat:'.png'}<br />
	{$VARS.GALERY_LABEL_NAME}{if $smarty.foreach.galeryLang.first}*{/if}:<br />
	<input type="text" size="40" maxlength="50" name="galery_label_{$LANG}" value="{$GALERY.galerylabel}" /><br />
	{$VARS.GALERY_TEXT_NAME}:<br />
	<textarea rows="5" cols="60" name="galery_text_{$LANG}">{$GALERY.galerytext}</textarea>
	<br />
	{/foreach}
	{if $VARS.GALERY_ID neq null}
	<input name="galery_id" type="hidden" value="{$VARS.GALERY_ID}" />
	{/if}
	</fieldset>
	
	<input name="galery_reset" type="reset" value="{$VARS.BUTTON_RESET}" />
	<input name="galery_send" type="submit" value="{$VARS.BUTTON_SEND}" />
	
</form>
</div>