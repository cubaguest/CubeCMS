<div>
{foreach from=$VARS.SPONSORS_ARRAY item="SPONSOR"}
<p>
{$NEW.name}<br />
<a href="{$SPONSOR.url}" title="{$SPONSOR.name}" target="_blank">
{if $SPONSOR.logo_image neq null}
{html_image file=$VARS.DIR_TO_IMAGES|cat:$SPONSOR.logo_image}
{else}
{$SPONSOR.name}
{/if}
</a>
<br /><br />
</p>
{/foreach}
<a href="{$VARS.SPONSORS_LINK}" title="{$VARS.SPONSORS_LINK_NAME}">{$VARS.SPONSORS_LINK_NAME}</a>
</div>