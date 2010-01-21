{foreach from=$VARS.SPONSORS_ARRAY item='SPONSOR'}
<div class="sponsors">
{if $SPONSOR.logo_image neq null}
{html_image file=$VARS.DIR_TO_IMAGES|cat:$SPONSOR.logo_image}
{/if}
{$VARS.SPONSOR_NAME}: {$SPONSOR.name}<br />
{$VARS.SPONSOR_LABEL}: {$SPONSOR.label}<br />
{$VARS.SPONSOR_URL_NAME}: <a href="{$SPONSOR.url}" title="{$SPONSOR.url}" target="_blank">{$SPONSOR.url}</a><br />
{if $VARS.EDITABLE eq true}
<div class="form_buttons form_buttons_inline">
	<form action="{$SPONSOR.editlink}" method="post">
		<input type="hidden" name="sponsor_id" value="{$SPONSOR.id_sponsor}" />
		<input type="submit" name="sponsor_edit" value="{$VARS.LINK_TO_EDIT_SPONSORS_NAME}"/>
	</form>
	<form class="delete_form" action="{$THIS_PAGE_LINK}" method="post" onsubmit="return Confirm('{$VARS.DELETE_CONFIRM_MESSAGE} - {$SPONSOR.name}')">
		<input type="hidden" name="sponsor_id" value="{$SPONSOR.id_sponsor}" />
		<input type="submit" name="sponsor_delete" value="{$VARS.LINK_TO_DELETE_SPONSORS_NAME}"/>
	</form>
</div>

{/if}
<br />
</div>
<hr class="reseter" />
{foreachelse}
{$VARS.NOT_ANY_SPONSOR}
{/foreach}
