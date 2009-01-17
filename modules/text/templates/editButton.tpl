<!--<p><a href="{$VARS.LINK_TO_EDIT_TEXT}" title="{$VARS.LINK_TO_EDIT_TEXT_NAME}">{$VARS.LINK_TO_EDIT_TEXT_NAME}</a></p>-->
<div class="editbox">
  <p class="upside"></p>
  <div class="contentForm"><form action="{$VARS.LINK_TO_EDIT_TEXT}" method="post"><input type="image" name="text_edit" value="" src="images/toolbox/edit.gif" title="{$VARS.LINK_TO_EDIT_TEXT_NAME}"></form></div>
  <p class="downside"></p>
</div>
{literal}
<script type="text/javascript">
  //$(document).ready(function(){
  $("#textConteiner{/literal}{$TPLKEY}{literal}").hover(
  function(){$("div#textConteiner{/literal}{$TPLKEY}{literal} div.editbox").fadeIn(100);},
  function(){$("div#textConteiner{/literal}{$TPLKEY}{literal} div.editbox").fadeOut(300);}
);
  //});
</script>
{/literal}
{* debug *}