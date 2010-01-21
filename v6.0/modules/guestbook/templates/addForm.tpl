<div>
<form action="{$THIS_PAGE_LINK}" method="post">
	{$PRIVATE.GB_TOPIC}:*<br />
	<input type="text" size="40" maxlength="50" name="guestbook_topic" value="{$smarty.post.guestbook_topic}" /><br />
	{$PRIVATE.GB_NICK}:*<br />
	<input type="text" size="40" maxlength="50" name="guestbook_nick" value="{$smarty.post.guestbook_nick}" /><br />
	{$PRIVATE.GB_EMAIL}:*<br />
	<input type="text" size="40" maxlength="50" name="guestbook_email" value="{$smarty.post.guestbook_email}" /><br />
	{$PRIVATE.GB_TEXT}:*<br />
	<textarea name="guestbook_text" class="textarea" rows="5" cols="60">{$smarty.post.guestbook_text}</textarea><br />
	<img src="{$VARS.VERIFY_IMAGE}" alt="verifyImage" />
	<input type="text" name="guestbook_verify" value="" size="5" maxlength="5"/><br />
	<input name="guestbook_send" type="submit" value="{$PRIVATE.BUTTON_SEND}" />
</form>
</div>