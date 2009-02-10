<div id="addPhotoContent">
   <form action="{$THIS_PAGE_LINK}" method="post" enctype="multipart/form-data">
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
                  <textarea name="photo_label[1][{$KEYLANG}]" cols="50" rows="3" class="textarea"></textarea><br />
               </p>
               {/foreach}
               <label>{$VARS.FORM_ADD_PHOTO_FILE_LABEL}:</label>
               <input type="file" name="photo_file[1]" size="16" />
            </td>
         </tr>
         <tr class="addInputTr">
            <td align="right">
               <a title="{$VARS.ADD_INPUTS_FIELDS}" id="images_upload_add" style="cursor:pointer">{$VARS.ADD_INPUTS_FIELDS}</a>
            </td>
         </tr>
      </table>


      <input name="photo_reset" type="reset" value="{$VARS.BUTTON_RESET}" />
      <input name="photo_send" type="submit" value="{$VARS.BUTTON_SEND}" />

   </form>
   {literal}
   <script>
      var index=2;
      var inputIndex = 2;
      $(document).ready(function(){
         $("#addPhotoContent ul.langMenu").tabs();
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
         $("#addPhotoContent ul.langMenu").tabs();
      });
   </script>
   {/literal}
</div>

{include file="engine:buttonback.tpl"}