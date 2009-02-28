{foreach from=$VARS.REFERENCES item="REFERENCE"}
<div class="content reference">
   {if $VARS.EDITABLE}
   <div class="editbox">
      <!--<p class="upside"></p> -->
      <div class="contentForm"><form action="{$REFERENCE.edit_link}" method="post"><input type="image" name="reference_edit" value="" src="images/toolbox/edit.gif" title="{$VARS.LINK_TO_EDIT_REFERENCE_NAME}"></form><form action="{$THIS_PAGE_LINK}" method="post" onsubmit="return Confirm('{$VARS.DELETE_REFERENCE_CONFIRM_MESSAGE} - {$REFERENCE.name}')"><input type="hidden" value="{$REFERENCE.id_reference}" name="reference_id" /><input type="image" name="reference_delete" value="" src="images/toolbox/remove.gif" title="{$VARS.LINK_TO_DELETE_REFERENCE_NAME}"></form></div>
      <!-- <p class="downside"></p> -->
   </div>
   {/if}
   <a href="{$VARS.IMAGES_DIR|cat:$REFERENCE.file}" title="{$REFERENCE.file}">
      {html_image file=$VARS.IMAGES_SMALL_DIR|cat:$REFERENCE.file class='referenceImage'}
   </a>
   <h2>{$REFERENCE.name}</h2>
   {$REFERENCE.label}
   <br class="reseter" />
</div>
<div class="hr"></div>
{/foreach}
<br />
{literal}
<script type="text/javascript">
   $(function() {
      $('.reference>a').lightBox();
   });
</script>
{/literal}
