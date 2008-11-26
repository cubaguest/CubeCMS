<!--<div class="text_obsah rezervation_form">-->
<form method="post" action="{$THIS_PAGE_LINK}">
{html_options name=foo options=$VARS.COURSES_ARRAY}
<div class="text rezervation_form {if $WRITABLE neq true}big_top_padding{/if}">
<div class="text_obsah">
	<table>
		<tr>
			<td colspan="2"><h4><span class="menu{$SEL_SECTION_COUNT} menuitem_sel">&nbsp;</span>&nbsp;&nbsp;{$FORM_CONTACT_DATA_LABEL|mb_upper}</h4></td>
		</tr>
		<tr>
			<td width="160px" class="rezervation_form_odsad">{$FORM_CONTACT_DATA_NAME}:*</td>
			<td align="right"><input name="contact_name" type="text" size="30" maxlength="50" value="{$smarty.post.contact_name}" /></td>
		</tr>
		<tr>
			<td class="rezervation_form_odsad">{$FORM_CONTACT_DATA_ADDRESS}:*</td>
			<td align="right"><input name="contact_address" type="text" size="30" maxlength="50" value="{$smarty.post.contact_address}" /></td>
		</tr>
		<tr>
			<td class="rezervation_form_odsad">{$FORM_CONTACT_DATA_PHONE}:</td>
			<td align="right"><input name="contact_phone" type="text" size="30" maxlength="13" value="{$smarty.post.contact_phone}" /></td>
		</tr>
		<tr>
			<td class="rezervation_form_odsad">{$FORM_CONTACT_DATA_MAIL}:</td>
			<td align="right"><input name="contact_mail" type="text" size="30" maxlength="50" value="{$smarty.post.contact_mail|default:'@'}" /></td>
		</tr>
	</table>
</div>
</div>
<div class="text rezervation_form">
<div class="text_obsah">
	<table>
		<tr>
			<td colspan="3"><h4><span class="menu{$SEL_SECTION_COUNT} menuitem_sel">&nbsp;</span>&nbsp;&nbsp;{$FORM_ROOMS_LABEL|mb_upper}</h4></td>
		</tr>
		<tr>
			<td width="240px" class="rezervation_form_odsad">{$FORM_ROOM} {$ROOMS_VARIANT.1}</td>
			<td width="30px">{$FORM_COUNT_ROOMS}:</td>
			<td align="right">{html_options name='type_rooms_count_1' options=$ROOMS_COUNT selected=$smarty.post.type_rooms_count_1|default:1}</td>
		</tr>
		<tr">
			<td class="rezervation_form_odsad">{$FORM_ROOM} {$ROOMS_VARIANT.2}</td>
			<td>{$FORM_COUNT_ROOMS}:</td>
			<td align="right">{html_options name='type_rooms_count_2' options=$ROOMS_COUNT selected=$smarty.post.type_rooms_count_2|default:0}</td>
		</tr>
		<tr>
			<td class="rezervation_form_odsad">{$FORM_ROOM} {$ROOMS_VARIANT.3}</td>
			<td>{$FORM_COUNT_ROOMS}:</td>
			<td align="right">{html_options name='type_rooms_count_3' options=$ROOMS_COUNT selected=$smarty.post.type_rooms_count_3|default:0}</td>
		</tr>
		<tr>
			<td class="rezervation_form_odsad">{$FORM_ROOM} {$ROOMS_VARIANT.4}</td>
			<td>{$FORM_COUNT_ROOMS}:</td>
			<td align="right">{html_options name='type_rooms_count_4' options=$ROOMS_COUNT selected=$smarty.post.type_rooms_count_4|default:0}</td>
		</tr>
	</table>
</div>
</div>
<div class="text rezervation_form">
<div class="text_obsah">
	<div class="rezervation_form_div">
	<h4><span class="menu{$SEL_SECTION_COUNT} menuitem_sel">&nbsp;</span>&nbsp;&nbsp;{$FORM_FOOD_LABEL|mb_upper}</h4>
	<span class="rezervation_form_odsad">{html_radios name='type_food' options=$FORM_FOOD_ARRAY selected=$smarty.post.type_food|default:1}</span>
	</div>
</div>
</div>
<div class="text rezervation_form">
<div class="text_obsah">
	<table>
		<tr>
			<td colspan="2"><h4><span class="menu{$SEL_SECTION_COUNT} menuitem_sel">&nbsp;</span>&nbsp;&nbsp;{$FORM_SPECIAL_BATHOUSE_LABEL|mb_upper}</h4></td>
		</tr>
		<tr>
			<td class="rezervation_form_odsad" width="180px">{$FORM_PROCEDURE}:</td>
			<td align="right"><input name="bath_procedure" type="checkbox" /></td>
		</tr>
		<tr>
			<td class="rezervation_form_odsad">{$FORM_SPECIAL_STAY}:</td>
			<td align="right">{html_options name='bath_stay' options=$FORM_SPECIAL_STAY_ARRAY selected=$smarty.post.bath_stay|default:0}</td>
		</tr>
	</table>
</div>
</div>
<div class="text rezervation_form">
<div class="text_obsah">
	<table>
		<tr>
			<td colspan="2"><h4><span class="menu{$SEL_SECTION_COUNT} menuitem_sel">&nbsp;</span>&nbsp;&nbsp;{$FORM_DATE_NAME|mb_upper}</h4></td>
		</tr>
		<tr>
			<td width="120px" class="rezervation_form_odsad">{$FORM_FROM_DATE}:*</td>
			<td align="right">{html_select_date field_array='start_day' prefix='' time=$FORM_DATE_CURRENT_TIME end_year=+5 field_order=DMY}</td>
		</tr>
		<tr>
			<td class="rezervation_form_odsad">{$FORM_TO_DATE}:*</td>
			<td align="right">{html_select_date field_array='end_day' prefix='' time=$FORM_DATE_CURRENT_TIME_PLUS_WEEK end_year=+5 field_order=DMY}</td>
		</tr>
	</table>
</div>
</div>
<div class="text rezervation_form">
<div class="text_obsah">
	<div class="rezervation_form_div">
	<h4><span class="menu{$SEL_SECTION_COUNT} menuitem_sel">&nbsp;</span>&nbsp;&nbsp;{$FORM_NOTE_LABEL|mb_upper}</h4>
	<textarea rows="5" cols="60" class="textarea note_textarea" name="note">{$smarty.post.note}</textarea><br />
	<p class="note">{$FORM_NOTE_NOTE}</p><br />
	<p class="note">* - {$RESERVE_DATA}</p>
	</div>
</div>
</div>
<div class="text rezervation_form big_bottom_padding">
<div class="text_obsah">
	<div class="form_buttons">
	<input type="submit" name="send_rezervation" value="{$BUTTON_SEND}" />
	</div>
</div>
</div>
</form>
