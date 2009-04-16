<div class="editbox">
  <p class="upside"></p>
  <div class="contentForm">
     <form action="{$VARS.LINK_ADD_PRODUCT}" method="post">{**}
        <input type="image" name="add" value="" src="images/toolbox/add.gif" title="{$VARS.LINK_TO_ADD_PRODUCT_NAME}">{**}
     </form>{**}
  </div>
  <p class="downside"></p>
</div>
{literal}
<script type="text/javascript">
  //$(document).ready(function(){
  $("div#productsConteiner{/literal}{$TPLKEY}{literal}").hover(
  function(){$("div#productsConteiner{/literal}{$TPLKEY}{literal} div.editbox").fadeIn(100);},
  function(){$("div#productsConteiner{/literal}{$TPLKEY}{literal} div.editbox").fadeOut(300);}
);
  //});
</script>
{/literal}