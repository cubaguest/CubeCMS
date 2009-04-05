<div>
<h2>{$VARS.GALERY.galerylabel} ({$VARS.GALERY.num_photos})</h2>
{if $VARS.GALERY_EDIT eq true}
<div class="form_buttons form_buttons_inline">
	<form action="{$VARS.LINK_TO_EDIT_GALERY}" method="post">
		<input type="hidden" name="galery_id" value="{$VARS.GALERY.id_galery}" />
		<input type="submit" name="galery_edit" value="{$VARS.BUTTON_EDIT}"/>
	</form>
	<form class="delete_form" action="{$THIS_PAGE_LINK}" method="post" onsubmit="return Confirm('{$VARS.DELETE_CONFIRM_MESSAGE} - {$VARS.GALERY.galerylabel}')">
		<input type="hidden" name="galery_id" value="{$VARS.GALERY.id_galery}" />
		<input type="submit" name="galery_delete" value="{$VARS.BUTTON_DELETE}"/>
	</form>
</div>
{/if}

{if $VARS.PHOTO_EDIT eq true}
<form class="delete_form" action="{$VARS.LINK_TO_EDIT_PHOTOS}" method="post" name="photosEdit" onsubmit="return check('photosEdit', '{$VARS.NOT_ANY_PHOTO_CHECKED}')">
{/if}

{assign var='ANY_PHOTO' value=true}
{foreach from=$VARS.PHOTOS item='PHOTO' name='phot'}
<!--<div class="galeryBoxContainer">-->
<div class="galeryBox left_float">
<p><a href="{$PHOTO.photoshowlink}" title="{$PHOTO.photolabel}">{$PHOTO.photolabel|truncate:22}</a></p>
<div class="smallPhotoBox">
<a href="{$PHOTO.photoshowlink}" title="{$PHOTO.photolabel}">
{if $PHOTO.file neq null}
{html_image file=$VARS.IMAGES_DIR|cat:$PHOTO.file width=130}
{/if}
</a>
</div>
{$VARS.ADD_TEXT}: {$PHOTO.time|date_format:'%x'}<br />
{if $VARS.PHOTO_EDIT eq true}
Vybrat:<input id="id_photo" name="photo_id[]" value="{$PHOTO.id_photo}" type="checkbox" />
<!--<div class="photo_buttons_edit form_buttons_inline">-->
<!--	<form class="delete_form" action="{$THIS_PAGE_LINK}" method="post" onsubmit="return Confirm('{$VARS.DELETE_CONFIRM_MESSAGE} - {$PHOTO.photolabel}')">-->
<!--		<input type="hidden" name="photo_id" value="{$PHOTO.id_photo}" />-->
<!--		<input type="submit" name="photo_delete" value="{$VARS.BUTTON_DELETE}"/>-->
<!--	</form>-->
<!--	<form action="{$PHOTO.editlink}" method="post" class="">-->
<!--		<input type="hidden" name="photo_id" value="{$PHOTO.id_photo}" />-->
<!--		<input type="submit" name="photo_edit" value="{$VARS.BUTTON_EDIT}"/>-->
<!--	</form>-->
<!--	<br class="reseter" />-->
<!--</div>-->
{/if}
<!--<a href="{$VARS.GALERIES_DIR_TO_MEDIUM_PHOTOS}{$IMAGE.file}" rel="lightbox[roadtrip{$smarty.foreach.galery.index}]" title="{$IMAGE.photolabel}">-->
<!--<img src="{$VARS.GALERIES_DIR_TO_SMALL_PHOTOS}{$IMAGE.file}" title="{$IMAGE.photolabel}"/>-->
<!--</a>-->
</div>
<!--</div>-->
{if $smarty.foreach.phot.iteration is div by 4}<br class="reseter" /><br />{/if}
{foreachelse}
{$VARS.NOT_ANY_PHOTO}
{assign var='ANY_PHOTO' value=false}
{/foreach}
<br class="reseter" />
{if $VARS.PHOTO_EDIT eq true AND $ANY_PHOTO}
<!--	<input type="submit" name="photo_edit" value="{$VARS.BUTTON_EDIT}"/>-->
	<a href="javascript:void setCheckboxes('photosEdit', 'checked');" onclick="setCheckboxes('photosEdit', 'checked');" title="{$VARS.CHECK_ALL_CHECKBOXS}">{$VARS.CHECK_ALL_CHECKBOXS}</a>
	<a href="javascript:void setCheckboxes('photosEdit', '');" onclick="setCheckboxes('photosEdit', '');" title="{$VARS.UNCHECK_ALL_CHECKBOXS}">{$VARS.UNCHECK_ALL_CHECKBOXS}</a>
	<input type="hidden" name="photo_is_edit" value="1" />
	<button type="submit" name="photo_edit" value="{$VARS.BUTTON_EDIT}" onclick="document.forms['photosEdit'].photo_is_edit.value = '1';">{$VARS.BUTTON_EDIT}</button>
	<span class="delete_form">
	<button class="delete_button" type="submit" name="photo_delete" value="{$VARS.BUTTON_DELETE}" onclick="document.forms['photosEdit'].photo_is_edit.value = '0'; return Confirm('{$VARS.DELETE_CONFIRM_MESSAGE} {$VARS.DELETE_SELECTED_PHOTOS}');">{$VARS.BUTTON_DELETE}</button>
	</span>
</form>
{/if}

<br />

</div>
