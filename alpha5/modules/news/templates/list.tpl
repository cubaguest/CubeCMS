{foreach from=$VARS.NEWS_LIST_ARRAY item="new"}
<div>
{$VARS.NEWS_TEXT_LANGUAGE}: {html_image file=$MAIN_LANG_IMAGES_PATH|cat:$new.lang|cat:'.png'}<br />
{$VARS.NEWS_TEXT_AUTHOR}: {$new.username}<br />
{$VARS.NEWS_TEXT_LABEL}: {$new.label}<br />
{$VARS.NEWS_TEXT_NAME}: <br />
{$new.text}
{if $new.editable eq true}
<div class="form_buttons form_buttons_inline">
	<form action="{$new.editlink}" method="post">
		<input type="hidden" name="news_id" value="{$new.id_new}" />
		<input type="submit" name="news_edit" value="{$VARS.LINK_TO_EDIT_NEWS_NAME}"/>
	</form>
	<form class="delete_form" action="{$THIS_PAGE_LINK}" method="post" onsubmit="return Confirm('{$VARS.DELETE_CONFIRM_MESSAGE} - {$new.label}')">
		<input type="hidden" name="news_id" value="{$new.id_new}" />
		<input type="submit" name="news_delete" value="{$VARS.LINK_TO_DELETE_NEWS_NAME}"/>
	</form>
</div>
{/if}
<div>

</div>

</div>
{/foreach}
