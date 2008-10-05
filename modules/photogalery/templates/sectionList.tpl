<div>
{foreach from=$VARS.SECTIONS item='SECTION' name='sec'}
<div>
<a href="{$SECTION.sectionshowlink}" title="{$SECTION.sectionlabel}">{$SECTION.sectionlabel} ({$SECTION.num_gals})</a><br />
{* if $SECTION.galeries neq null *}
{foreach from=$SECTION.galeries item='GALERY' name='gal}
<!--<div class="galeryBoxContainer">-->
<div class="galeryBox left_float">
<a href="{$GALERY.galeryshowlink}" title="{$GALERY.galerylabel}">{$GALERY.galerylabel}</a><br />
<div class="smallPhotoBox">
{if $GALERY.file neq null}
<a href="{$GALERY.galeryshowlink}" title="{$GALERY.galerylabel}">
{html_image file=$VARS.IMAGES_DIR|cat:$GALERY.file width=130}
</a>
{/if}
</div>
{$VARS.ADD_TEXT}: <br />{$GALERY.time|date_format:'%x %X'}<br />
{$VARS.NUM_PHOTOS}: {$GALERY.num_photos}<br />
<!--<a href="{$VARS.GALERIES_DIR_TO_MEDIUM_PHOTOS}{$IMAGE.file}" rel="lightbox[roadtrip{$smarty.foreach.galery.index}]" title="{$IMAGE.photolabel}">-->
<!--<img src="{$VARS.GALERIES_DIR_TO_SMALL_PHOTOS}{$IMAGE.file}" title="{$IMAGE.photolabel}"/>-->
<!--</a>-->
</div>
<!--</div>-->
{foreachelse}
{$VARS.NOT_ANY_GALERY}
{/foreach}
<br class="reseter" />
<br />
{* /if *}
</div>
{/foreach}
</div>
