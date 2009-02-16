<div>
<h2>{$VARS.NEWS_DETAIL.label}<span class="smallFont"></span></h2>
{$VARS.NEWS_DETAIL.time|date_format:"%x %X"}<br />
{$VARS.NEWS_DETAIL.username}<br />
{$VARS.NEWS_DETAIL.text}
{if $VARS.NEWS_EDIT eq true}
<!--<div class="form_buttons form_buttons_inline">
	<form action="{$VARS.LINK_TO_EDIT_NEWS}" method="post">
		<input type="hidden" name="news_id" value="{$VARS.NEWS_DETAIL.id_new}" />
		<input type="submit" name="news_edit" value="{$VARS.LINK_TO_EDIT_NEWS_NAME}"/>
	</form>
	<form class="delete_form" action="{$THIS_PAGE_LINK}" method="post" onsubmit="return Confirm('{$VARS.DELETE_CONFIRM_MESSAGE} - {$VARS.NEWS_DETAIL.label}')">
		<input type="hidden" name="news_id" value="{$VARS.NEWS_DETAIL.id_new}" />
		<input type="submit" name="news_delete" value="{$VARS.LINK_TO_DELETE_NEWS_NAME}"/>
	</form>
</div>-->
{/if}
{include file='engine:buttonback.tpl'}
</div>