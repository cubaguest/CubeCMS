<div class="productsList">
   {foreach from=$VARS.PRODUCT_LIST_ARRAY item="product" name=product}
   <div class="productBox" style="background-image:url('{$VARS.IMAGES_PATH}{$product.main_image}')">
      <div>
         <a href="{$product.showlink}" title="{$product.label}">
            <h2>{$product.label}</h2>
            <!--<p class="productImage">
               <img src="{$VARS.IMAGES_PATH}{$product.main_image}" alt="product title image" width="250"/>
            </p>-->
            <p style="text-align:right;"><a href="{$product.showlink}" title="{$product.label}">{$VARS.PRODUCTS_MORE_NAME}</a></p>
         </a>
      </div>
   </div>
   {/foreach}
   <hr class="reseter" />
   <br />
</div>