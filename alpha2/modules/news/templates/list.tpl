{foreach from=$VARS.NEWS_LIST_ARRAY item="new"}
<div class="news">
Autor: {$new.id_user}<br />
Popis: {$new.label}<br />
Text: <br />
{$new.text}
</div>
{/foreach}

<form method="post" action="{$THIS_PAGE_LINK}">
<textarea rows="5" cols="20" class="textarea textEditor"></textarea>
</form>