<div class="login_form_module">
<div>
<form method="post" action="{$THIS_PAGE_LINK}">
<p class="login_form_label"><img src="./images/icons/key.png" width="32" height="17" /><b>{$VARS.LOGIN_FORM_LABEL}</b></p>
<table>
	<tr>
		<td>{$VARS.LOGIN_USER_NAME}:</td>
		<td><input class="login_input" type="text" name="login_username" maxlength="20" id="login_panel" size="15" value="{$smarty.post.login_username}" /></td>
	</tr>
	<tr>
		<td>{$VARS.LOGIN_USER_PASSWORD}:</td>
		<td><input type="password" name="login_passwd" maxlength="20" id="passwd_panel" size="15" value="" /></td>
	</tr>
	<tr>
		<td colspan="2" align="right"><input id="login_submit" type="submit" name="login_submit" value="{$VARS.LOGIN_BUTTON}" /></td>
	</tr>
</table>
</form>
</div>
</div>