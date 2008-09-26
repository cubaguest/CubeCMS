<div>
{if $VARS.PHOTO.photolabel neq null}
<div>
{$VARS.PHOTO.phototext}
</div>
{/if}

<a href="{$VARS.DIR_TO_PHOTO}{$VARS.PHOTO.file}" rel="lightbox"  title="{$VARS.PHOTO.photolabel}">
<img src="{$VARS.DIR_TO_MEDIUM_PHOTO}{$VARS.PHOTO.file}" title="{$VARS.PHOTO.photolabel}"/>
</a>
{if $VARS.WRITABLE eq true}
<div class="photo_buttons_edit">
	<form class="delete_form right_float" action="{$THIS_PAGE_LINK}" method="post" onsubmit="return Confirm('{$VARS.DELETE_CONFIRM_MESSAGE} - {$VARS.PHOTO.photolabel}')">
		<input type="hidden" name="photo_id" value="{$VARS.PHOTO.id_photo}" />
		<input type="submit" name="photo_delete" value="{$VARS.BUTTON_DELETE}"/>
	</form>
	<form action="{$VARS.PHOTO.editlink}" method="post" class="right_float">
		<input type="hidden" name="photo_id" value="{$VARS.PHOTO.id_photo}" />
		<input type="submit" name="photo_edit" value="{$VARS.BUTTON_EDIT}"/>
	</form>
	<br class="reseter" />
</div>
{/if}
</div>