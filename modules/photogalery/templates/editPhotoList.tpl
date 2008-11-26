<div>
<div class="tabsContent">
<form method="post" action="{$THIS_PAGE_LINK}">

{foreach from=$VARS.PHOTOS key='PHOTOKEY' item='PHOTO' name='photoItem'}
	<div class="left_float photo_small_image">
	<img src="{$VARS.PHOTOS_DIR}{$PHOTO.file}" alt="{$PHOTO.file}"/>
	</div>
	<div>
	<ul id="langstabs{$smarty.foreach.photoItem.iteration}" class="shadetabs">
	{foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
	<li><a href="#" rel="lang{$smarty.foreach.photoItem.iteration}{$smarty.foreach.lang.iteration}" {if $smarty.foreach.lang.first}class="selected"{/if}>{html_engine_image file=$MAIN_LANG_IMAGES_PATH|cat:$KEYLANG|cat:'.png'}{$LANG}</a></li>
	{/foreach}
	</ul>
	{foreach from=$PHOTO.photolabel key='LANG' item='PHOTOLABEL' name='photoLang'}
	<p id="lang{$smarty.foreach.photoItem.iteration}{$smarty.foreach.photoLang.iteration}" class="tabcontent">
	{$VARS.PHOTO_LABEL_NAME}{if $smarty.foreach.photoLang.first}*{/if}:<br />
	<input type="text" size="40" maxlength="50" name="photo_[{$PHOTO.id_photo}][label_{$LANG}]" value="{$PHOTOLABEL.label}" /><br />
<!--	<input type="text" size="40" maxlength="50" name="photo  photo_label_{$LANG}[{$PHOTO.id_photo}]" value="{$PHOTOLABEL.label}" /><br />-->
	{$VARS.PHOTO_TEXT_NAME}:<br />
<!--	<textarea rows="5" cols="50" name="photo_text_{$LANG}[{$PHOTO.id_photo}]">{$PHOTOLABEL.text}</textarea>-->
	<textarea rows="5" cols="50" name="photo_[{$PHOTO.id_photo}][text_{$LANG}]">{$PHOTOLABEL.text}</textarea>
	</p>

	
	{/foreach}
	<br class="reseter" />
	</div>
<script type="text/javascript">
var langs=new ddtabcontent("langstabs{$smarty.foreach.photoItem.iteration}");
langs.setpersist(true);
langs.setselectedClassTarget("link"); //"link" or "linkparent"
langs.init();
</script>
{/foreach}	
	
	<input name="photo_reset" type="reset" value="{$VARS.BUTTON_RESET}" />
	<input name="photo_send" type="submit" value="{$VARS.BUTTON_SEND}" />
	
</form>
</div>

</div>