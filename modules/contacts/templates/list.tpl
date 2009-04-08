{include file=module:map.tpl}
{foreach from=$VARS.CONTACTS item="CONTACT" key=KEY}
{if $VARS.EDITABLE}
{include file="module:addButton.tpl"}
{/if}
<div class="contact contact{$TPLKEY}_{$KEY}">
   {if $VARS.EDITABLE}
   <div class="editbox editContactBox{$TPLKEY}_{$KEY}">
      <p class="upside"></p>
<div class="contentForm">
<form action="{$CONTACT.edit_link}" method="post">
<input type="image" name="contact_edit" value="" src="images/toolbox/edit.gif" title="{$VARS.LINK_TO_EDIT_CONTACT_NAME}">{**}
</form>{**}
<form action="{$THIS_PAGE_LINK}" method="post" onsubmit="return Confirm('{$VARS.DELETE_CONTACT_CONFIRM_MESSAGE} - {$CONTACT.name}')">{**}
<input type="hidden" value="{$CONTACT.id_contact}" name="contact_id" />{**}
<input type="image" name="contact_delete" value="" src="images/toolbox/remove.gif" title="{$VARS.LINK_TO_DELETE_CONTACT_NAME}">{**}
</form>
</div>
      <p class="downside"></p>
   </div>
   {/if}
   {if $AREA_NAME neq $CONTACT.area_name}
   <p class="contactAreaName" id="{$CONTACT.area_name|ascii:true|lower}">{$CONTACT.area_name}</p>
   {assign var=AREA_NAME value=$CONTACT.area_name}
   {/if}

   <p class="smallFont">{$CONTACT.city_name}</p>
   <h2>{$CONTACT.name}</h2>
   <a href="{$VARS.IMAGES_DIR|cat:$CONTACT.file}" title="{$CONTACT.file}">
      {html_image file=$VARS.IMAGES_SMALL_DIR|cat:$CONTACT.file class='contactImage'}
   </a>
   {$CONTACT.text}
   {literal}
   <script type="text/javascript">
      //$(document).ready(function(){
      $("div#contactsConteiner{/literal}{$TPLKEY}{literal} div.contact{/literal}{$TPLKEY}_{$KEY}{literal}").hover(
      function(){$("div#contactsConteiner{/literal}{$TPLKEY}{literal} div.editContactBox{/literal}{$TPLKEY}_{$KEY}{literal}").fadeIn(100);},
      function(){$("div#contactsConteiner{/literal}{$TPLKEY}{literal} div.editContactBox{/literal}{$TPLKEY}_{$KEY}{literal}").fadeOut(300);}
   );
      //});
   </script>
   {/literal}
   <br class="reseter" />
</div>
<div class="hr"></div>
{/foreach}
<br />
{literal}
<script type="text/javascript">
   $(function() {
      $('.contact>a').lightBox();
   });
</script>
{/literal}
