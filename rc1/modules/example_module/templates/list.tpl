{foreach from=$VARS.NEWS_LIST_ARRAY item="new"}
<div class="news">
Autor: {$new.id_user}<br />
Popis: {$new.label}<br />
Text: <br />
{$new.text}
</div>
{/foreach}