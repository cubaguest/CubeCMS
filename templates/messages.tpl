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
<p class="error_message text">
   {html_image file='./images/icons/coreerror.png'}
   {foreach from=$CORE_ERRORS key=ERRNO item=ERROR}
   {$ERROR_NAME}: ({$ERROR.code}) {$ERROR.name}. File: {$ERROR.file}. Line: {$ERROR.line}<br />
   {/foreach}
</p>
{/if}

{* Vypis chybovych hlasek MODULU *}
{if $ERRORS neq null}
<p class="error_message text">
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
