<div>{$VARS.TEXT}</div>
{if $VARS.LIGHTBOX}
{literal}
<script type="text/javascript">
$(document).ready(function() { $('a[rel*=lightbox]').lightBox(); });
</script>
{/literal}
{/if}