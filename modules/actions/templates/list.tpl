<div>
{foreach from=$VARS.ACTIONS_LIST item=action}
<div>
<h2><a href="{$action.showlink}" title="{$action.label}">{$action.label}</a></h2>
<!--&nbsp;<span class="smallFont">{$action.start_date|date_format:"%x"} - {$action.stop_date|date_format:"%x"}</span>-->
{if $action.image neq null}
<img style="float:left;" src="{$VARS.IMAGES_DIR}{$action.image}" alt="{$action.image}" />
{/if}

{$action.text}
</div>
<br clear="all" />
{/foreach}
</div>