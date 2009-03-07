{* Vypis hlasek *}
{if $MESSAGES neq null}
<p class="message text">
   {html_image file='./images/icons/accept.png'}
   {foreach from=$MESSAGES key=MESSAGESNO item=MESSAGE}
   {$MESSAGE}<br />
   {/foreach}
</p>
{/if}

{* Vypis chybovych hlasek ENGINU *}
{if $CORE_ERRORS_EMPTY neq true}
<div class="errorMessage text">
   {html_image file='./images/icons/coreerror.png'}
   {$ERROR_NAME}:<br />
   {foreach from=$CORE_ERRORS key=ERRNO item=ERROR}
      {$ERROR.name}: (<i>{$ERROR.code}</i>) <b>{$ERROR.message}.</b> {$ERROR_IN_FILE}: {$ERROR.file} {$ERROR_IN_FILE_LINE}: {$ERROR.line}<br />
      {if $DEBUG_MODE}
         {foreach from=$ERROR.trace item=ERRTR}
         <p class="errTrMargin">
            {$ERRTR.class}{$ERRTR.type}{$ERRTR.function} {$ERROR_IN_FILE}:{$ERRTR.file} {$ERROR_IN_FILE_LINE}:{$ERRTR.line}
         </p>
         {/foreach}
      {/if}
   {/foreach}
</div>
{/if}

{* Vypis chybovych hlasek MODULU *}
{if $ERRORS neq null}
<p class="errorMessage text">
   {html_image file='./images/icons/error.png'}
   {foreach from=$ERRORS key=ERRNO item=ERROR}
   {if $DEBUG_MODE == true}
   {$ERROR_MODULE_NAME} {$ERROR}<br />
   {else}
   {$ERROR}<br />
   {/if}
   {/foreach}
</p>
{/if}
