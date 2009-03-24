<div id="addReferenceForm" class="content">
<h2>{$VARS.ADD_REFERENCE_LABEL}</h2>
<ul class="langMenu">
{foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
<li><a href="#referLang{$smarty.foreach.lang.iteration}" {if $smarty.foreach.lang.first}
class="selected"{/if}>{html_engine_image file=$MAIN_LANG_IMAGES_PATH|cat:$KEYLANG|cat:'.png'}{$LANG}</a></li>
{/foreach}
</ul>

<div class="tabsContent">
<form method="post" action="{$THIS_PAGE_LINK}" enctype="multipart/form-data">
	{foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
	<p id="referLang{$smarty.foreach.lang.iteration}" class="tabcontent">
		{$VARS.REFERENCE_LABEL_NAME}{if $smarty.foreach.lang.first}*{/if}:<br />
		<input {if $VARS.ERROR_ITEMS.label.$KEYLANG eq true}class="badItem"{/if} type="text" size="40" maxlength="50" name="reference_name[{$KEYLANG}]" value="{$VARS.REFERENCE_DATA.name.$KEYLANG}" /><br />
		{$VARS.REFERENCE_TEXT_NAME}{if $smarty.foreach.lang.first}*{/if}:<br />
		<textarea rows="5" cols="60" name="reference_label[{$KEYLANG}]" class="textarea{if $VARS.ERROR_ITEMS.text.$KEYLANG eq true} badItem{/if}">{$VARS.REFERENCE_DATA.label.$KEYLANG}</textarea>
	</p>
	{/foreach}
   <label>{$VARS.REFERENCE_IMAGE_LABEL}</label><br />
   <input type="file" name="reference_file" size="30" />
	<br />
	{if $VARS.SELECTED_ID_NEWS neq null}
	<input name="reference_id" type="hidden" value="{$VARS.SELECTED_ID_NEWS}" />
	{/if}
	<input name="reference_reset" type="reset" value="{$VARS.BUTTON_RESET}" />
	<input name="reference_send" type="submit" value="{$VARS.BUTTON_SEND}" />
	
</form>
</div>
{literal}
<script>
  $(document).ready(function(){
    $("#addReferenceForm > ul").tabs();
  });
</script>
{/literal}
{include file='engine:buttonback.tpl'}
</div>
