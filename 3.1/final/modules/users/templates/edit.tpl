{if $VARS.USER_DETAIL_ARRAY.surname neq null}<h3>{$VARS.USER_DETAIL_ARRAY.surname}, {$VARS.USER_DETAIL_ARRAY.name}</h3>{/if}
<form method="post" action="{$THIS_PAGE_LINK}">
<table>
	<tr>
		<td colspan="2"><hr /></td>
	</tr>
	<tr>
		<td>{$VARS.USER_NAME}:</td>
		<td><input name="user_name" type="text" size="20" maxlength="40" value="{$smarty.post.user_name|default:$VARS.USER_DETAIL_ARRAY.name}" /></td>
	</tr>
	<tr>
		<td>{$VARS.USER_SURNAME}:</td>
		<td><input name="user_surname" type="text" size="20" maxlength="40" value="{$smarty.post.user_surname|default:$VARS.USER_DETAIL_ARRAY.surname}" /></td>
	</tr>
	<tr>
		<td>{$VARS.USER_USERNAME}:*</td>
		<td><input name="user_username" type="text" size="20" maxlength="40" value="{$smarty.post.user_username|default:$VARS.USER_DETAIL_ARRAY.username}" /></td>
	</tr>
	<tr>
		<td>{$VARS.USER_PASSWORD}:*</td>
		<td><input name="user_password" type="password" size="20" maxlength="40" value="" /></td>
	</tr>
	<tr>
		<td>{$VARS.USER_PASSWORD2}:*</td>
		<td><input name="user_password2" type="password" size="20" maxlength="40" value="" /></td>
	</tr>
	<tr>
		<td>{$VARS.USER_GROUP}:</td>
<!--		<td><input name="parent_label" type="text" size="40" maxlength="40" value="{$VARS.USER_DETAIL_ARRAY.label}" /></td>-->
		<td>{html_options name=user_group options=$VARS.USER_GROUPS selected=$smarty.post.user_group|default:$VARS.USER_DETAIL_ARRAY.id_group}</td>
	</tr>
	<tr>
		<td>{$VARS.USER_MAIL}:</td>
		<td><input name="user_mail" type="text" size="20" maxlength="40" value="{$smarty.post.user_mail|default:$VARS.USER_DETAIL_ARRAY.mail}" /></td>
	</tr>
	<tr>
		<td colspan="2">{$VARS.USER_NOTE}:</td>
	</tr>
	<tr>
		<td colspan="2"><textarea name="user_note" rows="4" cols="30" class="textarea">{$smarty.post.user_note|default:$VARS.USER_DETAIL_ARRAY.note}</textarea></td>
	</tr>
	<tr>
		<td colspan="2" align="right">
			<input name="user_reset" type="reset" value="{$VARS.USER_RESET}" />
			<input name="user_send" type="submit" value="{$VARS.USER_SEND}" />
		</td>
	</tr>
	
</table>
</form>
<div class="button_back">
	<a href="{$VARS.BUTTON_BACK}" title="{$VARS.BUTTON_BACK_NAME}">{$VARS.BUTTON_BACK_NAME}</a>
</div>
