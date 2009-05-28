<div class="editbox">
  <p class="upside"></p>
  <div class="contentForm"><form action="{$VARS.LINK_TO_ADD_ACTION}" method="post"><input type="image" name="add" value="" src="images/toolbox/add.gif" title="{$VARS.LINK_TO_ADD_ACTION_NAME}"></form></div>
  <p class="downside"></p>
</div>
{literal}
<script type="text/javascript">
  //$(document).ready(function(){
  $("div#actionsConteiner{/literal}{$TPLKEY}{literal}").hover(
  function(){$("div#actionsConteiner{/literal}{$TPLKEY}{literal} div.editbox").fadeIn(100);},
  function(){$("div#actionsConteiner{/literal}{$TPLKEY}{literal} div.editbox").fadeOut(300);}
);
  //});
</script>
{/literal}