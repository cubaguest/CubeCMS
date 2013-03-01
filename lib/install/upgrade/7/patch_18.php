<?
// repair order in shop attributes
if(defined('VVE_SHOP') && VVE_SHOP == true){
   // aktualizace pořadí atributů a jejich skupin
   $mgrp = new Shop_Model_AttributesGroups();
   $groups = $mgrp
      ->columns(array(Shop_Model_AttributesGroups::COLUMN_ID, Shop_Model_AttributesGroups::COLUMN_ORDER))
      ->records();
   $grpOrder = 1;
   if(!empty($groups)){
      foreach($groups as $grp){
         $grp->{Shop_Model_AttributesGroups::COLUMN_ORDER} = $grpOrder;
         $grp->save();
         $grpOrder++;
         $mAttr = new Shop_Model_Attributes();
         $attributes = $mAttr
            ->where(Shop_Model_Attributes::COLUMN_ID_GROUP." = :id", array('id' => $grp->getPK()))
            ->columns(array(Shop_Model_Attributes::COLUMN_ID, Shop_Model_Attributes::COLUMN_ID_GROUP, Shop_Model_Attributes::COLUMN_ORDER))
            ->records();
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
   $m = new Shop_Model_Product();
   $m->columns(array(Shop_Model_Product::COLUMN_ID_CATEGORY, Shop_Model_Product::COLUMN_ORDER))
      ->order(array(Shop_Model_Product::COLUMN_ID_CATEGORY => Model_ORM::ORDER_ASC, Shop_Model_Product::COLUMN_ID => Model_ORM::ORDER_ASC) );

   $products = $m->records();
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