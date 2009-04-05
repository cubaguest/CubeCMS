<!--<p><a href="{$VARS.LINK_TO_EDIT_TEXT}" title="{$VARS.LINK_TO_EDIT_TEXT_NAME}">{$VARS.LINK_TO_EDIT_TEXT_NAME}</a></p>-->
<div class="editbox">
  <p class="upside"></p>
  <div class="contentForm"><form action="{$VARS.link_add_galery}" method="post"><input type="image" name="galery_add" value="" src="images/toolbox/add.gif" title="{$VARS.LINK_TO_ADD_GALERY_NAME}"></form></div>
  <p class="downside"></p>
</div>
{literal}
<script type="text/javascript">
  //$(document).ready(function(){
  $("#photogaleryConteiner{/literal}{$TPLKEY}{literal}").hover(
  function(){$("div#photogaleryConteiner{/literal}{$TPLKEY}{literal} div.editbox").fadeIn(100);},
  function(){$("div#photogaleryConteiner{/literal}{$TPLKEY}{literal} div.editbox").fadeOut(300);}
);
  //});
</script>
{/literal}
{* debug *}