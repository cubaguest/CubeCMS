<div>
{foreach from=$VARS.ARTICLE_LIST_ARRAY item="article"}
<div>
<h2><a href="{$article.showlink}" title="{$article.label}">{$article.label}&nbsp;<span class="smallFont">{$article.time|date_format:"%x %X"} - {$article.username}</span></a></h2>
{$article.text|protect_html|truncate:500}
<p style="text-align:right;"><a href="{$article.showlink}" title="{$article.label}">{$VARS.ARTICLES_MORE_NAME}</a></p>
</div>
{/foreach}
<br />
</div>