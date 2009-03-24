<div id="galeryPhotos">
   {if $VARS.EDITABLE eq true}
   <div class="editbox">
      <p class="upside"></p>
      <div class="contentForm"><form action="{$VARS.link_add_photo}" method="post"><input type="image" name="galery_add" value="" src="images/toolbox/add.gif" title="{$VARS.LINK_TO_ADD_PHOTOS_NAME}"></form><form action="{$VARS.link_edit_galery}" method="post"><input type="image" name="galery_edit" value="" src="images/toolbox/edit.gif" title="{$VARS.LINK_TO_EDIT_GALERY_NAME}"></form><form action="{$THIS_PAGE_LINK}" method="post" onsubmit="return Confirm('{$VARS.DELETE_GALERY_CONFIRM_MESSAGE} - {$VARS.GALERY_DATA.label}')"><input type="hidden" value="{$VARS.GALERY_DATA.id_galery}" name="galery_id" /><input type="image" name="galery_delete" value="" src="images/toolbox/remove.gif" title="{$VARS.LINK_TO_DELETE_GALERY_NAME}"></form></div>
      <p class="downside"></p>
   </div>
   {literal}
   <script type="text/javascript">
      //$(document).ready(function(){
      $("#photogaleryConteiner{/literal}{$TPLKEY}{literal}").hover(
      function(){$("div#photogaleryConteiner{/literal}{$TPLKEY}{literal} div.editbox").fadeIn(100);},
      function(){$("div#photogaleryConteiner{/literal}{$TPLKEY}{literal} div.editbox").fadeOut(300);}
   );
      //});
   </script>
   {/literal}
   {/if}
   {foreach from=$VARS.PHOTOS_LIST item='PHOTO' name='photos'}
   <div class="galeryPhoto">
      {if $VARS.EDITABLE eq true}
      <div class="deletePhotoForm">
         <form action="{$THIS_PAGE_LINK}" method="post" onsubmit="return Confirm('{$VARS.DELETE_PHOTO_CONFIRM_MESSAGE} - {if $PHOTO.label neq null}{$PHOTO.label}{else}{$PHOTO.file}{/if}')">
            <input name="photo_id" type="hidden" value="{$PHOTO.id_photo}" />
            <input name="photo_delete" type="image" src="images/toolbox/remove.gif" title="{$VARS.BUTTON_DELETE_PHOTO}" />
         </form>
      </div>
      {/if}
      <a href="{$VARS.FULL_DIR|cat:$PHOTO.file}" title="{$PHOTO.label}">{html_image file=$VARS.SMALL_DIR|cat:$PHOTO.file alt=$PHOTO.label}</a>
   </div>

   {foreachelse}
   {$VARS.NOT_ANY_PHOTOS}
   {/foreach}
   {literal}
   <script type="text/javascript">
      $(function() {
         $('.galeryPhoto>a').lightBox();
      });
   </script>
   {/literal}
   <br class="reseter" />
</div>
{include file="engine:buttonback.tpl"}
