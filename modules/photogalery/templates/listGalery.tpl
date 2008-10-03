<div>
<h2>{$VARS.GALERY.galerylabel} ({$VARS.GALERY.num_photos})</h2>

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
