<div class="editbox">
  <p class="upside"></p>
  <div class="contentForm"><form action="{$VARS.LINK_TO_ADD_FILE}" method="post"><input type="image" name="file_add" value="" src="images/toolbox/upload.gif" title="{$VARS.LINK_TO_ADD_FILE_NAME}"></form></div>
  <p class="downside"></p>
</div>
{literal}
<script type="text/javascript">
  //$(document).ready(function(){
  $("#dwfilesConteiner{/literal}{$TPLKEY}{literal}").hover(
  function(){$("div#dwfilesConteiner{/literal}{$TPLKEY}{literal} div.editbox").fadeIn(100);},
  function(){$("div#dwfilesConteiner{/literal}{$TPLKEY}{literal} div.editbox").fadeOut(300);}
);
  //});
</script>
{/literal}
{* debug *}