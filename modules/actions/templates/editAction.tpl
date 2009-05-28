<div id="addActionForm">
<ul class="langMenu">
{foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
<li><a href="#actionLang{$smarty.foreach.lang.iteration}" {if $smarty.foreach.lang.first}
class="selected"{/if}>{html_engine_image file=$MAIN_LANG_IMAGES_PATH|cat:$KEYLANG|cat:'.png'}{$LANG}</a></li>
{/foreach}
</ul>

<div class="tabsContent">
<form method="post" action="{$THIS_PAGE_LINK}" enctype="multipart/form-data">
	{foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
	<p id="actionLang{$smarty.foreach.lang.iteration}" class="tabcontent">
		{$VARS.ACTION_LABEL_NAME}{if $smarty.foreach.lang.first}*{/if}:<br />
		<input {if $VARS.ERROR_ITEMS.label.$KEYLANG eq true}class="badItem"{/if} type="text" size="40" maxlength="50" name="action_label[{$KEYLANG}]" value="{$VARS.ACTION_DATA.label.$KEYLANG}" /><br />
		{$VARS.ACTION_TEXT_NAME}{if $smarty.foreach.lang.first}*{/if}:<br />
		<textarea rows="5" cols="60" name="action_text[{$KEYLANG}]" class="textarea{if $VARS.ERROR_ITEMS.text.$KEYLANG eq true} badItem{/if}">{$VARS.ACTION_DATA.text.$KEYLANG}</textarea>
	</p>
	{/foreach}
	<br />
<!--   {$VARS.SHOW_DATE_START}:<br />
   {html_select_date field_array='action_date_start' field_order='DMY' start_year=+10 time=$VARS.ACTION_DATA.start_date}<br />
   {$VARS.SHOW_DATE_STOP}:<br />
   {html_select_date field_array='action_date_stop' field_order='DMY' start_year=+10 time=$VARS.ACTION_DATA.stop_date}<br />-->
   {$VARS.ACTION_IMAGE}:<br />
   <input name="action_image" type="file" value="" size="30" /><br />
	{if $VARS.EDITING}
	{$VARS.DELETE_IMAGE}: {$VARS.ACTION_DATA.image}
   <input name="action_delete_image" type="checkbox" /><br />
	{/if}
	<input name="action_reset" type="reset" value="{$VARS.BUTTON_RESET}" />
	<input name="action_send" type="submit" value="{$VARS.BUTTON_SEND}" />
	
</form>
</div>
{literal}
<script>
  $(document).ready(function(){
    $("#addActionForm").tabs();
  });
</script>
{/literal}
{include file='engine:buttonback.tpl'}
</div>
