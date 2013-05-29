<?php
/**
 * Třída kategorií mvýpisu rss kanálů
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: category.class.php 1390 2010-08-09 06:40:37Z jakub $ VVE3.9.4 $Revision: 1390 $
 * @author        $Author: jakub $ $Date: 2010-08-09 08:40:37 +0200 (Po, 09 srp 2010) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2010-08-09 08:40:37 +0200 (Po, 09 srp 2010) $
 * @abstract 		Třída pro vytvoření kategorie rss kanálů
 */

class Module_Rss_Category extends Category_Core {
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
      $category->{Model_Category::COLUMN_MODULE} = 'rss';
      $category->{Model_Category::COLUMN_URLKEY} = 'rss.html';
      $category->{Model_Category::COLUMN_CAT_LABEL} = $this->tr('Přehled rss kanálů');
      $category->{Model_Category::COLUMN_INDIVIDUAL_PANELS} = false;
      $category->{Model_Category::COLUMN_DESCRIPTION} = $this->tr('Kompletní rss kanálů vyvedených ze stránek');
      $category->{Model_Category::COLUMN_KEYWORDS} = null;
      if(defined('VVE_CM_RSS_CAT_ICON')){
         $category->{Model_Category::COLUMN_ICON} = VVE_CM_RSS_CAT_ICON;
      } else {
         $category->{Model_Category::COLUMN_ICON} = 'rsslist.png';
      }
      return $category;
   }
}
