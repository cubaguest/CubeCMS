<?php
/*
 * Třída modelu detailem článku
 */
class Shop_Model_ProductCombinations extends Model_ORM {
   const DB_TABLE = 'shop_products_combinations';

   const COLUMN_ID = 'id_product_combination';
   const COLUMN_ID_PRODUCT = 'id_product';
   const COLUMN_QTY = 'product_combination_quantity';
   const COLUMN_PRICE = 'product_combination_price_add';
   const COLUMN_IS_DEFAULT = 'product_combination_is_default';

   protected function _initTable()
   {
      $this->setTableName(self::DB_TABLE, 't_product_comb');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_PRODUCT, array('datatype' => 'smallint', 'nn' => true, 'index' => self::COLUMN_ID_PRODUCT ));
      
      $this->addColumn(self::COLUMN_QTY, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_PRICE, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_IS_DEFAULT, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));

      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_PRODUCT, 'Shop_Model_Product', Shop_Model_Product::COLUMN_ID);

      $this->addRelatioOneToMany(self::COLUMN_ID, 'Shop_Model_ProductCombinationHasVariant', Shop_Model_ProductCombinationHasVariant::COLUMN_ID_COMBINATION);
   }

   public static function updateProductQty($idProduct)
   {
      // get num from combinations
      $model = new self();

      /*
       * SELECT SUM(product_combination_quantity) AS product_qty FROM cube_cms_shop_products_combinations
         WHERE id_product = 9
         GROUP BY id_product
       */
      $model = new self();
      $pQty = $model
         ->columns(array( 'product_qty' => 'SUM('.self::COLUMN_QTY.')' ))
         ->where(self::COLUMN_ID_PRODUCT." = :idp", array('idp' => $idProduct))
         ->groupBy(self::COLUMN_ID_PRODUCT)->record(null, null, PDO::FETCH_OBJ);

      $modelProduct = new Shop_Model_Product();
      $modelProduct
         ->where(Shop_Model_Product::COLUMN_ID." = :idp", array('idp' => $idProduct))
         ->update(array(Shop_Model_Product::COLUMN_QUANTITY => $pQty->product_qty < 0 ? -1 : $pQty->product_qty));

   }

   public static function productHasCombination($idProduct)
   {
      // get num from combinations
      $model = new self();
      return (bool)$model->where(self::COLUMN_ID_PRODUCT." = :idp", array('idp' => $idProduct))->count();
   }

   public static function generateDefaultCombination($idProduct)
   {
      $modelVariants = new Shop_Model_ProductVariants();
      $modelComb = new self();

      $defaultVariants = $modelVariants->where(
         Shop_Model_ProductVariants::COLUMN_IS_DEFAULT." = 1 AND ".Shop_Model_ProductVariants::COLUMN_ID_PRODUCT." = :idp",
         array('idp' => $idProduct))->records(PDO::FETCH_OBJ);

      $default = array();
      foreach ($defaultVariants as $variant) {
         $default[] = $variant->{Shop_Model_ProductVariants::COLUMN_ID};
      }

//      $storedVariants = $modelComb
//         ->join(Shop_Model_ProductCombinations::COLUMN_ID, "Shop_Model_ProductCombinationHasVariant",
//         Shop_Model_ProductCombinationHasVariant::COLUMN_ID_COMBINATION)
//         ->where(Shop_Model_ProductCombinations::COLUMN_ID_PRODUCT." = :idp",
//         array('idp' => $idProduct))
//         ->records(PDO::FETCH_OBJ);
//
//      $varCounter = array();
//      $defaultCount = count($default);
//      $idCombination = 0;
//      foreach ($storedVariants as $var) {
//         if(in_array($var->{Shop_Model_ProductCombinationHasVariant::COLUMN_ID_VARIANT}, $default)){
//            if(!isset( $varCounter[$var->{Shop_Model_ProductCombinations::COLUMN_ID}])){
//               $varCounter[$var->{Shop_Model_ProductCombinations::COLUMN_ID}] = 1;
//            } else {
//               $varCounter[$var->{Shop_Model_ProductCombinations::COLUMN_ID}]++;
//            }
//            if($varCounter[$var->{Shop_Model_ProductCombinations::COLUMN_ID}] == $defaultCount){
//               $idCombination = $var->{Shop_Model_ProductCombinations::COLUMN_ID};
//               break;
//            }
//         }
//      }
      // zrušit ostatní výchozí
      $modelComb
         ->where(Shop_Model_ProductCombinations::COLUMN_ID_PRODUCT." = :idp",
         array('idp' => $idProduct))
         ->update(array(Shop_Model_ProductCombinations::COLUMN_IS_DEFAULT => false));

      // nastav novou výchozí
      $combination = self::getCombinationByVariants($idProduct, $default);
      if($combination){
         // pokud je kombinace
         $comb = $modelComb->record($combination->{Shop_Model_ProductCombinations::COLUMN_ID});
         if($comb){
            $comb->{Shop_Model_ProductCombinations::COLUMN_IS_DEFAULT} = true;
            $comb->save();
         }
      } else {
         // pokud tato kombinace neexistuje, nasatv první kombinaci
         $combs = $modelComb
            ->where(Shop_Model_ProductCombinations::COLUMN_ID_PRODUCT." = :idp",
            array('idp' => $idProduct))->records();
         if(count($combs) > 0){
            $combs[0]->{Shop_Model_ProductCombinations::COLUMN_IS_DEFAULT} = true;
            $combs[0]->save();
         }
      }

      // update product qty
      self::updateProductQty($idProduct);
   }

   /**
    * Metoda vrátí kombinaci podle zadaných variant
    * @param $variants array -- pole s id variant
    */
   public static function getCombinationByVariants($idProduct, $variantsArray)
   {
      if(empty($variantsArray)){
         return false;
      }
      $model = new self();
      $varForSQL = array();
      foreach ($variantsArray as $key => $var) {
         $varForSQL[':var_'.$key] = $var;
      }

      $comb = $model
         ->columns(array(
            '*',
            'num_variants' => 'COUNT(var_table.'.Shop_Model_ProductVariants::COLUMN_ID.')'
         ))
         ->join(self::COLUMN_ID,
            array( 'comb_var' => 'Shop_Model_ProductCombinationHasVariant'),
            Shop_Model_ProductCombinationHasVariant::COLUMN_ID_COMBINATION
         )
         ->join(array('comb_var' => Shop_Model_ProductCombinationHasVariant::COLUMN_ID_VARIANT),
            array('var_table' => 'Shop_Model_ProductVariants'),
            Shop_Model_ProductVariants::COLUMN_ID
         )
         ->where(self::COLUMN_ID_PRODUCT." = :idp AND comb_var.".Shop_Model_ProductVariants::COLUMN_ID." IN (".implode(',', array_keys($varForSQL)).")",
            array_merge(array('idp' => $idProduct), $varForSQL))
         ->groupBy(array(self::COLUMN_ID))
         ->having('num_variants = :countVar', array('countVar' => count($variantsArray)))
         ->record(null, null, PDO::FETCH_OBJ);

      return $comb;
   }

   public static function getDefaultCombination($idProduct)
   {
      $model = new self();
      return $model->columns(array(
         '*',
         'variant_ids' => 'GROUP_CONCAT(comb_var.'.Shop_Model_ProductVariants::COLUMN_ID.' SEPARATOR ";")',
         'price_add' => 'SUM(var_t.'.Shop_Model_ProductVariants::COLUMN_PRICE_ADD.')',
         'weight_add' => 'SUM(var_t.'.Shop_Model_ProductVariants::COLUMN_WEIGHT_ADD.')',
         ))
         ->join(self::COLUMN_ID,
            array( 'comb_var' => "Shop_Model_ProductCombinationHasVariant"),
            Shop_Model_ProductCombinationHasVariant::COLUMN_ID_COMBINATION)
         ->join(array('comb_var' => Shop_Model_ProductVariants::COLUMN_ID),
            array( 'var_t' => "Shop_Model_ProductVariants"),
            Shop_Model_ProductVariants::COLUMN_ID)
         ->where(self::COLUMN_ID_PRODUCT." = :idp AND ".self::COLUMN_IS_DEFAULT." = 1", array('idp' => $idProduct))
         ->groupBy(self::COLUMN_ID)
         ->record();
      ;

   }

   public static function getCombinations($idProduct)
   {
      $model = new self();
      return $model->prepareForProductCombinations($idProduct)->records();
   }

   public static function generateCombinations($groupsVariants, $defaultVariants, $idProduct, $qty, $updatePrice = false, $onlyUpdate = false)
   {
      if(empty($groupsVariants)){
         return;
      }

      function permutations(array $array)
      {
         if(count($array) == 1){
            return $array[0];
         }
         $a = array_shift($array);
         $b = permutations($array);
         $return = array();
         foreach ($a as $v) {
            foreach ($b as $v2) {
               $return[] = array_merge(array($v), (array) $v2);
            }
         }
         return $return;
      }

      $variants = array();
      if(count($groupsVariants) > 1){
         $variants = permutations($groupsVariants);
      } else {
         foreach($groupsVariants[0] as $var) {
            $variants[] = array( $var );
         }
      }

      foreach ($variants as $variant) {
         $modelCombAttr = new Shop_Model_ProductCombinationHasVariant();
         $modelComb = new Shop_Model_ProductCombinations();

         $combination = self::getCombinationByVariants($idProduct, $variant);

         if(!$combination){
            if($onlyUpdate){ // don't add new combination
               continue;
            }
            $combination = $modelComb->newRecord();
            $combination->{Shop_Model_ProductCombinations::COLUMN_ID_PRODUCT} = $idProduct;
            $combination->{Shop_Model_ProductCombinations::COLUMN_QTY} = $qty;
         } else {
            $combination = $modelComb->record($combination->{Shop_Model_ProductCombinations::COLUMN_ID});
         }

         if(($updatePrice || $combination->isNew()) ){
            $combination->{self::COLUMN_PRICE} = Shop_Model_ProductVariants::getVariantsPrice($variant);
         }

         // create combination
         $combination->{Shop_Model_ProductCombinations::COLUMN_IS_DEFAULT} = false;

         // set default combination
         $compareDefaults = array_diff( $variant, $defaultVariants );
         if( empty( $compareDefaults ) ){
            $combination->{Shop_Model_ProductCombinations::COLUMN_IS_DEFAULT} = true;
         }
         $combination->save();

         // connect with attributes
         // delete prev attributes
         $modelCombAttr->where(Shop_Model_ProductCombinationHasVariant::COLUMN_ID_COMBINATION." = :idc",
            array('idc' => $combination->getPK()))->delete();
         foreach ($variant as $attr) {
            $comAttr = $modelCombAttr->newRecord();
            $comAttr->{Shop_Model_ProductCombinationHasVariant::COLUMN_ID_VARIANT} = $attr;
            $comAttr->{Shop_Model_ProductCombinationHasVariant::COLUMN_ID_COMBINATION} = $combination->getPK();
            $comAttr->save();
         }
      }

      // odstranit staré kombinace bez atributů a s malám počtem atributů
      $modelComb = new Shop_Model_ProductCombinations();
      $forDelete = $modelComb
         ->columns(array(
            Shop_Model_ProductCombinations::COLUMN_ID,
            'num_variants' => 'COUNT('.Shop_Model_ProductCombinationHasVariant::COLUMN_ID_VARIANT.')'
         ))
         ->join(Shop_Model_ProductCombinations::COLUMN_ID, "Shop_Model_ProductCombinationHasVariant",
            Shop_Model_ProductCombinationHasVariant::COLUMN_ID_COMBINATION)
         ->where(Shop_Model_ProductCombinations::COLUMN_ID_PRODUCT.' = :idp', array('idp' => $idProduct) )
         ->groupBy(Shop_Model_ProductCombinations::COLUMN_ID)
         ->having('num_variants != :countGrps', array('countGrps' => count($groupsVariants)))
         ->records();

      $modelComb = new Shop_Model_ProductCombinations();
      foreach ($forDelete as $c){
         $modelComb->delete($c);
      }

      self::updateProductQty($idProduct);
   }

   public static function generateCombinationsFromVariants($idProduct, $onlyUpdate = false)
   {
      // load variants

      // sort variants to groups and create default variants
   }

   public function prepareForProductCombinations($idProduct)
   {
      $this // setup model
         ->columns(array(
         Shop_Model_ProductCombinations::COLUMN_ID,
         Shop_Model_ProductCombinations::COLUMN_QTY,
         Shop_Model_ProductCombinations::COLUMN_PRICE,
         Shop_Model_ProductCombinations::COLUMN_IS_DEFAULT,
         'comb_name' =>
         'GROUP_CONCAT( attr_g.'.Model_ORM::getLangColumn(Shop_Model_AttributesGroups::COLUMN_NAME)
            .',": ", attr.'.Model_ORM::getLangColumn(Shop_Model_Attributes::COLUMN_NAME).' SEPARATOR ", ")',
//         'price' =>
//         'SUM(vari.'.Shop_Model_ProductVariants::COLUMN_PRICE_ADD.')',
         'weight' =>
         'SUM(vari.'.Shop_Model_ProductVariants::COLUMN_WEIGHT_ADD.')',
         'comb_codes_json' =>
         "CONCAT('{', GROUP_CONCAT( '\"', attr.".Shop_Model_Attributes::COLUMN_ID_GROUP.", '\"', \" : \","
            ." '\"', vari.".Shop_Model_ProductVariants::COLUMN_CODE_ADD.", '\"' SEPARATOR ', '),'}')",
         'comb_variant_ids' =>
         "GROUP_CONCAT( vari.".Shop_Model_ProductVariants::COLUMN_ID." SEPARATOR ',')",
      ))
      ->join(
            Shop_Model_ProductCombinations::COLUMN_ID,
            array( 'comb_var' => "Shop_Model_ProductCombinationHasVariant"),
            Shop_Model_ProductCombinationHasVariant::COLUMN_ID_COMBINATION)
      ->join(
            array('comb_var' => Shop_Model_ProductCombinationHasVariant::COLUMN_ID_VARIANT),
            array( 'vari' => "Shop_Model_ProductVariants"),
            Shop_Model_ProductVariants::COLUMN_ID,
            array('*'), Model_ORM::JOIN_LEFT, ' AND vari.'.Shop_Model_ProductVariants::COLUMN_ID_PRODUCT." = :idvp",
            array('idvp' => $idProduct)
      )
      ->join(
            array('vari' => Shop_Model_ProductVariants::COLUMN_ID_ATTR),
            array( 'attr' => "Shop_Model_Attributes"),
            Shop_Model_Attributes::COLUMN_ID, false
      )
      ->join(
            array('attr' => Shop_Model_Attributes::COLUMN_ID_GROUP),
            array( 'attr_g' => "Shop_Model_AttributesGroups"),
            Shop_Model_AttributesGroups::COLUMN_ID, array(Shop_Model_AttributesGroups::COLUMN_NAME)
      )
         ->where(Shop_Model_ProductCombinations::COLUMN_ID_PRODUCT." = :idp", array('idp' => $idProduct))
         ->groupBy(array(Shop_Model_ProductCombinations::COLUMN_ID));

      return $this;
   }
}

?>