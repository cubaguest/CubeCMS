<!--<p><a href="{$VARS.LINK_TO_ADD_NEWS}" title="{$VARS.LINK_TO_ADD_NEWS_NAME}">{$VARS.LINK_TO_ADD_NEWS_NAME}</a></p>-->
<div class="editbox">
  <p class="upside"></p>
  <div class="contentForm"><form action="{$VARS.LINK_TO_ADD_NEWS}" method="post"><input type="image" name="add" value="" src="images/toolbox/add.gif" title="{$VARS.LINK_TO_ADD_NEWS_NAME}"></form></div>
  <p class="downside"></p>
</div>
{literal}
<script type="text/javascript">
  //$(document).ready(function(){
  $("div.newsContainerClass").hover(
  function(){$("div.newsContainerClass div.editbox").fadeIn(100);},
  function(){$("div.newsContainerClass div.editbox").fadeOut(300);}
);
  //});
</script>
{/literal}
