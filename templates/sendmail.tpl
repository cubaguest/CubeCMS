{if $MAILS_ID eq null}
{assign var='MAILS_ID' value=$VARS.SENDMAILS_ID}
{/if}
<div class="sendMails">
<h5>{$VARS.SENDMAIL_LABEL_NAME} ({$VARS.SENDMAILS_NUM_ROWS[$MAILS_ID]})</h5>
<form method="post" action="{$THIS_PAGE_LINK}">
	<input type="text" name="sendmail_mail" value="{$VARS.SEND_MAIL}" size="28" maxlength="60" />
	<input type="submit" name="sendmail_send" value="{$VARS.BUTTON_SENDMAIL_SEND}"/>
</form>

<table border="0" cellpadding="2" cellspacing="2">
<thead>
	<tr>
		<th colspan="2" align="left">{$VARS.MIAL_NAME}:</th>
	</tr>
</thead>
{foreach from=$VARS.SENDMAILS_ARRAY[$MAILS_ID] item="MAIL"}

<tbody>
	<tr>
	<td>{$MAIL.mail}</td>
	<td align="right">
		<form action="{$THIS_PAGE_LINK}" method="post" onsubmit="return Confirm('{$VARS.CONFIRM_MESAGE_DELETE} - {$MAIL.mail}')">
			<input type="hidden" name="sendmail_id" value="{$MAIL.id_mail}" />
			<input type="submit" name="sendmail_delete" value="{$VARS.BUTTON_SENDMAIL_DELETE}" />
		</form>
	</td>
	</tr>
</tbody>
{foreachelse}
<tr>
	<td colspan="2" align="center">
		{$VARS.NOT_ANY_MAIL}
	</td>
</tr>
{/foreach}
</table>
<br />
</div>