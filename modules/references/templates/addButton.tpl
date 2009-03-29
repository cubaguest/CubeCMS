<div class="editbox addButton editboxrmargin">
  <p class="upside"></p>
  <div class="contentForm"><form action="{$VARS.LINK_TO_ADD_REFERENCE}" method="post"><input type="image" name="add" value="" src="images/toolbox/add.gif" title="{$VARS.LINK_TO_ADD_REFERENCE_NAME}"></form></div>
  <p class="downside"></p>
</div>
{literal}
<script type="text/javascript">
  //$(document).ready(function(){
  $("div#referencesConteiner{/literal}{$TPLKEY}{literal}").hover(
  function(){$("div#referencesConteiner{/literal}{$TPLKEY}{literal} div.addButton").fadeIn(100);},
  function(){$("div#referencesConteiner{/literal}{$TPLKEY}{literal} div.addButton").fadeOut(300);}
);
  //});
</script>
{/literal}

