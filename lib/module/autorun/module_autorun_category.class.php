<?php
/**
 * Třída kategorií pro automatický spouštěč
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: category.class.php 1390 2010-08-09 06:40:37Z jakub $ VVE3.9.4 $Revision: 1390 $
 * @author        $Author: jakub $ $Date: 2010-08-09 08:40:37 +0200 (Po, 09 srp 2010) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2010-08-09 08:40:37 +0200 (Po, 09 srp 2010) $
 * @abstract 		Třída pro vytvoření kategorie automatického spouštěče
 */

class Module_AutoRun_Category extends Category_Core {
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
      $category->{Model_Category::COLUMN_MODULE} = 'autorun';
      $category->{Model_Category::COLUMN_URLKEY} = 'autorun.php';
      $category->{Model_Category::COLUMN_CAT_LABEL} = $this->tr('Plánované úlohy');
      $category->{Model_Category::COLUMN_INDIVIDUAL_PANELS} = false;
      $category->{Model_Category::COLUMN_DESCRIPTION} = $this->tr('Spouštěč plánovaných úloh');
      $category->{Model_Category::COLUMN_KEYWORDS} = null;
      $category->{Model_Category::COLUMN_ICON} = null;
      $category->{Model_Module::COLUMN_VERSION} = '1.0.0';
      return $category;
   }
}
?>