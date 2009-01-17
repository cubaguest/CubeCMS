<div id="addNewForm">
<ul class="langMenu">
{foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
<li><a href="#newsLang{$smarty.foreach.lang.iteration}" {if $smarty.foreach.lang.first}
class="selected"{/if}>{html_engine_image file=$MAIN_LANG_IMAGES_PATH|cat:$KEYLANG|cat:'.png'}{$LANG}</a></li>
{/foreach}
</ul>

<div class="tabsContent">
<form method="post" action="{$THIS_PAGE_LINK}">
	{foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
	<p id="newsLang{$smarty.foreach.lang.iteration}" class="tabcontent">
		{$VARS.NEWS_LABEL_NAME}{if $smarty.foreach.lang.first}*{/if}:<br />
		<input {if $VARS.ERROR_ITEMS.label.$KEYLANG eq true}class="badItem"{/if} type="text" size="40" maxlength="50" name="news_label[{$KEYLANG}]" value="{$VARS.NEWS_DATA.label.$KEYLANG}" /><br />
		{$VARS.NEWS_TEXT_NAME}{if $smarty.foreach.lang.first}*{/if}:<br />
		<textarea rows="5" cols="60" name="news_text[{$KEYLANG}]"class="textarea{if $VARS.ERROR_ITEMS.text.$KEYLANG eq true} badItem{/if}">{$VARS.NEWS_DATA.text.$KEYLANG}</textarea>
	</p>
	{/foreach}
	<br />
	{if $VARS.SELECTED_ID_NEWS neq null}
	<input name="news_id" type="hidden" value="{$VARS.SELECTED_ID_NEWS}" />
	{/if}
	<input name="news_reset" type="reset" value="{$VARS.BUTTON_RESET}" />
	<input name="news_send" type="submit" value="{$VARS.BUTTON_SEND}" />
	
</form>
</div>
{literal}
<script>
  $(document).ready(function(){
    $("#addNewForm > ul").tabs();
  });
</script>
{/literal}
<script type="text/javascript">
//var langs=new ddtabcontent("langstabs");
//langs.setpersist(true);
//langs.setselectedClassTarget("link"); //"link" or "linkparent"
//langs.init();
</script>
</div>
