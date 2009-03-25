<h1>{$SITEMAP_PAGE_NAME} {$MAIN_PAGE_TITLE}</h1>
<div class="moduleBox">
{foreach from=$SITEMAP_PAGES item=SECTION}
<ul>
   <li>{$SECTION.name}
      <ul>
         {foreach from=$SECTION.categories item=CATEGORY}
         <li>
            <a href="{$CATEGORY.url}" title="{$CATEGORY.name}">{$CATEGORY.name}</a>
            {if !empty($CATEGORY.results)}
            <ul>
            {foreach from=$CATEGORY.results item=RESULT}
               <li><a href="{$RESULT.loc}" title="{$RESULT.name}">{$RESULT.name}</a></li>
            {/foreach}
            </ul>
            {/if}
         </li>
         {/foreach}
      </ul>
   </li>
</ul>
{/foreach}
</div>