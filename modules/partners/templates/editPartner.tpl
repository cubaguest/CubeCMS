<div id="editPartnerForm">
<ul class="langMenu">
{foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
<li><a href="#newsLang{$smarty.foreach.lang.iteration}" {if $smarty.foreach.lang.first}
class="selected"{/if}>{html_engine_image file=$MAIN_LANG_IMAGES_PATH|cat:$KEYLANG|cat:'.png'}{$LANG}</a></li>
{/foreach}
</ul>

<form action="{$THIS_PAGE_LINK}" method="post" enctype="multipart/form-data">
	{$VARS.PARTNER_NAME}{if $smarty.foreach.sponsorLang.first}*{/if}:<br />
	<input type="text" size="40" maxlength="50" name="partner_name" value="{$VARS.PARTNER_DATA.name}" {if $VARS.ERROR_ITEMS.name eq true}class="badItem"{/if}/><br />

   {foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
   <p id="newsLang{$smarty.foreach.lang.iteration}" class="tabcontent">
      {$VARS.PARTNER_LABEL}:<br />
      <textarea rows="5" cols="60" name="partner_label[{$KEYLANG}]" class="textarea{if $VARS.ERROR_ITEMS.text.$KEYLANG eq true} badItem{/if}">{$VARS.PARTNER_DATA.label.$KEYLANG}</textarea>
   </p>
	{/foreach}

   {$VARS.PARTNER_URL_NAME}:<br />
	<input type="text" size="40" maxlength="50" name="partner_url" value="{$VARS.PARTNER_DATA.url}" /><br />
	{$VARS.PARTNER_LOGO_IMAGE}:<br />
	<input type="file" size="40" maxlength="50" name="partner_logo_file" /><br />
	
	{if $VARS.EDIT_PARTNER eq true AND $VARS.PARTNER_LOGO_FILE neq null}
   {$VARS.DELTE_IMAGE_LABEL}&nbsp;&#132;{$VARS.PARTNER_LOGO_FILE}&#148;&nbsp;<input name="partner_delete_image" type="checkbox" /><br />
	<input name="partner_id" type="hidden" value="{$VARS.PARTNER_DATA.id}" />
	{/if}
	
	<input name="partner_reset" type="reset" value="{$VARS.BUTTON_RESET}" />
	<input name="partner_send" type="submit" value="{$VARS.BUTTON_SEND}" />
	
</form>


{if $VARS.SPONSOR_IMAGE neq null}
{html_image file=$VARS.DIR_TO_IMAGES|cat:$VARS.SPONSOR_IMAGE}
{/if}


{literal}
<script>
  $(document).ready(function(){
    $("#editPartnerForm > ul").tabs();
  });
</script>
{/literal}
{include file='engine:buttonback.tpl'}
</div>