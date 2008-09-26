{if $VARS.WRITABLE eq true}
<div class="align_right form_buttons_inline right_float">
	<form action="{$VARS.LINK_TO_EDIT_GALERY}" method="post" class="">
		<input type="hidden" name="galery_id" value="{$VARS.GALERY_ID}" />
		<input type="submit" name="galery_edit" value="{$VARS.BUTTON_EDIT}"/>
	</form>
	<form class="delete_form" action="{$THIS_PAGE_LINK}" method="post" onsubmit="return Confirm('{$VARS.DELETE_GALERY_CONFIRM_MESSAGE} - {$VARS.GALERY_LABEL}')">
		<input type="hidden" name="galery_id" value="{$VARS.GALERY_ID}" />
		<input type="submit" name="galery_delete" value="{$VARS.BUTTON_DELETE}"/>
	</form>
</div>
<br class="reseter" /><br />
{/if}
<div class="galery_box">

{if $VARS.GALERY_TEXT neq null}
<div class="galery_text">{$VARS.GALERY_TEXT}</div>
<br />
{/if}

{foreach from=$VARS.GALERY_LIST_ARRAY item='GALERY' name=photo}
<div class="left_float smallPhotoBox">
<a href="{$GALERY.photoshowlink}" title="{$GALERY.photolabel}">
<img src="{$VARS.GALERY_DIR_TO_SMALL_PHOTOS}{$GALERY.file}" title="{$GALERY.photolabel}"/>
</a>
{if $VARS.WRITABLE eq true}
<div class="photo_buttons_edit">
	<form action="{$GALERY.editlink}" method="post" class="left_float">
		<input type="hidden" name="photo_id" value="{$GALERY.id_photo}" />
		<input type="submit" name="photo_edit" value="{$VARS.BUTTON_EDIT}"/>
	</form>
	<form class="delete_form right_float" action="{$THIS_PAGE_LINK}" method="post" onsubmit="return Confirm('{$VARS.DELETE_CONFIRM_MESSAGE} - {$GALERY.photolabel}')">
		<input type="hidden" name="photo_id" value="{$GALERY.id_photo}" />
		<input type="submit" name="photo_delete" value="{$VARS.BUTTON_DELETE}"/>
	</form>
</div>
{/if}
</div>

{if $smarty.foreach.photo.iteration is div by $VARS.NUMBER_OF_PHOTOS_IN_ROW}
<br class="reseter" /><br />
{/if}

{foreachelse}
{$VARS.NOT_ANY_IMAGE}
{/foreach}
<br class="reseter" />
</div>
{if $VARS.LINK_TO_BACK neq null}
<a href="{$VARS.LINK_TO_BACK}" title="{$VARS.LINK_TO_BACK_NAME}" class="button_back">{$VARS.LINK_TO_BACK_NAME}</a>
{/if}