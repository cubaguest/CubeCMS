{foreach from="$SEARCH_RESULTS" item="RESULT" key="RESULT_KEY" name ='searchresults'}
{foreachelse}
{$NOT_ANY_SEARCH_RESULT}
{/foreach}