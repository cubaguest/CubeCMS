<div>
   <h2>{$VARS.PRODUCT.label}</h2>
   <img src="{$VARS.IMAGES_PATH}{$VARS.PRODUCT.main_image}" alt="{$VARS.PRODUCT.label} image" width="580"/>

   {$VARS.PRODUCT.text}
   {include file='engine:buttonback.tpl'}
   {if $VARS.LIGHTBOX}
   {literal}
   <script type="text/javascript">
      $(document).ready(function() { $('a[rel*=lightbox]').lightBox(); });
   </script>
   {/literal}
   {/if}
</div>
