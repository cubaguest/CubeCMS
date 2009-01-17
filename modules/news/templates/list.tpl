{foreach from=$VARS.NEWS_LIST_ARRAY item="new"}
<div>
<h2><a href="{$new.showlink}" title="{$new.label}">{$new.label}</a></h2>
{$new.time|date_format:"%x %X"}<br />
{$new.username}<br />
{$new.text}
</div>
{/foreach}
<div>
{$VARS.NUM_NEWS_SHOW}:
{foreach from=$VARS.NUM_NEWS item='LINK' key='NUM'}
{if $LINK neq null}
<a href="{$LINK}" title="{$NUM}">{$NUM}</a>
{else}
<span>{$NUM}</span>
{/if}
{/foreach}
<a href="{$VARS.NUM_NEWS_ALL}" title="{$VARS.NUM_NEWS_ALL_NAME}">{$VARS.NUM_NEWS_ALL_NAME}</a>
</div>
