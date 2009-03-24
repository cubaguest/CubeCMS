{if $VARS.EDITABLE eq true}
<div class="editbox" id="editBox{$TPLKEY}">
   <p class="upside"></p>
   <div class="contentForm"><form action="{$VARS.LINK_TO_ADD}" method="post"><input type="image" name="add" value="" src="images/toolbox/add.gif" title="{$VARS.LINK_TO_ADD_NAME}" /></form></div>
   <p class="downside"></p>
</div>
{literal}
<script type="text/javascript">
   //$(document).ready(function(){
   $("div#partnersConteiner{/literal}{$TPLKEY}{literal}").hover(
   function(){$("div#partnersConteiner{/literal}{$TPLKEY}{literal} div#editBox{/literal}{$TPLKEY}{literal}").fadeIn(100);},
   function(){$("div#partnersConteiner{/literal}{$TPLKEY}{literal} div#editBox{/literal}{$TPLKEY}{literal}").fadeOut(300);}
);
   //});
</script>
{/literal}
{/if}
<div>
   {* pole partneru *}
   {foreach from=$VARS.PARTNERS_ARRAY item='PARTNER' key='KEY'}
   <div id="partner{$KEY}" class="partner">
      <h2>{$PARTNER.name}</h2>

      {if $PARTNER.logo_file neq null}
      <div class="partnerImage">
         <a href="{$PARTNER.url}" title="{$PARTNER.name}" target="_blank">
            {if $PARTNER.logo_type eq 'image'}
            {html_image file=$VARS.LOGO_DIR|cat:$PARTNER.logo_file}
            {elseif $PARTNER.logo_type eq 'flash'}
            <!--[if !IE]> -->
            <object type="application/x-shockwave-flash" data="{$VARS.LOGO_DIR|cat:$PARTNER.logo_file}" width="{$PARTNER.logo_width}px" height="{$PARTNER.logo_height}px">
               <!-- <![endif]-->
               <!--[if IE]>
               <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="{$PARTNER.logo_width}px" height="{$PARTNER.logo_height}px">
               <param name="movie" value="{$VARS.LOGO_DIR|cat:$PARTNER.logo_file}" />
               <!--><!--dgx-->
               <param name="loop" value="true" />
               <param name="menu" value="false" />
               <param name="wmode" value="transparent" />
               <param name="scale" value="noborder" />
               <!--  		Alternativni obsah-->
            </object>
            <!-- <![endif]-->
            {/if}
         </a>
      </div>
      {/if}

      {$PARTNER.label}
      <br />
      <hr class="reseter" />

      {if $PARTNER.url neq null}
      <a href="{$PARTNER.url}" title="{$PARTNER.name}" target="_blank">{$PARTNER.url}</a>
      {/if}

      {if $VARS.EDITABLE eq true}
      <div class="editbox" id="editPartnerBox{$KEY}">
         <p class="upside"></p>
         <div class="contentForm"><form action="{$PARTNER.linkEdit}" method="post"><input type="image" name="partner_edit" value="" src="images/toolbox/edit.gif" title="{$VARS.LINK_TO_EDIT_NAME}" /></form><form action="{$THIS_PAGE_LINK}" method="post" onsubmit="return Confirm('{$VARS.DELETE_CONFIRM_MESSAGE} - {$PARTNER.name}')"><input type="image" name="partner_delete" value="" src="images/toolbox/remove.gif" title="{$VARS.LINK_TO_REMOVE_NAME}" /><input type="hidden" name="partner_id" value="{$PARTNER.id_partner}" /></form></div>
         <p class="downside"></p>
      </div>
      {literal}
      <script type="text/javascript">
         //$(document).ready(function(){
         $("div#partner{/literal}{$KEY}{literal}").hover(
         function(){$("div#editPartnerBox{/literal}{$KEY}{literal}").fadeIn(100);},
         function(){$("div#editPartnerBox{/literal}{$KEY}{literal}").fadeOut(300);}
      );
         //});
      </script>
      {/literal}
      {/if}
   </div>
   {foreachelse}
   {$VARS.NOT_ANY_PARTNER}
   {/foreach}
</div>
