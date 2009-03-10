<div>
{foreach from=$VARS.PARTNERS_ARRAY item="PARTNER"}

{if $PARTNER.logo_file eq null}
<a href="{$PARTNER.url}" title="{$PARTNER.name}" target="_blank">{$PARTNER.name}</a>
{else}
   {if $PARTNER.logo_type eq 'image'}
   <a href="{$PARTNER.url}" title="{$PARTNER.name}" target="_blank" rel="nofollow">
      <img src="{$VARS.DIR_TO_IMAGES|cat:$PARTNER.logo_file}" title="{$VARS.DIR_TO_IMAGES|cat:$PARTNER.logo_file}" width="123" />
      {* html_image file=$VARS.DIR_TO_IMAGES|cat:$PARTNER.logo_file width=$VARS.LOGO_WIDTH *}
   </a>
   {elseif $PARTNER.logo_type eq 'flash'}
   <a href="{$PARTNER.url}" title="{$PARTNER.name}" target="_blank" rel="nofollow">
      <!--[if !IE]> -->
      <object type="application/x-shockwave-flash" data="{$VARS.DIR_TO_IMAGES|cat:$PARTNER.logo_file}" width="{$PARTNER.logo_width}px" height="{$PARTNER.logo_height}px">
         <!-- <![endif]-->
         <!--[if IE]>
         <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="{$PARTNER.logo_width}px" height="{$PARTNER.logo_height}px">
         <param name="movie" value="{$VARS.DIR_TO_IMAGES|cat:$PARTNER.logo_file}" />
         <!--><!--dgx-->
         <param name="loop" value="true" />
         <param name="menu" value="false" />
         <param name="wmode" value="transparent" />
         <param name="scale" value="noborder" />
         <!--  		Alternativni obsah-->
      </object>
      <!-- <![endif]-->
   </a>
   {/if}
{/if}
{/foreach}
<a href="{$VARS.PARTNERS_LINK}" title="{$VARS.PARTNERS_LINK_NAME}">{$VARS.PARTNERS_LINK_NAME}</a>
</div>