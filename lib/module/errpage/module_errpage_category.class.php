<?php
/**
 * Třída kategorie chybové stránky
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE 7.18 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro vytvoření kategorie chybové stránky
 */

class Module_ErrPage_Category extends Category_Core {
   /**
    * Konstruktor načte informace o kategorii
    * @string $catKey --  klíč kategorie
    * @bool $isMainCategory --  (option) jest-li se jedná o hlavní kategorii
    */
   public function  __construct($catKey = null, $isSelectedCategory = false, $categoryDataObj = null) {
      parent::__construct($catKey, $isSelectedCategory, $this->createCatObj());
   }

   protected function createCatObj() {
      $category = new Object();
      // Je třeba více?
      $category->{Model_Category::COLUMN_CAT_ID} = 0;
      $category->{Model_Rights::COLUMN_RIGHT} = 'r--';
      $category->{Model_Category::COLUMN_MODULE} = 'errpage';
      $category->{Model_Category::COLUMN_CAT_LABEL} = $this->tr('Chyba');
      $category->{Model_Category::COLUMN_INDIVIDUAL_PANELS} = false;
      $category->{Model_Category::COLUMN_DESCRIPTION} = $this->tr('Chybová stránka');
      $category->{Model_Category::COLUMN_KEYWORDS} = null;
      $category->{Model_Module::COLUMN_VERSION} = '1.0.0';
      if(defined('VVE_CM_ERR_CAT_ICON')){
         $category->{Model_Category::COLUMN_ICON} = VVE_CM_ERR_CAT_ICON;
      } else {
         $category->{Model_Category::COLUMN_ICON} = 'error.png';
      }
      return $category;
   }
}
