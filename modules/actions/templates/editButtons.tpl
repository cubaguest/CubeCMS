<div class="editbox">
   <p class="upside"></p>
   <div class="contentForm">
<form action="{$VARS.ADD_ACTION}" method="post">{**}
<input type="image" name="action_add" value="" src="images/toolbox/add.gif" title="{$VARS.LINK_TO_ADD_ACTION_NAME}" />{**}
</form>{**}
<form action="{$VARS.EDIT_LINK}" method="post">{**}
<input type="image" name="action_edit" src="images/toolbox/edit.gif" title="{$VARS.LINK_TO_EDIT_ACTION_NAME}" />{**}
<input type="hidden" name="action_id" value="{$VARS.ACTION.id_action}" />{**}
</form>{**}
<form action="{$THIS_PAGE_LINK}" method="post" onsubmit="return Confirm('{$VARS.DELETE_CONFIRM_MESSAGE} - {$VARS.ACTION.label}')">{**}
<input type="image" name="action_delete" src="images/toolbox/remove.gif" title="{$VARS.LINK_TO_DELETE_ACTION_NAME}" />{**}
<input type="hidden" name="action_id" value="{$VARS.ACTION.id_action}" />{**}
</form>{**}
   </div>
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