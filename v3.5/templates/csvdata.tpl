<div>
	<br />
	<h5>{$VARS.CSV_SAVE_DATA_LABEL}:</h5>
	<form action="{$THIS_PAGE_LINK}" method="post">
		<label>{$VARS.CSV_DATA_SEPARATOR}:</label>
		{html_options name=csv_separator options=$VARS.SEPARATORS}
		<input type="submit" name="csv_save" value="{$VARS.BUTTON_CSV_SAVE}"/>
	</form>
</div>