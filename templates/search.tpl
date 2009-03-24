<div style="text-align:right">{$SEARCH_RESULT_COUNT_LABEL}: {$SEARCH_RESULTS_COUNT}</div>
{foreach from="$SEARCH_RESULTS" item="RESULT" key="RESULT_KEY" name ='searchresults'}
<h2><a href="{$RESULT.url}" title="{$RESULT.category}">{$RESULT.category}</a></h2>
<p>{$RESULT.text|truncate:300}</p>
{$SEARCH_RESULT_MORE}: <a href="{$RESULT.url}" title="{$RESULT.url}">{$RESULT.url|truncate:100}</a>
{foreachelse}
{$NOT_ANY_SEARCH_RESULT}
{/foreach}