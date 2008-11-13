<p>
{foreach from=$APP_LANGS item="LANG" name='langForeach'}
<a href="{$LANG.link}" title="{$LANG.label}" >{$LANG.name}</a>{ if !$smarty.foreach.langForeach.last}&nbsp;&#448;&nbsp;{/if}
{/foreach}
</p>