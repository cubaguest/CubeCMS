{if $FILES_ID eq null}
{assign var='FILES_ID' value=$VARS.USERFILES_ID}
{/if}
<div class="userFiles">
<h5>{$VARS.USERFILES_LABEL_NAME} ({$VARS.USERFILES_NUM_ROWS[$FILES_ID]})</h5>
<form method="post" action="{$THIS_PAGE_LINK}" enctype="multipart/form-data">
	<input type="file" name="userfiles_new_file" value="" />
	<input type="submit" name="userfiles_send_file" value="{$VARS.BUTTON_USERFILE_SEND}"/>
</form>


{foreach from=$VARS.USERFILES_ARRAY[$FILES_ID] item="FILE"}
<table border="0" cellpadding="2" cellspacing="2">
  <tbody>
    <tr>
      <td width="300">{$VARS.FILE_NAME}: {$FILE.file}</td>
      <td>{$VARS.FILE_SIZE_NAME}: {math equation="x / 1024" x=$FILE.size format="%.2f"}KB</td>
      <td align="right">
      	<form action="{$THIS_PAGE_LINK}" method="post" onsubmit="return Confirm('{$VARS.CONFIRM_MESAGE_DELETE} - {$FILE.file}')">
      		<input type="hidden" name="userfiles_id" value="{$FILE.id_file}" />
      		<input type="submit" name="userfiles_delete" value="{$VARS.BUTTON_USERFILE_DELETE}" />
      	</form>
      </td>
    </tr>
    <tr>
      <td colspan="3" rowspan="1">{$VARS.FILE_LINK_TO_SHOW_NAME}:<br>
		<a href="{$FILE.link_show}" title="{$VARS.FILE_LINK_TO_SHOW_NAME} - {$FILE.file}" target="_blank">{$FILE.link_show}</a></td>
    </tr>
    <tr>
      <td colspan="3" rowspan="1">{$VARS.FILE_LINK_TO_DOWNLOAD_NAME}:<br>
		<a href="{$FILE.link_download}" title="{$VARS.FILE_LINK_TO_DOWNLOAD_NAME} - {$FILE.file}">{$FILE.link_download}</a></td>
    </tr>
<!--    <tr><td colspan="3"></td>-->
<!--    </tr>-->
  </tbody>
</table>
<br />
{/foreach}
</div>