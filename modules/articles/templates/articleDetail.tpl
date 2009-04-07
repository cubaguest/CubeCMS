<div>
<h2>{$VARS.ARTICLE.label}&nbsp;<span class="smallFont">{$VARS.ARTICLE.add_time|date_format:"%x %X"} - {$VARS.ARTICLE.username}</span></h2>
{$VARS.ARTICLE.text}
{include file='engine:buttonback.tpl'}
{if $VARS.LIGHTBOX}
{literal}
<script type="text/javascript">
$(document).ready(function() { $('a[rel*=lightbox]').lightBox(); });
</script>
{/literal}
{/if}
</div>
