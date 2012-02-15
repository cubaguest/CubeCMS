<?php
/**
 * Třída kategorie chybové stránky
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE 6.2 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro vytvoření kategorie chybové stránky
 */

class Module_DenyPage_Category extends Module_ErrPage_Category {

   private function createCatObj() {
      $category = new Object();
      // Je třeba více?
      $category->{Model_Category::COLUMN_CAT_ID} = 0;
      $category->{Model_Rights::COLUMN_RIGHT} = 'r--';
      $category->{Model_Category::COLUMN_MODULE} = 'denypage';
      $category->{Model_Category::COLUMN_CAT_LABEL} = $this->tr('Přístup odepřen');
      $category->{Model_Category::COLUMN_INDIVIDUAL_PANELS} = false;
      $category->{Model_Category::COLUMN_DESCRIPTION} = $this->tr('Chybová stránka - přístup odepřen');
      $category->{Model_Category::COLUMN_KEYWORDS} = null;
      if(defined('VVE_CM_ERR_CAT_ICON')){
         $category->{Model_Category::COLUMN_ICON} = VVE_CM_ERR_CAT_ICON;
      } else {
         $category->{Model_Category::COLUMN_ICON} = 'error.png';
      }
      return $category;
   }
}
?>