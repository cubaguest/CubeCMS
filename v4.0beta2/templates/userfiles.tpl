{if $FILES_ID eq null}
{assign var='FILES_ID' value=$VARS.USERFILES_ID}
{/if}
<div class="userFiles">
   <h5>{$VARS.USERFILES_LABEL_NAME} (<span id="userFilesCount">{$VARS.USERFILES_NUM_ROWS[$FILES_ID]}</span>)</h5>
   <script type="text/javascript">
      <!--
      document.write("<a id=\"uploadFileButton\">{$VARS.UPLOAD_FILE}</a>");
      document.write("<img style=\"display: none\" id=\"uploadFileStatus\" \n\
   src=\"images/progress.gif\" width=\"16\" height=\"16\" alt=\"progress\" />");
      //-->
   </script>
   <noscript>
      <form method="post" action="{$THIS_PAGE_LINK}" enctype="multipart/form-data">
         <input type="file" name="userfiles_new_file" value="" />
         <input type="submit" name="userfiles_send_file" value="{$VARS.BUTTON_USERFILE_SEND}"/>
      </form>
   </noscript>
   {literal}
   <script type="text/javascript">
      $(document).ready(function() {
         new Ajax_upload('#uploadFileButton', {
            action: '{/literal}{$VARS.AJAX_USERFILE_FILE}{literal}', 
            name: 'userfiles_new_file',
            autoSubmit: true,
            data: {
               userfiles_idItem : "{/literal}{$VARS.ID_ITEM}{literal}",
               userfiles_idArticle : "{/literal}{$VARS.ID_ARTICLE|default:$VARS.ID_ITEM}{literal}",
               action : 'addFile',
               userfiles_send_file: 1},
            onComplete : function(file, response){
               $('#uploadFileStatus').hide();
               alert( response );
               loadFiles();
            },
            onSubmit : function(file , ext){
               $('#uploadFileStatus').show();
            }

         });
      });

      function clearFiles(){
         $("#userFilesList tbody").html("");
      }

      function loadFiles(){
         clearFiles();
         var count = 0;
         $.getJSON(
         "{/literal}{$VARS.AJAX_USERFILE_FILE}?action=getFiles&idItem={$VARS.ID_ITEM}&idArticle={$VARS.ID_ARTICLE|default:$VARS.ID_ITEM}{literal}",
         function(jsondata){
            $.each(jsondata.files, function(i,ufile){
               count++;
               var tbl ="<tr><td>"+ufile.file+"</td>"
                  +"<td>"+ Math.round(ufile.size/1024 * 100)/100 + "KB</td>"
                  +"<td align=\"right\">"
                  +"<form action=\"{/literal}{$THIS_PAGE_LINK}{literal}\" method=\"post\"  onsubmit=\"return deleteFile('{/literal}{$VARS.CONFIRM_MESAGE_DELETE_FILE}{literal} - "+ufile.file+"', '"+ufile.id_file+"');\">"
                  +"<input type=\"hidden\" name=\"userfiles_id\" value=\""+ufile.id_file+"\" />"
                  +"<input type=\"submit\" name=\"userfiles_delete\" value=\"{/literal}{$VARS.BUTTON_USERFILE_DELETE}{literal}\" />"
                  +"</form>"
                  +"</td></tr>"
                  +"<tr><td colspan=\"3\">"
                  +"<a href=\""+ufile.link_show+"\" title=\"{/literal}{$VARS.FILE_LINK_TO_SHOW_NAME}{literal} - "+ufile.file+"\" target=\"_blank\">"+ufile.link_show+"</a>"
                  +"</td></tr>"
                  +"<tr><td colspan=\"3\">"
                  +"<a href=\""+ufile.link_download+"\" title=\"{/literal}{$VARS.FILE_LINK_TO_DOWNLOAD_NAME}{literal} - "+ufile.file+"\">"+ufile.link_download+"</a>"
                  +"</td></tr>"
                  +"<tr><td colspan=\"3\" style=\"border: 1px solid gray;\"></td></tr>";
               $(tbl).appendTo("#userFilesList tbody");
            });
            $("#userFilesCount").text(count);
         });
      };

      function deleteFile(name, idFile){
         if (confirm(name)){
            $.ajax({
               type: "POST",
               url: "{/literal}{$VARS.AJAX_USERFILE_FILE}{literal}",
               data: {
                  idFile : idFile,
                  action : 'deleteFile'},
               success: function(msg){
                  loadFiles();
                  alert( msg );
               }
            });
            return false;
         }
         return false;
      }
   </script>
   {/literal}
   <table id="userFilesList" border="0" cellpadding="2" cellspacing="2">
      <tbody>
         {foreach from=$VARS.USERFILES_ARRAY[$FILES_ID] item="FILE"}
         <tr>
            <td>{$FILE.file}</td>
            <td>{math equation="x / 1024" x=$FILE.size format="%.2f"}KB</td>
            <td align="right">
               <form action="{$THIS_PAGE_LINK}" method="post" onsubmit="return deleteFile('{$VARS.CONFIRM_MESAGE_DELETE_FILE} - {$FILE.file}', '{$FILE.id_file}');">
                  <input class="id_file" type="hidden" name="userfiles_id" value="{$FILE.id_file}" />
                  <input type="submit" name="userfiles_delete" value="{$VARS.BUTTON_USERFILE_DELETE}" />
               </form>
            </td>
         </tr>
         <tr>
            <td colspan="3">
               <a href="{$FILE.link_show}" title="{$VARS.FILE_LINK_TO_SHOW_NAME} - {$FILE.file}" target="_blank">{$FILE.link_show}</a>
            </td>
         </tr>
         <tr>
            <td colspan="3">
               <a href="{$FILE.link_download}" title="{$VARS.FILE_LINK_TO_DOWNLOAD_NAME} - {$FILE.file}">{$FILE.link_download}</a>
            </td>
         </tr>
         <tr>
            <td colspan="3" style="border: 1px solid gray;"></td>
         </tr>
         {/foreach}
      </tbody>
   </table>
</div>