<div id="editArticleForm">
<ul class="langMenu">
{foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
<li><a href="#articleLang{$smarty.foreach.lang.iteration}" {if $smarty.foreach.lang.first}
class="selected"{/if}>{html_engine_image file=$MAIN_LANG_IMAGES_PATH|cat:$KEYLANG|cat:'.png'}{$LANG}</a></li>
{/foreach}
</ul>

<div class="tabsContent">
<form method="post" action="{$THIS_PAGE_LINK}">
	{foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
	<p id="articleLang{$smarty.foreach.lang.iteration}" class="tabcontent">
		{$VARS.ARTICLE_LABEL_NAME}{if $smarty.foreach.lang.first}*{/if}:<br />
		<input {if $VARS.ERROR_ITEMS.label.$KEYLANG eq true}class="badItem"{/if} type="text" size="40" maxlength="50" name="article_label[{$KEYLANG}]" value="{$VARS.ARTICLE_DATA.label.$KEYLANG}" /><br />
		{$VARS.ARTICLE_TEXT_NAME}{if $smarty.foreach.lang.first}*{/if}:<br />
		<textarea rows="5" cols="60" name="article_text[{$KEYLANG}]" class="textarea{if $VARS.ERROR_ITEMS.text.$KEYLANG eq true} badItem{/if}">{$VARS.ARTICLE_DATA.text.$KEYLANG}</textarea>
	</p>
	{/foreach}
	<br />
	{if $VARS.SELECTED_ID_ARTICLE neq null}
	<input name="article_id" type="hidden" value="{$VARS.SELECTED_ID_ARTICLE}" />
	{/if}
	<input name="article_reset" type="reset" value="{$VARS.BUTTON_RESET}" />
	<input name="article_send" type="submit" value="{$VARS.BUTTON_SEND}" />
	
</form>
</div>
{literal}
<script>
  $(document).ready(function(){
    $("#editArticleForm").tabs();
  });
</script>
{/literal}
{include file='engine:buttonback.tpl'}
</div>
