<div>
<h2>{$VARS.ARTICLE.label}&nbsp;<span class="smallFont">{$VARS.ARTICLE.time|date_format:"%x %X"} - {$VARS.ARTICLE.username}</span></h2>
{$VARS.ARTICLE.text}
{if $VARS.LIGHTBOX eq true}
{literal}
<script type="text/javascript">
//$(function() {
	// Use this example, or...
	//$('a[@rel=lightbox]').lightBox(); // Select all links that contains lightbox in the attribute rel
	// This, or...
	//$('#gallery a').lightBox(); // Select all links in object with gallery ID
	// This, or...
	//$('a.lightbox').lightBox(); // Select all links with lightbox class
	// This, or...
	//$('a').lightBox(); // Select all links in the page
	// ... The possibility are many. Use your creative or choose one in the examples above
//});
</script>
{/literal}
{/if}
{include file='engine:buttonback.tpl'}
{if $VARS.LIGHTBOX}
{literal}
<script type="text/javascript">
$(document).ready(function() { $('a[rel*=lightbox]').lightBox(); });
</script>
{/literal}
{/if}
</div>
