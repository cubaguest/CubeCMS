<div>
{foreach from=$VARS.ARTICLE_LIST_ARRAY item="article"}
<div>
<h2><a href="{$new.showlink}" title="{$article.label}">{$article.label}</a></h2>
{$article.time|date_format:"%x %X"}<br />
{$article.username}<br />
{$article.text|truncate:500}
</div>
{/foreach}
<br />
</div>