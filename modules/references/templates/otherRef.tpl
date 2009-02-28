<div class="otherReference">
   {if $VARS.EDITABLE}
   <div class="editbox">
      <!--<p class="upside"></p> -->
      <div class="contentForm"><form action="{$VARS.LINK_TO_EDIT_OTHER_REFERENCE}" method="post"><input type="image" name="reference_edit" value="" src="images/toolbox/edit.gif" title="{$VARS.LINK_TO_EDIT_OTHER_REFERENCE_NAME}"></form></div>
      <!-- <p class="downside"></p> -->
   </div>
   {/if}
   <div class="content">
   <a title="{$VARS.OTHER_REFERENCES_NAME}" id="showOtherReferences">{$VARS.OTHER_REFERENCES_NAME}</a>
   <div id="otherReferencesBox">
   {$VARS.OTHER_REFERENCES}
   </div>
   {literal}
<script type="text/javascript">
   $("a#showOtherReferences").click(function () {
      $("div#otherReferencesBox").toggle("slow");
   });
</script>
{/literal}
   <br />
   </div>
</div>