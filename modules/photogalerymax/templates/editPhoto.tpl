<div>
<ul id="langstabs" class="shadetabs">
{foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
<li><a href="#" rel="lang{$smarty.foreach.lang.iteration}" {if $smarty.foreach.lang.first}class="selected"{/if}>{html_engine_image file=$MAIN_LANG_IMAGES_PATH|cat:$KEYLANG|cat:'.png'}{$LANG}</a></li>
{/foreach}
</ul>
<div class="tabsContent">
<form method="post" action="{$THIS_PAGE_LINK}">

	{foreach from=$VARS.PHOTO_A key='LANG' item='PHOTO' name='photoLang'}
	<p id="lang{$smarty.foreach.photoLang.iteration}" class="tabcontent">
	{$VARS.PHOTO_LABEL_NAME}{if $smarty.foreach.photoLang.first}*{/if}:<br />
	<input type="text" size="40" maxlength="50" name="photo_label_{$LANG}" value="{$PHOTO.label}" /><br />
	{$VARS.PHOTO_TEXT_NAME}:<br />
	<textarea rows="5" cols="60" name="photo_text_{$LANG}">{$PHOTO.text}</textarea>
	</p>	
	{/foreach}
	<input name="photo_id" type="hidden" value="{$VARS.PHOTO_ID}" />
	
	<input name="photo_reset" type="reset" value="{$VARS.BUTTON_RESET}" />
	<input name="photo_send" type="submit" value="{$VARS.BUTTON_SEND}" />
	
</form>
<!--<img src="{$VARS.GALERY_DIR_TO_SMALL_PHOTOS}{$VARS.PHOTO_FILE}" title="{$VARS.PHOTO_NAME}"/>-->
</div>
<script type="text/javascript">
var langs=new ddtabcontent("langstabs");
langs.setpersist(true);
langs.setselectedClassTarget("link"); //"link" or "linkparent"
langs.init();
</script>
</div>