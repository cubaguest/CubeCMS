{if $VARS.WRITABLE eq true AND $VARS.IN_SECTION eq true}
<div class="align_right form_buttons_inline right_float">
	<form action="{$VARS.LINK_TO_EDIT_SECTION}" method="post" class="">
		<input type="hidden" name="section_id" value="{$VARS.SECTION_ID}" />
		<input type="submit" name="section_edit" value="{$VARS.BUTTON_EDIT}"/>
	</form>
	<form class="delete_form" action="{$THIS_PAGE_LINK}" method="post" onsubmit="return Confirm('{$VARS.DELETE_SECTION_CONFIRM_MESSAGE} - {$VARS.SECTION_NAME}')">
		<input type="hidden" name="section_id" value="{$VARS.SECTION_ID}" />
		<input type="submit" name="section_delete" value="{$VARS.BUTTON_DELETE}"/>
	</form>
</div>
{/if}
{if $VARS.WRITABLE eq true}
<br class="reseter" /><br />
{/if}
<div>
{foreach from=$VARS.GALERIES_LIST_ARRAY item='GALERY'}
<div>
<a href="{$GALERY.sectionshowlink}" title="{$GALERY.sectionlabel}">{$GALERY.sectionlabel}</a> - <a href="{$GALERY.galeryshowlink}" title="{$GALERY.galerylabel}" >{$GALERY.galerylabel}</a> {$VARS.ADD_TEXT}: {$GALERY.time|date_format:'%c'}<br />
{if $GALERY.galerytext neq null}
{$GALERY.galerytext}<br />
{/if}

{foreach from=$GALERY.images item='IMAGE' name=photo}
<div class="left_float smallPhotoBox">
<img src="{$VARS.GALERIES_DIR_TO_SMALL_PHOTOS}{$IMAGE.file}" title="{$IMAGE.photolabel}"/>
</div>
{if $smarty.foreach.photo.iteration is div by $VARS.NUMBER_OF_PHOTOS_IN_ROW}
<br class="reseter" />
{/if}
{foreachelse}
{$VARS.NOT_ANY_IMAGE}
{/foreach}
<br />
<br />
</div>
{/foreach}
</div>

