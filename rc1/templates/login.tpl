<div class="login_form" style="text-align: left">
	<form method="post" action="{$THIS_PAGE_LINK}">
	{if $USER_IS_LOGIN neq true}
	{literal}
	<input class="login_input" type="text" name="login_username" maxlength="20" id="login_panel" size="15" value="_intranet_login" onclick="if (this.value == '_intranet_login') { this.value = ''; }" onblur="if (this.value == '') { this.value = '_intranet_login'; }"/>
	<input type="password" name="login_passwd" maxlength="20" id="passwd_panel" size="15" value="Password" onclick="if (this.value == 'Password') { this.value = ''; }" onblur="if (this.value == '') { this.value = 'Password'; }"/>
	{/literal}
	<input id="login_submit" type="submit" name="login_submit" value="{$LOGIN_LOGIN_BUTTON_NAME}" />
	{else}
	<input id="login_submit" type="submit" name="logout_submit" value="{$LOGIN_LOGOUT_BUTTON_NAME}" />
	{/if}
	</form>
</div>