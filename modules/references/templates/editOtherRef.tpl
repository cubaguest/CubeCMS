<div id="editOtherRefForm">
   <h2>{$VARS.EDIT_OTHER_REFERENCE_LABEL}</h2>
   <ul class="langMenu">
      {foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
      <li><a href="#refLang{$smarty.foreach.lang.iteration}" {if $smarty.foreach.lang.first}
             class="selected"{/if}>{html_engine_image file=$MAIN_LANG_IMAGES_PATH|cat:$KEYLANG|cat:'.png'}{$LANG}</a></li>
      {/foreach}
   </ul>

   <div class="tabsContent">
      <form method="post" action="{$THIS_PAGE_LINK}">
         {foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
         <p id="refLang{$smarty.foreach.lang.iteration}" class="tabcontent">
            {$VARS.REFERENCE_OTEHR_TEXT_NAME}{if $smarty.foreach.lang.first}*{/if}:<br />
            <textarea class="textarea_tinymce textarea{if $VARS.ERROR_ITEMS.text.$KEYLANG eq true} badItem{/if}"
                  rows="20" cols="60" name="reference_other_text[{$KEYLANG}]">{$VARS.REFERENCE_OTHER_DATA.text.$KEYLANG}</textarea>
         </p>
         {/foreach}
         <br />
         <input name="reference_other_reset" type="reset" value="{$VARS.BUTTON_RESET}" />
         <input name="reference_other_send" type="submit" value="{$VARS.BUTTON_SEND}" />

      </form>
   </div>
   {literal}
   <script>
      $(document).ready(function(){
         $("#editOtherRefForm > ul").tabs();
      });
   </script>
   {/literal}
</div>
{include file="engine:buttonback.tpl"}