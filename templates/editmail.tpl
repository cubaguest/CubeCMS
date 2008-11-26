{if $MAILS_ID eq null}
{assign var='MAILS_ID' value=$VARS.SENDMAILS_ID}
{/if}
<form action="{$THIS_PAGE_LINK}" method="post">
	<label>{$VARS.MAIL_SUBJECT}*:</label><br />
	<input name="sendmail_subject" type="text" size="30" maxlength="200" value="{$VARS.MAIL_TEXT_DETAIL.subject}" /><br />
	<label>{$VARS.MAIL_TEXT}*:</label><br />
	<textarea class="textarea" rows="5" cols="35" name="sendmail_text">{$VARS.MAIL_TEXT_DETAIL.text}</textarea><br />
	<label>{$VARS.MAIL_REPLAY_MAIL}*:</label><br />
	<input name="sendmail_replay_mail" type="text" size="28" maxlength="200" value="{$VARS.MAIL_TEXT_DETAIL.replay_mail}" />
<!--	<input name="sendmail_item" type="hidden" value="{$MAILS_ID}" />-->
	<input name="sendmail_in_db" type="hidden" value="{$VARS.MAIL_TEXT_IN_DB}" />
	<input name="sendmail_send_text" type="submit" value="{$VARS.BUTTON_SENDMAIL_SEND}" />
</form>
<br />
{$VARS.TAGS}:<br />
{foreach from=$VARS.MAIL_TAGS item="TAG"}
{$TAG.string} - {$TAG.label}<br /> 
{/foreach}
<br />
<br />