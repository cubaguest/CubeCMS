<div id="editProductForm">
<ul class="langMenu">
{foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
<li><a href="#productLang{$smarty.foreach.lang.iteration}" {if $smarty.foreach.lang.first}
class="selected"{/if}>{html_engine_image file=$MAIN_LANG_IMAGES_PATH|cat:$KEYLANG|cat:'.png'}{$LANG}</a></li>
{/foreach}
</ul>

<div class="tabsContent">
<form method="post" action="{$THIS_PAGE_LINK}" enctype="multipart/form-data">
	{foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
	<p id="productLang{$smarty.foreach.lang.iteration}" class="tabcontent">
      <label>{$VARS.PRODUCT_LABEL_NAME}{if $smarty.foreach.lang.first}*{/if}:</label><br />
		<input {if $VARS.ERROR_ITEMS.label.$KEYLANG eq true}class="badItem"{/if} type="text" size="40" maxlength="50" name="product_label[{$KEYLANG}]" value="{$VARS.PRODUCT_DATA.label.$KEYLANG}" /><br />
      <label>{$VARS.PRODUCT_TEXT_NAME}{if $smarty.foreach.lang.first}*{/if}:</label><br />
		<textarea rows="15" cols="60" name="product_text[{$KEYLANG}]" class="textarea{if $VARS.ERROR_ITEMS.text.$KEYLANG eq true} badItem{/if}">{$VARS.PRODUCT_DATA.text.$KEYLANG}</textarea>
	</p>
	{/foreach}
	<br />
   <label>{$VARS.PRODUCT_IMAGE_NAME}:</label><br />
   <input name="product_image" type="file" value="" size="30" {if $VARS.ERROR_ITEMS.image eq true}class="badItem"{/if}/><br />
	{if $VARS.SELECTED_ID_PRODUCT neq null}
	<input name="product_id" type="hidden" value="{$VARS.SELECTED_ID_PRODUCT}" />
	{/if}
	<input name="product_reset" type="reset" value="{$VARS.BUTTON_RESET}" />
	<input name="product_send" type="submit" value="{$VARS.BUTTON_SEND}" />
	{include file=engine:help.tpl helpfile='edit' module='products'}
</form>
</div>
{literal}
<script>
  $(document).ready(function(){
    $("#editProductForm").tabs();
  });
</script>
{/literal}
{if $VARS.LIGHTBOX}
{literal}
<script type="text/javascript">
$(document).ready(function() { $('a[rel*=lightbox]').lightBox(); });
</script>
{/literal}
{/if}
{include file='engine:buttonback.tpl'}
</div>
