<div>
{foreach from=$VARS.RANDOM_PHOTOS_ARRAY item="PHOTO"}
<h5>{$VARS.GALERY_NAME}: <a href="{$PHOTO.galeryshowlink}" title="{$PHOTO.galerylabel}">{$PHOTO.galerylabel|truncate:18:"...":true}</a></h5>
<!--{html_image file=$PHOTO.file href=$PHOTO.photoshowlink alt=$PHOTO.photolabel}-->
<a href="{$PHOTO.photoshowlink}" title="{$PHOTO.photolabel}">{html_image file=$PHOTO.file alt=$PHOTO.photolabel}</a> 

<br /><br>
{/foreach}
<h5>{$VARS.NEW_PHOTOGALERIES}:</h5>
{foreach from=$VARS.NEW_PHOTOGALERIES_ARRAY item="GALERY"}
<a href="{$GALERY.galeryshowlink}" title="{$GALERY.galerylabel}">{$GALERY.galerylabel|truncate:25:"..."}</a><br />
{/foreach}
<br />
<a href="{$VARS.PHOTOGALERY_LINK}" title="{$VARS.PHOTOGALERY_LINK_NAME}">{$VARS.PHOTOGALERY_LINK_NAME}</a>
</div>