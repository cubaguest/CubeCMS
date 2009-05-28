<div>
<h2>{$VARS.ACTION.label}<span class="smallFont"></span></h2>
{if $VARS.ACTION.image neq null}
<img style="float:left;" src="{$VARS.IMAGES_DIR}{$VARS.ACTION.image}" alt="{$VARS.ACTION.image}" />
{/if}
{$VARS.ACTION.text}
<hr class="reseter" />
{include file='engine:buttonback.tpl'}
</div>