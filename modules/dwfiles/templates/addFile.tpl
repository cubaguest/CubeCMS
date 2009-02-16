<div id="addFileForm">
<ul class="langMenu">
{foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
<li><a href="#textLang{$smarty.foreach.lang.iteration}" {if $smarty.foreach.lang.first}
class="selected"{/if}>{html_engine_image file=$MAIN_LANG_IMAGES_PATH|cat:$KEYLANG|cat:'.png'}{$LANG}</a></li>
{/foreach}
</ul>

<div class="tabsContent">
<form method="post" action="{$THIS_PAGE_LINK}" enctype="multipart/form-data">
	{foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
	<p id="textLang{$smarty.foreach.lang.iteration}" class="tabcontent">
		{$VARS.FILE_LABEL_LABEL}{if $smarty.foreach.lang.first}{/if}:
      <input name="dwfiles_label[{$KEYLANG}]" type="text" value="{$VARS.FILE_DATA.label.$KEYLANG}" {if $VARS.ERROR_ITEMS.label.$KEYLANG eq true}class="badItem"{/if} />
	</p>
	{/foreach}
   {$VARS.FILE_LABEL}*:
   <input name="dwfiles_file" type="file" value="" /><br /><br />
	<input name="dwfiles_reset" type="reset" value="{$VARS.BUTTON_RESET}" />
	<input name="dwfiles_send" type="submit" value="{$VARS.BUTTON_SEND}" />
	
</form>
</div>
{literal}
<script>
  $(document).ready(function(){
    $("#addFileForm > ul").tabs();
  });
</script>
{/literal}
</div>
{include file="engine:buttonback.tpl"}