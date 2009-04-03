{if $IMAGES_ID eq null}
{assign var='IMAGES_ID' value=$VARS.USERIMAGES_ID}
{/if}
<div class="userImages">
   <h5>{$VARS.USERMIAGES_LABEL_NAME} (<span id="userImagesCount">{$VARS.USERIMAGES_NUM_ROWS[$IMAGES_ID]}</span>)</h5>
   <script type="text/javascript">
      <!--
      document.write("<a id=\"uploadImageButton\">{$VARS.UPLOAD_IMAGE}</a>");
      document.write("<img style=\"display: none\" id=\"uploadImageStatus\" \n\
   src=\"images/progress.gif\" width=\"16\" height=\"16\" alt=\"progress\" />");
      //-->
   </script>
   <noscript>
      <form method="post" action="{$THIS_PAGE_LINK}" enctype="multipart/form-data">
         <input type="file" name="userimages_new_file" value="" />
         <input type="submit" name="userimages_send_file" value="{$VARS.BUTTON_USERIMAGE_SEND}"/>
      </form>
   </noscript>

{literal}
   <script type="text/javascript">
      $(document).ready(function() {
         new Ajax_upload('#uploadImageButton', {
            action: '{/literal}{$VARS.AJAX_USERIMAGE_FILE}{literal}',
            name: 'userimages_new_file',
            autoSubmit: true,
            data: {
               userimages_idItem : "{/literal}{$VARS.ID_ITEM}{literal}",
               userimages_idArticle : "{/literal}{$VARS.ID_ARTICLE|default:$VARS.ID_ITEM}{literal}",
               action : 'addImage',
               userimages_send_file: 1},
            onComplete : function(file, response){
               $('#uploadImageStatus').hide();
               alert( response );
               loadImages();
            },
            onSubmit : function(file , ext){
               $('#uploadImageStatus').show();
            }

         });
      });

      function clearImages(){
         $("#userImagesList tbody").html("");
      }

      function loadImages(){
         clearImages();
         var count = 0;
         $.getJSON(
         "{/literal}{$VARS.AJAX_USERIMAGE_FILE}?action=getImages&idItem={$VARS.ID_ITEM}{literal}",
         function(jsondata){
            $.each(jsondata.images, function(i,uimage){
               count++;
               var tbl ="<tr><td rowspan=\"3\">"
                  +"<a href=\""+uimage.link_show+"\" rel=\"lightbox\" title=\""+uimage.file+"\"><img src=\""+uimage.link_small+"\" alt=\""+uimage.file+"\"</a>"
                  +"</td><td>"+uimage.file+"</td>"
                  +"<td>"+ Math.round(uimage.size/1024 * 100)/100 + "KB</td>"
                  +"<td align=\"right\">"
                  +"<form action=\"{/literal}{$THIS_PAGE_LINK}{literal}\" method=\"post\" onsubmit=\"return deleteImage('{/literal}{$VARS.CONFIRM_MESAGE_DELETE_IMAGE}{literal} - "+uimage.file+"', '"+uimage.id_file+"');\">"
                  +"<input type=\"hidden\" name=\"userimages_id\" value=\""+uimage.id_file+"\" />"
                  +"<input type=\"submit\" name=\"userimages_delete\" value=\"{/literal}{$VARS.BUTTON_USERIMAGE_DELETE}{literal}\" />"
                  +"</form></td></tr>"
                  +"<tr><td colspan=\"3\">"+uimage.width+" x "+uimage.height+"px</td></tr>"
                  +"<tr><td colspan=\"3\">"
                  +"<a href=\""+uimage.link_show+"\" title=\"{/literal}{$VARS.IMAGE_LINK_TO_SHOW_NAME}{literal} - "+uimage.file+" target=\"_blank\">"+uimage.link_show+"</a>"
                  +"</td></tr>"
                  +"<tr><td colspan=\"4\" style=\"border: 1px solid gray;\"></td></tr>";

               $(tbl).appendTo("#userImagesList tbody");
               $("#userImagesCount").text(count);
            });
         });
         return false;
      };

      function deleteImage(name, idImage){
         if (confirm(name)){
            $.ajax({
               type: "POST",
               url: "{/literal}{$VARS.AJAX_USERIMAGE_FILE}{literal}",
               data: {
                  idImage : idImage,
                  action : 'deleteImage'},
               success: function(msg){
                  loadImages();
                  alert( msg );
               }
            });
            return false;
         }
         return false;
      }
   </script>
   {/literal}

<table id="userImagesList" border="0" cellpadding="2" cellspacing="2">
      <tbody>
         {foreach from=$VARS.USERIMAGES_ARRAY[$IMAGES_ID] item="IMAGE"}
         <tr>
            <td rowspan="3">
               <a href="{$IMAGE.link_show}" rel="lightbox" title="{$IMAGE.file}">{html_image file=$IMAGE.link_small}</a>
            </td>
            <td>{$IMAGE.file}</td>
            <td>{math equation="x/1024" x=$IMAGE.size format="%.2f"}KB</td>
            <td align="right">
               <form action="{$THIS_PAGE_LINK}" method="post" onsubmit="return deleteImage('{$VARS.CONFIRM_MESAGE_DELETE_IMAGE} - {$IMAGE.file}', '{$IMAGE.id_file}');">
                  <input type="hidden" name="userimages_id" value="{$IMAGE.id_file}" />
                  <input type="submit" name="userimages_delete" value="{$VARS.BUTTON_USERIMAGE_DELETE}" />
               </form>
            </td>
         </tr>
         <tr>
            <td colspan="3">{$IMAGE.width} x {$IMAGE.height}px</td>
         </tr>
         <tr>
            <td colspan="3">
               <a href="{$IMAGE.link_show}" title="{$VARS.IMAGE_LINK_TO_SHOW_NAME} - {$IMAGE.file}" target="_blank">{$IMAGE.link_show}</a>
            </td>
         </tr>
         <tr>
            <td colspan="4" style="border: 1px solid gray;"></td>
         </tr>
         {/foreach}
      </tbody>
   </table>
</div>