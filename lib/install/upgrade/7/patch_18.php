<?
// repair order in shop attributes
if(defined('VVE_SHOP') && VVE_SHOP == true){
   // aktualizace pořadí atributů a jejich skupin
   $groups = (new Shop_Model_AttributesGroups())->records();
   $grpOrder = 1;
   if(!empty($groups)){
      foreach($groups as $grp){
         $grp->{Shop_Model_AttributesGroups::COLUMN_ORDER} = $grpOrder;
         $grp->save();
         $grpOrder++;

         $attributes = (new Shop_Model_Attributes())->where(Shop_Model_Attributes::COLUMN_ID_GROUP." = :id", array('id' => $grp->getPK()))->records();
         $atrOrder = 1;
         if(!empty($attributes)){
            foreach($attributes as $atr){
               $atr->{Shop_Model_Attributes::COLUMN_ORDER} = $atrOrder;
               $atr->save();
               $atrOrder++;
            }
         }

      }
   }

   // Aktualizace pořadí produktů
   $products = (new Shop_Model_Product())
      ->order(array(Shop_Model_Product::COLUMN_ID_CATEGORY => Model_ORM::ORDER_ASC, Shop_Model_Product::COLUMN_NAME => Model_ORM::ORDER_ASC) )
      ->records();
   $order = 1;
   $idc = null;
   foreach ($products as $product) {
      if($product->{Shop_Model_Product::COLUMN_ID_CATEGORY} != $idc){
         $idc = $product->{Shop_Model_Product::COLUMN_ID_CATEGORY};
         $order = 1;
      }
      $product->{Shop_Model_Product::COLUMN_ORDER} = $order;
      $product->save();
      $order++;
   }


}