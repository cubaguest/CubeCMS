<div id="editGaleryContent">
   <p>{$VARS.ADD_NEW_GALERY_NAME}:</p>
   <ul class="langMenu">
      {foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
      <li><a href="#galeryLang{$smarty.foreach.lang.iteration}" {if $smarty.foreach.lang.first}class="selected"{/if}>{html_engine_image file=$MAIN_LANG_IMAGES_PATH|cat:$KEYLANG|cat:'.png'}{$LANG}</a></li>
      {/foreach}
   </ul>

   <div class="tabsContent">
      <form action="{$THIS_PAGE_LINK}" method="post" enctype="multipart/form-data">

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
         {html_select_date field_array='galery_date' field_order='DMY' start_year=+1 end_year=-10 time=$VARS.DATE_SELECT}<br />
         <br />
         <p>{$VARS.ADD_EXISTING_GALERY_NAME}:</p><br />
         
         {html_options name=galery_exist_id options=$VARS.GALERIES_LIST selected=$VARS.GALERY_DATA.exist_id}
         <br />
         {$VARS.PHOTO_FILE_LABEL}:<br />

         <table>
            <tr class="inputImages">
               <td>
                  <ul class="langMenu">
                     {foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
                     <li><a href="#photoLang_1_{$smarty.foreach.lang.iteration}" {if $smarty.foreach.lang.first}class="selected"{/if}>{html_engine_image file=$MAIN_LANG_IMAGES_PATH|cat:$KEYLANG|cat:'.png'}{$LANG}</a></li>
                     {/foreach}
                  </ul>

                  {foreach from=$APP_LANGS_NAMES key='KEYLANG' item='LANG' name='lang'}
                  <p id="photoLang_1_{$smarty.foreach.lang.iteration}" class="tabcontent">
                     <label>{$VARS.FORM_ADD_PHOTO_LABEL_LABEL}:</label>
                     <textarea name="galery_photo_label[1][{$KEYLANG}]" cols="50" rows="3" class="textarea"></textarea><br />
                  </p>
                  {/foreach}
                  <label>{$VARS.FORM_ADD_PHOTO_FILE_LABEL}:</label>
                  <input type="file" name="galery_photo_file[1]" size="16" />
               </td>
            </tr>
            <tr class="addInputTr">
               <td align="right">
                  <a title="{$VARS.ADD_INPUTS_FIELDS}" id="images_upload_add" style="cursor:pointer">{$VARS.ADD_INPUTS_FIELDS}</a>
               </td>
            </tr>
         </table>

         
         <input name="galery_reset" type="reset" value="{$VARS.BUTTON_RESET}" />
         <input name="galery_send" type="submit" value="{$VARS.BUTTON_SEND}" />

      </form>
   </div>
   {literal}
   <script>
      var index=2;
      var inputIndex = 2;
      $(document).ready(function(){
         $("#editGaleryContent ul.langMenu").tabs();
      });
      $('#images_upload_add').click(function(){
         var html= $('.inputImages:last').html();

         html = html.replace(/photoLang_[\d]+_([\d]+)/g, "photoLang_"+index+"_$1");
         html = html.replace(/\[\d\]+/g, '['+inputIndex+']');
         //html = html.replace("photoLang", 'photoLang'+index, "gi");
         index++;
         inputIndex++;
         //$('.inputImages:last').after($('.inputImages:last').clone(true).find('.inputImages').val(object).end());
         $('.addInputTr').before("<tr class=\"inputImages\">"+html+"</tr>");;
         $("#editGaleryContent ul.langMenu").tabs();
      });
   </script>
   {/literal}
</div>

{include file="engine:buttonback.tpl"}
