<div id="addContactForm" class="content">
<h2>{$VARS.ADD_REFERENCE_LABEL}</h2>
<ul class="langMenu">
{foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
<li><a href="#contactLang{$smarty.foreach.lang.iteration}" {if $smarty.foreach.lang.first}
class="selected"{/if}>{html_engine_image file=$MAIN_LANG_IMAGES_PATH|cat:$KEYLANG|cat:'.png'}{$LANG}</a></li>
{/foreach}
</ul>

<div class="tabsContent">
<form method="post" action="{$THIS_PAGE_LINK}" enctype="multipart/form-data">
	{foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
	<p id="contactLang{$smarty.foreach.lang.iteration}" class="tabcontent">
		{$VARS.CONTACT_LABEL_NAME}{if $smarty.foreach.lang.first}*{/if}:<br />
		<input {if $VARS.ERROR_ITEMS.label.$KEYLANG eq true}class="badItem"{/if} type="text" size="40" maxlength="50" name="contact_name[{$KEYLANG}]" value="{$VARS.CONTACT_DATA.name.$KEYLANG}" /><br />
		{$VARS.CONTACT_TEXT_NAME}{if $smarty.foreach.lang.first}*{/if}:<br />
		<textarea rows="15" cols="60" name="contact_text[{$KEYLANG}]" class="textarea{if $VARS.ERROR_ITEMS.text.$KEYLANG eq true} badItem{/if}">{$VARS.CONTACT_DATA.text.$KEYLANG}</textarea>
	</p>
	{/foreach}
   <label>{$VARS.CONTACT_IMAGE_LABEL}</label><br />
   <input type="file" name="contact_file" size="30" />
	<br />
   <label>{$VARS.CONTACT_CITY_LABEL}</label><br />
   <select name="contact_id_city">
       <option value='0'>--</option>
      {html_options options=$VARS.AREAS selected=$VARS.SELECT_AREA}
   </select>
	<br />
	<br />
	{if $VARS.SELECTED_ID_CONTACT neq null}
	<input name="contact_id" type="hidden" value="{$VARS.SELECTED_ID_CONTACT}" />
	{/if}
	<input name="contact_reset" type="reset" value="{$VARS.BUTTON_RESET}" />
	<input name="contact_send" type="submit" value="{$VARS.BUTTON_SEND}" />
	
</form>
</div>
{literal}
<script>
  $(document).ready(function(){
    $("#addContactForm").tabs();
  });
</script>
{/literal}
{include file='engine:buttonback.tpl'}
</div>
