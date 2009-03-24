<!--Sipky pro posun stranek-->
<div class="scroll_main">
<table>
<tr>
<td width="100" align="left">
{* Sipka na zacatek *}
{if $VARS.BUTTON_BEGIN == true}
<a href="{$VARS.SCROLL_BUTTONS_LINKS.begin}">{html_engine_image file='/scroll/begin.png' alt='begin'}</a>
{else}
{html_engine_image file='/scroll/begin_disable.png' alt='begin'}
{/if}
&#032;&#032;
{* Sipka o jedno vpred *}
{if $VARS.BUTTON_NEXT == true}
<a href="{$VARS.SCROLL_BUTTONS_LINKS.next}">{html_engine_image file='/scroll/back.png' alt=$VARS.NAME_BUTTON_PREVIOUS_PAGE}<span>{$VARS.NAME_BUTTON_PREVIOUS_PAGE}</span></a>
{else}
{html_engine_image file='/scroll/back_disable.png' alt=$VARS.NAME_BUTTON_PREVIOUS_PAGE}<span>{$VARS.NAME_BUTTON_PREVIOUS_PAGE}</span>
{/if}
</td>
<td align="center">
<!--<p class="scroll_center">&#032;&#032;{$NAME_PAGE} {$SCROLL_DATA_ARRAY.$TPLKEY.selected_page} {$NAME_FROM} {$SCROLL_DATA_ARRAY.$TPLKEY.all_pages}&#032;&#032;</p>-->
{foreach from=$VARS.SCROLL_LEFT_SIDE_DATA_ARRAY item=ITEM}
&nbsp;<a href="{$ITEM.link}" title="{$ITEM.name}">{$ITEM.name}</a>&nbsp;
{/foreach}

[&nbsp;{$VARS.SCROLL_SELECTED_PAGE}/{$VARS.SCROLL_ALL_PAGES}&nbsp;]

{foreach from=$VARS.SCROLL_RIGHT_SIDE_DATA_ARRAY item=ITEM}
&nbsp;<a href="{$ITEM.link}" title="{$ITEM.name}">{$ITEM.name}</a>&nbsp;
{/foreach}
<!--&nbsp;&nbsp;{$SCROLL_ALL_PAGES}: {$VARS.SCROLL_DATA_ARRAY.all_pages}-->
</td>
<td width="100" align="right">
{* Sipka o jedno vzad *}
{if $VARS.BUTTON_BACK == true}
<a href="{$VARS.SCROLL_BUTTONS_LINKS.back}"><span>{$VARS.NAME_BUTTON_NEXT_PAGE}</span>{html_engine_image file='/scroll/next.png' alt=$VARS.NAME_BUTTON_NEXT_PAGE}</a>
{else}
<span>{$VARS.NAME_BUTTON_NEXT_PAGE}</span>{html_engine_image file='/scroll/next_disable.png' alt=$VARS.NAME_BUTTON_NEXT_PAGE}
{/if}
{* Sipka na konec *}
{if $VARS.BUTTON_END == true}
<a href="{$VARS.SCROLL_BUTTONS_LINKS.end}">{html_engine_image file='/scroll/end.png' alt='end'}</a>
{else}
{html_engine_image file='/scroll/end_disable.png' alt='end'}
{/if}
</td>
</tr>
</table>
</div>