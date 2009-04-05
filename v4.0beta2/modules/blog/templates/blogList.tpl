{foreach from=$VARS.BLOGS item='BLOG'}
<h2><a href="{$BLOG.urlkey}" title="{$BLOG.label_cs}">{$BLOG.label_cs}</a></h2>
<div>
	{$BLOG.text_cs|truncate:300}
</div>
{/foreach}