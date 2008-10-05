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
{foreach from=$VARS.PHOTOS item='PHOTO' name='phot'}
<!--<div class="galeryBoxContainer">-->
<div class="galeryBox left_float">
<a href="{$PHOTO.photoshowlink}" title="{$PHOTO.photolabel}">{$PHOTO.photolabel}</a><br />
<div class="smallPhotoBox">
<a href="{$PHOTO.photoshowlink}" title="{$PHOTO.photolabel}">
{if $PHOTO.file neq null}
{html_image file=$VARS.IMAGES_DIR|cat:$PHOTO.file width=130}
{/if}
</a>
</div>
{$VARS.ADD_TEXT}: {$PHOTO.time|date_format:'%x'}<br />
<!--<a href="{$VARS.GALERIES_DIR_TO_MEDIUM_PHOTOS}{$IMAGE.file}" rel="lightbox[roadtrip{$smarty.foreach.galery.index}]" title="{$IMAGE.photolabel}">-->
<!--<img src="{$VARS.GALERIES_DIR_TO_SMALL_PHOTOS}{$IMAGE.file}" title="{$IMAGE.photolabel}"/>-->
<!--</a>-->
</div>
<!--</div>-->
{if $smarty.foreach.phot.iteration is div by 4}<br class="reseter" /><br />{/if}
{foreachelse}
{$VARS.NOT_ANY_PHOTO}
{/foreach}
<br class="reseter" />
<br />

</div>
