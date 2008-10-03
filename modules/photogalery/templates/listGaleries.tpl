<div>
<h2>{$VARS.SECTION.sectionlabel} ({$VARS.SECTION.num_gals})</h2>
{if $VARS.SECTION_EDIT eq true}
<div class="form_buttons form_buttons_inline">
	<form action="{$VARS.LINK_TO_EDIT_SECTION}" method="post">
		<input type="hidden" name="section_id" value="{$VARS.SECTION.id_section}" />
		<input type="submit" name="section_edit" value="{$VARS.BUTTON_EDIT}"/>
	</form>
	<form class="delete_form" action="{$THIS_PAGE_LINK}" method="post" onsubmit="return Confirm('{$VARS.DELETE_CONFIRM_MESSAGE} - {$VARS.SECTION.sectionlabel}')">
		<input type="hidden" name="section_id" value="{$VARS.SECTION.id_section}" />
		<input type="submit" name="section_delete" value="{$VARS.BUTTON_DELETE}"/>
	</form>
</div>
{/if}
{foreach from=$VARS.GALERIES item='GALERY' name='gal}
<!--<div class="galeryBoxContainer">-->
<div class="galeryBox left_float">
<a href="{$GALERY.galeryshowlink}" title="{$GALERY.galerylabel}">{$GALERY.galerylabel}</a><br />
<div class="smallPhotoBox">
<a href="{$GALERY.galeryshowlink}" title="{$GALERY.galerylabel}">
{if $GALERY.file neq null}
{html_image file=$VARS.IMAGES_DIR|cat:$GALERY.file width=130}
{/if}
</a>
</div>
{$VARS.ADD_TEXT}: {$GALERY.time|date_format:'%x'}<br />
{$VARS.NUM_PHOTOS}: {$GALERY.num_photos}<br />
<!--<a href="{$VARS.GALERIES_DIR_TO_MEDIUM_PHOTOS}{$IMAGE.file}" rel="lightbox[roadtrip{$smarty.foreach.galery.index}]" title="{$IMAGE.photolabel}">-->
<!--<img src="{$VARS.GALERIES_DIR_TO_SMALL_PHOTOS}{$IMAGE.file}" title="{$IMAGE.photolabel}"/>-->
<!--</a>-->
</div>
<!--</div>-->
{if $smarty.foreach.gal.iteration is div by 4}<br class="reseter" /><br />{/if}
{foreachelse}
{$VARS.NOT_ANY_GALERY}
{/foreach}
<br class="reseter" />
<br />

</div>

