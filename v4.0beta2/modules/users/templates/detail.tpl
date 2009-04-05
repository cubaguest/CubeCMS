{if $VARS.USER_DETAIL_ARRAY.surname neq null}<h3>{$VARS.USER_DETAIL_ARRAY.surname}, {$VARS.USER_DETAIL_ARRAY.name}</h3>{/if}
<table class="table_detail" width="450">
<tbody>
	<tr>
		<td width="180" class="border_bottom"><b>{$VARS.USER_ID}:</b></td>
		<td class="border_bottom">{$VARS.USER_DETAIL_ARRAY.id_user}</td>
	</tr>
	<tr>
		<td class="border_bottom"><b>{$VARS.USER_NAME}:</b></td>
		<td class="border_bottom">{$VARS.USER_DETAIL_ARRAY.name}</td>
	</tr>
	<tr>
		<td class="border_bottom"><b>{$VARS.USER_SURNAME}:</b></td>
		<td class="border_bottom">{$VARS.USER_DETAIL_ARRAY.surname}</td>
	</tr>
	<tr>
		<td class="border_bottom"><b>{$VARS.USER_LABEL}:</b></td>
		<td class="border_bottom">{$VARS.USER_DETAIL_ARRAY.username}</td>
	</tr>
	<tr>
		<td class="border_bottom"><b>{$VARS.USER_GROUP}:</b></td>
		<td class="border_bottom">{$VARS.USER_DETAIL_ARRAY.group_name}</td>
	</tr>
	<tr>
		<td class="border_bottom"><b>{$VARS.USER_MAIL}:</b></td>
		<td class="border_bottom">
		{if $VARS.USER_DETAIL_ARRAY.mail neq null}
		<a href="mailto:{$VARS.USER_DETAIL_ARRAY.mail}" title="{$VARS.USER_MAIL}: {$VARS.USER_DETAIL_ARRAY.mail}">{$VARS.USER_DETAIL_ARRAY.mail}</a>
		{else}
		{$VARS.USER_NONE_RECORD}
		{/if}
		</td>
	</tr>
	<tr>
		<td colspan="2"><b>{$VARS.USER_NOTE}:</b></td>
	</tr>
	<tr>
		<td>{$VARS.USER_DETAIL_ARRAY.note|default:$VARS.USER_NONE_RECORD}</td>
	</tr>
	{if $VARS.USER_CONTROL eq true}
	<tr>
		<td colspan="2" align="right" class="border_bottom">
				<div class="edit_buttons">
		<form class="delete_button" action="{$THIS_PAGE_LINK}" method="post" onsubmit="return Confirm('{$VARS.BUTTON_DELETE_USER_MESSAGE} {$VARS.USER_DETAIL_ARRAY.username} {if $VARS.USER_DETAIL_ARRAY.surname neq null}- {$VARS.USER_DETAIL_ARRAY.name}, {$VARS.USER_DETAIL_ARRAY.surname}{/if}')">
			<input type="hidden" name="user_id" value="{$VARS.USER_DETAIL_ARRAY.id_user}"/>
			<input type="submit" value="{$VARS.USER_DELETE}" name="user_delete" />
		</form>
		<form action="{$VARS.USER_EDIT_LINK}" method="post">
			<input type="submit" value="{$VARS.USER_EDIT}" name="user_edit" />
		</form>
		</div>
		

		</td>
	</tr>
	{/if}
</tbody>
</table>

<div class="button_back">
	<a href="{$VARS.BUTTON_BACK}" title="{$VARS.BUTTON_BACK_NAME}">{$VARS.BUTTON_BACK_NAME}</a>
</div>
