<div>
{foreach from=$VARS.NEWS_ARRAY item="NEW"}
<h5><a href="{$NEW.show_link}" title="{$NEW.label}">{$NEW.label}</a></h5>
<p>
{$NEW.text|truncate:200}<br />
<a href="{$NEW.show_link}" title="{$VARS.NEWS_LINK_NAME}">[&nbsp;{$VARS.NEWS_MORE}&nbsp;]</a><br /><br />
</p>
{/foreach}
<a href="{$VARS.NEWS_LINK}" title="{$VARS.NEWS_LINK_NAME}">{$VARS.NEWS_LINK_NAME}</a>
</div>