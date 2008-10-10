{literal}
<script type="text/javascript">
function cleanSearch() {
	document.searchform.user_search_id.value='';
	document.searchform.user_search_name.value='';
	document.searchform.user_search_surname.value='';
	document.searchform.user_search_username.value='';
/*	document.searchform.submit();*/
}
</script>
{/literal}

<form method="post" action="{$THIS_PAGE_LINK}" name="searchform">
<table class="search_form records_table">
	<tbody>
	<tr>
		<th width="35"><input type="text" name="user_search_id" size="2" value="{$VARS.USER_SEARCH_ARRAY.id}" /></th>
		<th width="125"><input type="text" name="user_search_name" size="13" value="{$VARS.USER_SEARCH_ARRAY.name}" /></th>
		<th width="125"><input type="text" name="user_search_surname" size="13" value="{$VARS.USER_SEARCH_ARRAY.surname}" /></th>
		<th width="170"><input type="text" name="user_search_username" size="15" value="{$VARS.USER_SEARCH_ARRAY.username}" /></th>
		<th width="150" align="right">
			<input type="button" name="user_search_reset" value="{$VARS.USERS_RESET_BUTTON}" onclick="cleanSearch()" />
			<input type="submit" name="user_search_send" value="{$VARS.USERS_SEARCH_BUTTON}" />
		</th>
	</tr>
	</tbody>
</table>
</form>
<table class="records_table">
	<tbody>
	<tr>
		<th class="border_bottom" width="35">{$VARS.USERS_ID}<a href="{$VARS.USERS_ORDER_LINKS.id_desc}">{html_image file='./images/buttons/order_desc.png'}</a><a href="{$VARS.USERS_ORDER_LINKS.id_asc}">{html_image file='./images/buttons/order_asc.png'}</a></th>
		<th class="border_bottom" width="125">{$VARS.USERS_NAME}<a href="{$VARS.USERS_ORDER_LINKS.name_desc}">{html_image file='./images/buttons/order_desc.png'}</a><a href="{$VARS.USERS_ORDER_LINKS.name_asc}">{html_image file='./images/buttons/order_asc.png'}</a></th>
		<th class="border_bottom" width="125">{$VARS.USERS_SURNAME}<a href="{$VARS.USERS_ORDER_LINKS.surname_desc}">{html_image file='./images/buttons/order_desc.png'}</a><a href="{$VARS.USERS_ORDER_LINKS.surname_asc}">{html_image file='./images/buttons/order_asc.png'}</a></th>
		<th class="border_bottom" width="90">{$VARS.USERS_USERNAME}<a href="{$VARS.USERS_ORDER_LINKS.username_desc}">{html_image file='./images/buttons/order_desc.png'}</a><a href="{$VARS.USERS_ORDER_LINKS.username_asc}">{html_image file='./images/buttons/order_asc.png'}</a></th>
		<th class="border_bottom" width="90">{$VARS.USERS_GROUPNAME}<a href="{$VARS.USERS_ORDER_LINKS.groupname_desc}">{html_image file='./images/buttons/order_desc.png'}</a><a href="{$VARS.USERS_ORDER_LINKS.groupname_asc}">{html_image file='./images/buttons/order_asc.png'}</a></th>
	</tr>
	</tbody>
	<tbody>
{foreach from=$VARS.USERS_LIST_ARRAY item="USER"}
	<tr>
		<td class="border_bottom"><a href="{$USER.detail_link}" title="{$VARS.USER_SHOW_DETAIL}">{$USER.id_user}</a></td>
		<td class="border_bottom"><a href="{$USER.detail_link}" title="{$VARS.USER_SHOW_DETAIL}">{$USER.name}</a></td>
		<td class="border_bottom"><a href="{$USER.detail_link}" title="{$VARS.USER_SHOW_DETAIL}">{$USER.surname}</a></td>
		<td class="border_bottom"><a href="{$USER.detail_link}" title="{$VARS.USER_SHOW_DETAIL}">{$USER.username}</a></td>
		<td class="border_bottom"><a href="{$USER.detail_link}" title="{$VARS.USER_SHOW_DETAIL}">{$USER.group_name}</a></td>
	</tr>
{/foreach}
	</tbody>
</table>
