{* Vypis sablon modulu *}
{foreach from="$MODULES_TEMPLATES" item="TEMPLATE" key="TPLKEY" name ='mtemplates'}

{* upravit *}
{if $TEMPLATE.LABEL eq null AND $smarty.foreach.mtemplates.first}
<h1>&gt;&gt;{$MAIN_CATEGORY_TITLE}{if $TEMPLATE.SUBLABEL neq null} - {$TEMPLATE.SUBLABEL}{/if}</h1>
{else}
<h1>&gt;&gt;{$TEMPLATE.LABEL}{if $TEMPLATE.SUBLABEL neq null} - {$TEMPLATE.SUBLABEL}{/if}</h1>
{/if}
<!-- sof Module box -->
<div id="{$TEMPLATE.IDENT}Conteiner{$TPLKEY}" class="{$TEMPLATE.IDENT}ContainerClass moduleBox">
   {foreach from=$TEMPLATE.TEMPLATES item=tplfile}
   {assign var='MODULE_TPL_FILE' value=$tplfile.FILE}{* FOR LOADING MADULE TEPLATE WITH INCLUDE *}

   {include file=$tplfile.FILE VARS=$TEMPLATE.VARS PRIVATE=$TEMPLATE.VARS ID=$FILE.ID}

   {/foreach}
   <hr class="reseter" />
</div>
<!-- eof Module box -->
{/foreach}