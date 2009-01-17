<div class="editbox">
  <p class="upside"></p>
<div class="contentForm"><form action="{$VARS.LINK_TO_ADD_NEWS}" method="post"><input type="image" name="news_add" value="" src="images/toolbox/add.gif" title="{$VARS.LINK_TO_ADD_NEWS_NAME}"></form><form action="{$VARS.LINK_TO_EDIT_NEWS}" method="post"><input type="image" name="news_edit" src="images/toolbox/edit.gif" title="{$VARS.LINK_TO_EDIT_NEWS_NAME}"><input type="hidden" name="news_id" value="{$VARS.NEWS_DETAIL.id_new}" /></form><form action="{$THIS_PAGE_LINK}" method="post" onsubmit="return Confirm('{$VARS.DELETE_CONFIRM_MESSAGE} - {$VARS.NEWS_DETAIL.label}')"><input type="image" name="news_delete" src="images/toolbox/remove.gif" title="{$VARS.LINK_TO_DELETE_NEWS_NAME}"><input type="hidden" name="news_id" value="{$VARS.NEWS_DETAIL.id_new}" /></form></div>
  <p class="downside"></p>
</div>
{literal}
<script type="text/javascript">
  //$(document).ready(function(){
  $("div#newsConteiner{/literal}{$TPLKEY}{literal}").hover(
  function(){$("div#newsConteiner{/literal}{$TPLKEY}{literal} div.editbox").fadeIn(100);},
  function(){$("div#newsConteiner{/literal}{$TPLKEY}{literal} div.editbox").fadeOut(300);}
);
  //});
</script>
{/literal}