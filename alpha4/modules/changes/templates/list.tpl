{literal}
<script type="text/javascript">
function cleanSearch() {
	document.searchform.changes_search_name.value='';
	document.searchform.changes_search_username.value='';
	document.searchform.changes_search_label.value='';
/*	document.searchform.submit();*/
}
</script>
{/literal}


<h2>{$VARS.CHANGES_NAME_OF_LIST}</h2>
<form method="post" action="{$THIS_PAGE_LINK}" name="searchform">
<table class="search_form records_table">
	<tbody>
	<tr>
		<th width="100"></th>
		<th width="140"><input type="text" name="changes_search_name" size="15" value="{$VARS.CHANGE_SEARCH_ARRAY.name}" /></th>
		<th width="100"><input type="text" name="changes_search_username" size="10" value="{$VARS.CHANGE_SEARCH_ARRAY.username}" /></th>
		<th><input type="text" name="changes_search_label" size="15" value="{$VARS.CHANGE_SEARCH_ARRAY.label}" /></th>
		<th width="150" align="right">
			<input type="button" name="changes_search_reset" value="{$VARS.CHANGES_RESET_BUTTON}" onclick="cleanSearch()" />
			<input type="submit" name="changes_search_send" value="{$VARS.CHANGES_SEARCH_BUTTON}" />
		</th>
	</tr>
	</tbody>
</table>
</form>
<table class="records_table">
	<tbody>
	<tr>
		<th class="border_bottom" width="100">{$VARS.CHANGES_TIME}<a href="{$VARS.CHANGES_ORDER_LINKS.time_desc}">{html_image file='./images/buttons/order_desc.png'}</a><a href="{$VARS.CHANGES_ORDER_LINKS.time_asc}">{html_image file='./images/buttons/order_asc.png'}</a></th>
		<th class="border_bottom" width="140">{$VARS.CHANGES_SURNAME}, {$VARS.CHANGES_NAME}<a href="{$VARS.CHANGES_ORDER_LINKS.name_desc}">{html_image file='./images/buttons/order_desc.png'}</a><a href="{$VARS.CHANGES_ORDER_LINKS.name_asc}">{html_image file='./images/buttons/order_asc.png'}</a></th>
		<th class="border_bottom" width="100">{$VARS.CHANGES_USERNAME}<a href="{$VARS.CHANGES_ORDER_LINKS.username_desc}">{html_image file='./images/buttons/order_desc.png'}</a><a href="{$VARS.CHANGES_ORDER_LINKS.username_asc}">{html_image file='./images/buttons/order_asc.png'}</a></th>
		<th class="border_bottom">{$VARS.CHANGES_LABEL}<a href="{$VARS.CHANGES_ORDER_LINKS.label_desc}">{html_image file='./images/buttons/order_desc.png'}</a><a href="{$VARS.CHANGES_ORDER_LINKS.label_asc}">{html_image file='./images/buttons/order_asc.png'}</a></th>
	</tr>
	</tbody>
	<tbody>
{foreach from=$VARS.CHANGES_LIST_ARRAY item="CHANGE"}
	<tr>
		<td class="border_bottom">{$CHANGE.time|date_format:"%x %R"}</td>
		<td class="border_bottom">{$CHANGE.surname}, {$CHANGE.name}</td>
		<td class="border_bottom">{$CHANGE.username}</td>
		<td class="border_bottom">{$CHANGE.label}</td>
	</tr>
{/foreach}
	</tbody>
</table>
