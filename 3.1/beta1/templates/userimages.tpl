{if $IMAGES_ID eq null}
{assign var='IMAGES_ID' value=$VARS.USERFILES_ID}
{/if}
<div class="userImages">
<h5>{$VARS.USERMIAGES_LABEL_NAME} ({$VARS.USERIMAGES_NUM_ROWS[$IMAGES_ID]})</h5>
<form method="post" action="{$THIS_PAGE_LINK}" enctype="multipart/form-data">
	<input type="file" name="userimages_new_file" value="" />
	<input type="submit" name="userimages_send_file" value="{$VARS.BUTTON_USERIMAGE_SEND}"/>
</form>


{foreach from=$VARS.USERIMAGES_ARRAY[$IMAGES_ID] item="IMAGE"}
<table cellpadding="2" cellspacing="2">
  <tbody>
    <tr>
      <td width="300">{$VARS.IMAGE_NAME}: {$IMAGE.file}</td>
      <td>{$VARS.IMAGE_SIZE_NAME}: {math equation="x/1024" x=$IMAGE.size format="%.2f"}KB</td>
      <td align="right">
      <form action="{$THIS_PAGE_LINK}" method="post" onsubmit="return Confirm('{$VARS.CONFIRM_MESAGE_DELETE} - {$IMAGE.file}')">
      	<input name="userimages_id" value="{$IMAGE.id_file}" type="hidden" />
        <input name="userimages_delete" value="{$VARS.BUTTON_USERIMAGE_DELETE}" type="submit" />
      </form>
      </td>
    </tr>
    <tr>
      <td>
      	<a href="{$IMAGE.link_show}" rel="lightbox" title="{$IMAGE.file}">{html_image file=$IMAGE.link_small}</a>
      </td>
      <td>{$VARS.IMAGE_DIMENSIONS}:<br />
      	{$VARS.IMAGE_DIMENSIONS_WIDTH}: {$IMAGE.width}px<br />
		{$VARS.IMAGE_DIMENSIONS_HEIGHT}: {$IMAGE.height}px</td>
      <td></td>
    </tr>
    <tr>
      <td colspan="3" rowspan="1">{$VARS.FILE_LINK_TO_SHOW_NAME}:<br />
      <a href="{$IMAGE.link_show}" title="{$VARS.FILE_LINK_TO_SHOW_NAME} - {$IMAGE.file}" target="_blank">{$IMAGE.link_show}</a></td>
    </tr>
<!-- <tr><td colspan="3"></td>-->
<!-- </tr>-->
  </tbody>
</table>

<br />
{/foreach}
</div>