<div>
<ul id="langstabs" class="shadetabs">
{foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
<li><a href="#" rel="lang{$smarty.foreach.lang.iteration}" {if $smarty.foreach.lang.first}
class="selected"{/if}>{html_engine_image file=$MAIN_LANG_IMAGES_PATH|cat:$KEYLANG|cat:'.png'}{$LANG}</a></li>
{/foreach}
</ul>
<div class="tabsContent">
<form method="post" action="{$THIS_PAGE_LINK}">
	{foreach from=$VARS.NEWS_EDIT_ARRAY key='LANG' item='NEWS' name='newsLang'}
	<p id="lang{$smarty.foreach.newsLang.iteration}" class="tabcontent">
		{$VARS.NEWS_LABEL_NAME}{if $smarty.foreach.newsLang.first}*{/if}:<br />
		<input type="text" size="40" maxlength="50" name="news_label_{$LANG}" value="{$NEWS.label}" /><br />
		{$VARS.NEWS_TEXT_NAME}{if $smarty.foreach.newsLang.first}*{/if}:<br />
		<textarea rows="5" cols="60" name="news_text_{$LANG}">{$NEWS.text}</textarea>
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
<script type="text/javascript">
var langs=new ddtabcontent("langstabs");
langs.setpersist(true);
langs.setselectedClassTarget("link"); //"link" or "linkparent"
langs.init();
</script>
</div>
