<div>
<form action="{$THIS_PAGE_LINK}" method="post" enctype="multipart/form-data">
	<fieldset>
	<legend>{$VARS.ADD_TO_GALERY}</legend>
	{html_options name=photo_galery_id options=$VARS.GALERIES selected=$VARS.GALERY_SELECTED}<br />
	</fieldset>

	<fieldset>
	<legend>{$VARS.ADD_PHOTO}</legend>
	<ul id="langsphototabs" class="shadetabs">
		{foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
		<li><a href="#" rel="lang{$smarty.foreach.lang.iteration}" {if $smarty.foreach.lang.first}class="selected"{/if}>{html_engine_image file=$MAIN_LANG_IMAGES_PATH|cat:$KEYLANG|cat:'.png'}{$LANG}</a></li>
		{/foreach}
	</ul>
{foreach from=$VARS.PHOTO key='LANG' item='PHOTO' name='photoLang'}
	<p id="lang{$smarty.foreach.photoLang.iteration}" class="tabcontent">
	{$VARS.PHOTO_LABEL_NAME}:<br />
	<input type="text" size="40" maxlength="50" name="photo_label_{$LANG}" value="{$PHOTO.photolabel}" /><br />
	{$VARS.PHOTO_TEXT_NAME}:<br />
	<textarea rows="5" cols="60" name="photo_text_{$LANG}">{$PHOTO.phototext}</textarea>
	</p>
{/foreach}
	<br />
	<label>{$VARS.PHOTO_FILE_NAME}:</label><br />
	<input type="file" name="photo_file" value="" size="40" />
	</fieldset>
	
	<input name="photo_reset" type="reset" value="{$VARS.BUTTON_RESET}" />
	<input name="photo_send" type="submit" value="{$VARS.BUTTON_SEND}" />

<fieldset>
	<legend>{$VARS.ADD_NEW_SECTION_OR_GALERY}</legend>
	<fieldset>
	<legend>{$VARS.SELECT_SECTION_NAME}</legend>
	{html_options name=photo_section_id options=$VARS.SECTIONS selected=$VARS.SELECTED_SECTION}<br />
	<br />
	</fieldset>
	<fieldset>
	<legend>{$VARS.CREATE_NEW_GALERY_NAME}</legend>
	<ul id="langsgalerytabs" class="shadetabs">
		{foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
		<li><a href="#" rel="langgalery{$smarty.foreach.lang.iteration}" {if $smarty.foreach.lang.first}class="selected"{/if}>{html_engine_image file=$MAIN_LANG_IMAGES_PATH|cat:$KEYLANG|cat:'.png'}{$LANG}</a></li>
		{/foreach}
	</ul>
{foreach from=$VARS.GALERY key='LANG' item='GALERY' name='galeryLang'}
	<p id="langgalery{$smarty.foreach.galeryLang.iteration}" class="tabcontent">
	{$VARS.GALERY_LABEL_NAME}{if $smarty.foreach.galeryLang.first}*{/if}:<br />
	<input type="text" size="40" maxlength="50" name="galery_label_{$LANG}" value="{$GALERY.galerylabel}" /><br />
	{$VARS.GALERY_TEXT_NAME}:<br />
	<textarea rows="5" cols="60" name="galery_text_{$LANG}">{$GALERY.galerytext}</textarea>
	</p>
{/foreach}
	{$VARS.DATE_SELECT_NAME}:<br />
	{html_select_date field_array='galery_date' field_order='DMY' start_year=+1 end_year=-10 time=$VARS.DATE_SELECT}<br />
	</fieldset>
</fieldset>

</form>
<script type="text/javascript">
var langsphoto=new ddtabcontent("langsphototabs");
langsphoto.setpersist(true);
langsphoto.setselectedClassTarget("link"); //"link" or "linkparent"
langsphoto.init();
var langsgalery=new ddtabcontent("langsgalerytabs");
langsgalery.setpersist(true);
langsgalery.setselectedClassTarget("link"); //"link" or "linkparent"
langsgalery.init();
</script>
</div>