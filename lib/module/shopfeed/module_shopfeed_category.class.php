<?php
/**
 * Třída kategorií mapy stránek
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: category.class.php 1390 2010-08-09 06:40:37Z jakub $ VVE3.9.4 $Revision: 1390 $
 * @author        $Author: jakub $ $Date: 2010-08-09 08:40:37 +0200 (Po, 09 srp 2010) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2010-08-09 08:40:37 +0200 (Po, 09 srp 2010) $
 * @abstract 		Třída pro vytvoření kategorie mapy stránek
 */

class Module_ShopFeed_Category extends Category_Core {
   /**
    * Konstruktor načte informace o kategorii
    * @string $catKey --  klíč kategorie
    * @bool $isMainCategory --  (option) jest-li se jedná o hlavní kategorii
    */
   public function  __construct($catKey = null, $isSelectedCategory = false, $categoryDataObj = null) {
      parent::__construct($catKey, $isSelectedCategory, $this->createCatObj());
   }

   private function createCatObj() {
      $category = new Object();
      // Je třeba více?
      $category->{Model_Category::COLUMN_CAT_ID} = 0;
      $category->{Model_Rights::COLUMN_RIGHT} = 'r--';
      $category->{Model_Category::COLUMN_MODULE} = 'shopfeed';
      $category->{Model_Category::COLUMN_CAT_LABEL} = $this->tr('XML feed zboží');
      $category->{Model_Category::COLUMN_INDIVIDUAL_PANELS} = false;
      $category->{Model_Category::COLUMN_DESCRIPTION} = $this->tr('XML feed zboží');
      $category->{Model_Category::COLUMN_KEYWORDS} = null;
      $category->{Model_Module::COLUMN_VERSION} = '1.0.0';
      return $category;
   }
}
