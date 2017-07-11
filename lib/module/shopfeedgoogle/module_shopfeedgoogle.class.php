<?php
/**
 * Třída Core Modulu pro obsluhu mapy stránek
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE 6.2 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro obsluhu mapy stránek
 */
class Module_ShopFeedGoogle extends Module_Core {
   
   /**
    *
    * @var Shop_Feed
    */
   protected $feed = null;


   public function runController() {
      // načtení kategorií a podle nich vytahání a vytvoření pododkazů
      $cats = new Model_Category();
      $this->feed = new Shop_Feed_Google();
      $this->feed->generate();
   }

   public function runView() {
      
   }

   public function runXmlView() {
      $file = $this->feed->getFilePath();
      $fp = fopen($file, 'rb');
      Template_Output::sendHeaders();
      fpassthru($fp);
//      header('Location: ' . Utils_Url::pathToSystemUrl($file));
      die;
      
   }
}