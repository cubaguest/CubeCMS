<div class="editbox addButton">
  <p class="upside"></p>
  <div class="contentForm">
     <form action="{$VARS.LINK_TO_ADD_CONTACT}" method="post">
        <input type="image" name="add" value="" src="images/toolbox/add.gif" title="{$VARS.LINK_TO_ADD_CONTACT_NAME}">
     </form>
 </div>
  <p class="downside"></p>
</div>
{literal}
<script type="text/javascript">
  //$(document).ready(function(){
  $("div#contactsConteiner{/literal}{$TPLKEY}{literal}").hover(
  function(){$("div#contactsConteiner{/literal}{$TPLKEY}{literal} div.addButton").fadeIn(100);},
  function(){$("div#contactsConteiner{/literal}{$TPLKEY}{literal} div.addButton").fadeOut(300);}
);
  //});
</script>
{/literal}

