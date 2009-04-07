<div class="articlesList">
{foreach from=$VARS.ARTICLE_LIST_ARRAY item="article"}
<div>
<h2><a href="{$article.showlink}" title="{$article.label}">{$article.label}&nbsp;<span class="smallFont">{$article.add_time|date_format:"%x %X"} - {$article.username}</span></a></h2>
<div class="imageBox">
{if $article.title_image neq null}
<img src="{$article.title_image}" alt="article title image" height="80"/>
{else}
<img src="images/icons/article_icon.png" alt="article title image" height="80" />
{/if}
</div>
{$article.text|protect_html|truncate:650}
<p style="text-align:right;"><a href="{$article.showlink}" title="{$article.label}">{$VARS.ARTICLES_MORE_NAME}</a></p>
<hr class="reseter" />
</div>
{/foreach}
<br />
</div>