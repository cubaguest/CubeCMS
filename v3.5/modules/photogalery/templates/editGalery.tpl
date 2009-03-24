<div id="editGaleryContent">
   <ul class="langMenu">
      {foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
      <li><a href="#galeryLang{$smarty.foreach.lang.iteration}" {if $smarty.foreach.lang.first}class="selected"{/if}>{html_engine_image file=$MAIN_LANG_IMAGES_PATH|cat:$KEYLANG|cat:'.png'}{$LANG}</a></li>
      {/foreach}
   </ul>

   <div class="tabsContent">
      <form action="{$THIS_PAGE_LINK}" method="post">

         {foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
         <p id="galeryLang{$smarty.foreach.lang.iteration}" class="tabcontent">
            {$VARS.GALERY_LABEL_NAME}{if $smarty.foreach.lang.first}*{/if}:<br />
            <input type="text" size="40" maxlength="50" name="galery_label[{$KEYLANG}]" value="{$VARS.GALERY_DATA.label.$KEYLANG}"  {if $VARS.ERROR_ITEMS.name eq true}class="badItem"{/if}/><br />
            {$VARS.GALERY_TEXT_NAME}:<br />
            <textarea rows="5" cols="60" name="galery_text[{$KEYLANG}]" class="textarea">{$VARS.GALERY_DATA.text.$KEYLANG}</textarea>
         </p>
         {/foreach}
         <br />
         {$VARS.DATE_SELECT_NAME}:&nbsp;
         {html_select_date field_array='galery_date' field_order='DMY' start_year=+1 end_year=-10 time=$VARS.GALERY_DATA.date}<br />
         <br />
         {if $VARS.GALERY_ID neq null}
         <input name="galery_id" type="hidden" value="{$VARS.GALERY_ID}" />
         {/if}
         <br />
         <input name="galery_reset" type="reset" value="{$VARS.BUTTON_RESET}" />
         <input name="galery_send" type="submit" value="{$VARS.BUTTON_SEND}" />

      </form>
   </div>
   {literal}
   <script>
      $(document).ready(function(){
         $("#editGaleryContent ul.langMenu").tabs();
      });
   </script>
   {/literal}
</div>
{include file="engine:buttonback.tpl"}