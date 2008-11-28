<div>
<form action="{$THIS_PAGE_LINK}" method="post">
	{$PRIVATE.GB_TOPIC}:*<br />
	<input type="text" size="40" maxlength="50" name="guestbook_topic" value="" /><br />
	{$PRIVATE.GB_NICK}:*<br />
	<input type="text" size="40" maxlength="50" name="guestbook_nick" value="" /><br />
	{$PRIVATE.GB_EMAIL}:*<br />
	<input type="text" size="40" maxlength="50" name="guestbook_email" value="" /><br />
	{$PRIVATE.GB_TEXT}:*<br />
	<textarea name="guestbook_text" class="textarea" rows="5" cols="60"></textarea><br />
	
	<input name="guestbook_send" type="submit" value="{$PRIVATE.BUTTON_SEND}" />

	<input type="text" size="40" maxlength="50" name="guestbook_pokus[en]" value="" /><br />
	<input type="text" size="40" maxlength="50" name="guestbook_pokus[cs]" value="" /><br />
	<input type="text" size="40" maxlength="50" name="guestbook_pokus[de]" value="" /><br />
</form>
</div>