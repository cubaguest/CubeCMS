<div class="dwFiles">
   {foreach from=$VARS.DWFILES item='DWFILE'}
   <a href="{$DWFILE.download_file}" title="{if $DWFILE.label neq null}{$DWFILE.label}{else}{$DWFILE.file}{/if}">
      {if $DWFILE.label neq null}{$DWFILE.label}{else}{$DWFILE.file}{/if}
   </a>
   {if $VARS.EDITABLE eq ture}
   <form action="{$THIS_PAGE_LINK}" onsubmit="return Confirm('{$VARS.DELETE_DWFILE_CONFIRM_MESSAGE} - {if $DWFILE.label neq null}{$DWFILE.label}{else}{$DWFILE.file}{/if}')">
      <input type="hidden" name="dwfiles_id" value="{$DWFILE.id_file}" />
      <input name="dwfiles_delete" type="image" src="images/toolbox/remove.gif" title="{$VARS.BUTTON_DELETE_FILE}" />
   </form>
   {/if}
   <br />
   {foreachelse}
   {$VARS.NOT_ANY_FILE}
   {/foreach}
</div>