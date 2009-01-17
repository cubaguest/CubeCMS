<div id="editTextForm">
<ul class="langMenu">
{foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
<li><a href="#textLang{$smarty.foreach.lang.iteration}" {if $smarty.foreach.lang.first}
class="selected"{/if}>{html_engine_image file=$MAIN_LANG_IMAGES_PATH|cat:$KEYLANG|cat:'.png'}{$LANG}</a></li>
{/foreach}
</ul>

<div class="tabsContent">
<form method="post" action="{$THIS_PAGE_LINK}">
	{foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
	<p id="textLang{$smarty.foreach.lang.iteration}" class="tabcontent">
		{$VARS.TEXT_NAME}{if $smarty.foreach.textLang.first}*{/if}:<br />
		<textarea class="textarea_tinymce textarea{if $VARS.ERROR_ITEMS.text.$KEYLANG eq true} badItem{/if}"
         rows="5" cols="60" name="text_text[{$KEYLANG}]">{$VARS.TEXT_DATA.text.$KEYLANG}</textarea>
	</p>
	{/foreach}
	<br />
	<input name="text_reset" type="reset" value="{$VARS.BUTTON_RESET}" />
	<input name="text_send" type="submit" value="{$VARS.BUTTON_TEXT_SEND}" />
	
</form>
</div>
{literal}
<script>
  $(document).ready(function(){
    $("#editTextForm > ul").tabs();
  });
</script>
{/literal}
</div>
{include file="engine:buttonback.tpl"}