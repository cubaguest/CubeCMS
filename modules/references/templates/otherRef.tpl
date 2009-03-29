<div class="otherReference">
   {if $VARS.EDITABLE}
   <div class="editbox editOtherBox">
      <p class="upside"></p>
      <div class="contentForm"><form action="{$VARS.LINK_TO_EDIT_OTHER_REFERENCE}" method="post"><input type="image" name="reference_edit" value="" src="images/toolbox/edit.gif" title="{$VARS.LINK_TO_EDIT_OTHER_REFERENCE_NAME}"></form></div>
      <p class="downside"></p>
   </div>
   {/if}
   <a title="{$VARS.OTHER_REFERENCES_NAME}" id="showOtherReferences">{$VARS.OTHER_REFERENCES_NAME}</a>
   <div id="otherReferencesBox">
   {$VARS.OTHER_REFERENCES}
   </div>
   <br />
      {literal}
   <script type="text/javascript">
      //$(document).ready(function(){
   $("div#referencesConteiner{/literal}{$TPLKEY}{literal} div.otherReference").hover(
      function(){$("div#referencesConteiner{/literal}{$TPLKEY}{literal} div.editOtherBox").fadeIn(100);},
      function(){$("div#referencesConteiner{/literal}{$TPLKEY}{literal} div.editOtherBox").fadeOut(300);}
   );
      //});
   $("a#showOtherReferences").click(function () {
      $("div#otherReferencesBox").toggle("slow");
   });
</script>
{/literal}
<br />
</div>