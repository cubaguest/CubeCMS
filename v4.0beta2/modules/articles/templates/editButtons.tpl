<div class="editbox" id="articles{$TPLKEY}">
  <p class="upside"></p>
<div class="contentForm">
{if $VARS.WRITABLE}
<form action="{$VARS.ADD_LINK}" method="post"><input type="image" name="article_add" value="" src="images/toolbox/add.gif" title="{$VARS.LINK_TO_ADD_ARTICLE_NAME}"></form>{/if}{if $VARS.EDITABLE}
<form action="{$VARS.EDIT_LINK}" method="post"><input type="image" name="article_edit" src="images/toolbox/edit.gif" title="{$VARS.LINK_TO_EDIT_ARTICLE_NAME}"><input type="hidden" name="news_id" value="{$VARS.NEWS_DETAIL.id_new}" /></form>{**}
<form action="{$THIS_PAGE_LINK}" method="post" onsubmit="return Confirm('{$VARS.DELETE_CONFIRM_MESSAGE} - {$VARS.ARTICLE.label}')"><input type="image" name="article_delete" src="images/toolbox/remove.gif" title="{$VARS.LINK_TO_DELETE_ARTICLE_NAME}"><input type="hidden" name="article_id" value="{$VARS.ARTICLE.id_article}" /></form>
{/if}
</div>
  <p class="downside"></p>
</div>
{literal}
<script type="text/javascript">
  //$(document).ready(function(){
  $("div#articlesConteiner{/literal}{$TPLKEY}{literal}").hover(
  function(){$("div#articlesConteiner{/literal}{$TPLKEY}{literal} div#articles{/literal}{$TPLKEY}{literal}").fadeIn(100);},
  function(){$("div#articlesConteiner{/literal}{$TPLKEY}{literal} div#articles{/literal}{$TPLKEY}{literal}").fadeOut(300);}
);
  //});
</script>
{/literal}