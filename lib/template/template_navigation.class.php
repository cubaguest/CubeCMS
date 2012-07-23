<?php
/**
 * Třída pro práci s šablonami modulu.
 * Třida obsahuje vrstvu mezi šablonovacím systémem a samotným pohledem (viewrem).
 * Umožňuje všechny základní operace při volbě a plnění šablony a jejímu zobrazení.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: template_core.class.php 2659 2012-03-02 13:46:31Z jakub $ Cube CMS 7.7 $Revision: 2659 $
 * @author        $Author: jakub $ $Date: 2012-03-02 14:46:31 +0100 (Pá, 02 III 2012) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2012-03-02 14:46:31 +0100 (Pá, 02 III 2012) $
 * @abstract 		Třída pro obsluhu šablony
 */

class Template_Navigation extends Template {

   /**
    * Nastavená šablony systému
    * @var string
    */
   protected static $template = 'navigation.phtml';

   
   protected static $baseParts = array();
   protected static $extendedParts = array();
   
   /**
    * Konstruktor
    */
   function  __construct() {
      parent::__construct(new Url_Link());
      $this->addFile('tpl://'.self::$template);
      // příprava navigace
      if(empty(self::$baseParts)){
         self::prepareNavigation();
      }
   }

   public function __toString() 
   {
      $this->baseParts = self::$baseParts;
      $this->extendedParts = self::$extendedParts;
      return parent::__toString();
   }
   
   /**
    * Metoda přidá položku do navigace (drobečkové menu)
    * @param array $item -- polžka do navigace ( array('name' => null, 'title' => null, 'link' => null, 'image' => null, ) )
    */
   public static function addItem($name, $link = null, $title = null, $image = null, $key = null, $admin = false) 
   {
      $arr = array('name' => $name, 'title' => $title, 'link' => $link, 'image' => $image, 'admin' => $admin);
      if((string)$arr['title'] == null){
         $arr['title'] = $arr['name'];
      }
      if($link == null){
         $link = new Url_Link();
      }
      if($key == null){
         array_push(self::$extendedParts, $arr );
      } else {
         self::$extendedParts[$key] = $arr;
      }
   }
   
   /**
    * Metoda vrací kompletní navigaci
    * @return array
    */
   public static function getNavigation() 
   {
      $merged = array_merge(self::$baseParts, self::$extendedParts);
      return $merged;
   }
   
   /**
    * Metoda načte základní navigaci po kategoriích
    */
   protected static function prepareNavigation() 
   {
      $link = new Url_Link(true);
      if(Category::getSelectedCategory() instanceof Category_Admin){
         $img = Category::getSelectedCategory()->getCatDataObj()->{Model_Category::COLUMN_ICON};
         self::$baseParts[] = array(
               'name' => (string)Category::getSelectedCategory()->getName(),
               'title' => (string)Category::getSelectedCategory()->getName(true),
               'link' => (string)$link->clear(true)->category(Category::getSelectedCategory()->getUrlKey()),
               'image' => ( $img != null ? Category::getImageDir().$img : null),
               'admin' => true,
         );
      } else if(Category::getSelectedCategory() instanceof Category){
         $struct = Category_Structure::getStructure();
         $path = $struct->getPath();
         if(!empty($path)){
            foreach ($path as $item) {
               if($item->getId() == 0){
                  continue;
               }
               $img = $item->getCatObj()->getCatDataObj()->{Model_Category::COLUMN_ICON};
               self::$baseParts[] = array(
                  'name' => (string)$item->getCatobj()->getName(),      
                  'title' => (string)$item->getCatObj()->getName(true),      
                  'link' => (string)$link->clear(true)->category($item->getCatObj()->getUrlKey()),      
                  'image' => ( $img != null ? Category::getImageDir().$img : null),      
                  'admin' => false,      
               );
            }
         }
      } else {
         $img = Category::getSelectedCategory()->getCatDataObj()->{Model_Category::COLUMN_ICON};
         self::$baseParts[] = array(
               'name' => (string)Category::getSelectedCategory()->getName(),
               'title' => (string)Category::getSelectedCategory()->getName(true),
               'link' => (string)$link->clear(true),
               'image' => ( $img != null ? Category::getImageDir().$img : null),
               'admin' => false,      
         );
      }
   }
   
}
?>