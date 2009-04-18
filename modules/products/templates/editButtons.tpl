<div class="editbox" id="products{$TPLKEY}">
  <p class="upside"></p>
<div class="contentForm">
{if $VARS.WRITABLE}
<form action="{$VARS.ADD_LINK}" method="post"><input type="image" name="product_add" value="" src="images/toolbox/add.gif" title="{$VARS.LINK_TO_ADD_PRODUCT_NAME}"></form>{/if}{if $VARS.EDITABLE}
<form action="{$VARS.EDIT_LINK}" method="post"><input type="image" name="product_edit" src="images/toolbox/edit.gif" title="{$VARS.LINK_TO_EDIT_PRODUCT_NAME}"><input type="hidden" name="product_id" value="{$VARS.PRODUCT.id_product}" /></form>{**}
<form action="{$THIS_PAGE_LINK}" method="post" onsubmit="return Confirm('{$VARS.DELETE_CONFIRM_MESSAGE} - {$VARS.PRODUCT.label}')"><input type="image" name="product_delete" src="images/toolbox/remove.gif" title="{$VARS.LINK_TO_DELETE_PRODUCT_NAME}"><input type="hidden" name="product_id" value="{$VARS.PRODUCT.id_product}" /></form>
{/if}
</div>
  <p class="downside"></p>
</div>
{literal}
<script type="text/javascript">
  //$(document).ready(function(){
  $("div#productsConteiner{/literal}{$TPLKEY}{literal}").hover(
  function(){$("div#productsConteiner{/literal}{$TPLKEY}{literal} div#products{/literal}{$TPLKEY}{literal}").fadeIn(100);},
  function(){$("div#productsConteiner{/literal}{$TPLKEY}{literal} div#products{/literal}{$TPLKEY}{literal}").fadeOut(300);}
);
  //});
</script>
{/literal}