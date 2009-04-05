<div>
{foreach from=$VARS.GALERIES_LIST item='GALERY' name='gal}
<h2><a href="{$GALERY.linkshow}" title="{$GALERY.label}">{$GALERY.label}</a></h2>

{* fotky *}
{foreach from=$GALERY.photos item='PHOTO' name='photo'}
{html_image file=$VARS.PHOTOS_SMALL_DIR|cat:$PHOTO.file}
{/foreach}
<br />
<p><a href="{$GALERY.linkshow}" title="{$VARS.SHOW_GALERY}">[&nbsp;{$VARS.SHOW_GALERY}&nbsp;]</a></p>
{foreachelse}
{$VARS.NOT_ANY_GALERY}
{/foreach}

<br class="reseter" />
<br />

</div>

