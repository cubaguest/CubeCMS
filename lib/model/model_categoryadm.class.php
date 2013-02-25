<?php

/**
 * Třída Modelu pro práci s kategoriemi.
 * Třída, která umožňuje pracovet s modelem kategorií
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: model_category.class.php 1989 2011-03-16 18:47:03Z jakub $ VVE3.9.2 $Revision: 1989 $
 * @author			$Author: jakub $ $Date: 2011-03-16 19:47:03 +0100 (St, 16 bře 2011) $
 * 						$LastChangedBy: jakub $ $LastChangedDate: 2011-03-16 19:47:03 +0100 (St, 16 bře 2011) $
 * @abstract 		Třída pro vytvoření modelu pro práci s kategoriemi
 * @todo          nutný refaktoring
 */
class Model_CategoryAdm extends Model_File {
   const STRUCTURE_FILE = 'admmenu.xml';
   const STRUCTURE_SHOP_FILE = 'admshopmenu.xml';
   /**
    * Objekt s admin menu
    * @var SimpleXMLElement
    */
   private static $structure = null;

   public function __construct()
   {
      if(self::$structure === null){
         self::$structure = new SimpleXMLElement(AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR.DIRECTORY_SEPARATOR
            .'menu'.DIRECTORY_SEPARATOR.self::STRUCTURE_FILE, NULL, TRUE);
         // shop struct
         if(defined('VVE_SHOP') && VVE_SHOP == true){
            $shopStructure = new SimpleXMLElement(AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR.DIRECTORY_SEPARATOR
               .'menu'.DIRECTORY_SEPARATOR.self::STRUCTURE_SHOP_FILE, NULL, TRUE);
            $this->appendSimplexml(self::$structure, $shopStructure);
         }
         // user struct
         if(is_file(AppCore::getAppWebDir().AppCore::ENGINE_CONFIG_DIR.DIRECTORY_SEPARATOR.self::STRUCTURE_FILE)){
            $userStructure = new SimpleXMLElement(AppCore::getAppLibDir().AppCore::ENGINE_CONFIG_DIR.DIRECTORY_SEPARATOR.self::STRUCTURE_FILE, NULL, TRUE);
            $this->appendSimplexml(self::$structure, $userStructure);
         }
      }
   }

   private function appendSimplexml(&$simplexml_to, &$simplexml_from)
   {
      foreach ($simplexml_from->children() as $simplexml_child)
      {
         $simplexml_temp = $simplexml_to->addChild($simplexml_child->getName(), (string) $simplexml_child);
         foreach ($simplexml_child->attributes() as $attr_key => $attr_value)
         {
            $simplexml_temp->addAttribute($attr_key, $attr_value);
         }
         $this->appendSimplexml($simplexml_temp, $simplexml_child);
      }
   }

   public function getCategory($urlkey){
      $child = self::$structure->xpath("//item[child::urlkey=\"".$urlkey."\" and child::urlkey[@lang=\"".Locales::getLang()."\"]]");
      return empty($child) ? false : self::createCatObject($child[0]);
   }

   public static function getCategoryByID($id){
      $child = self::$structure->xpath("//item[@id=\"".$id."\" and child::urlkey[@lang=\"".Locales::getLang()."\"]]");
      return empty($child) ? false : self::createCatObject($child[0]);
   }

   public static function getCategoryByModule($module){
      $child = self::$structure->xpath("//item[child::module=\"".$module."\" and child::urlkey[@lang=\"".Locales::getLang()."\"]]");
      return empty($child) ? false : self::createCatObject($child[0]);
   }

   private function createCatObject($child){
      $obj = new Object();
      $urlkey = $child->xpath('urlkey[@lang="'.Locales::getLang().'"]');
      if(empty($urlkey)){
         $urlkey = $child->xpath('urlkey[@lang="cs"]');
      }
      $obj->{Model_Category::COLUMN_URLKEY} = (string)$urlkey[0];
      $name = $child->xpath('name[@lang="'.Locales::getLang().'"]');
      if(empty($name)){
         $name = $child->xpath('name[@lang="cs"]');
      }
      $obj->{Model_Category::COLUMN_NAME} = (string)$name[0];
      $obj->{Model_Category::COLUMN_MODULE} = (string)$child->module;
      $obj->{Model_Category::COLUMN_PARAMS} = (string)$child->params;
      $obj->{Model_Category::COLUMN_ID} = (int)$child['id'];
      $obj->{Model_Category::COLUMN_DATADIR} = null;
      if(isset($child->datadir)){
         $obj->{Model_Category::COLUMN_DATADIR} = (string)$child->datadir;
      }
      return $obj;
   }

   /**
    * Metoda načte všechny kategorie
    * @return array of Model_ORM_Records -- pole s objekty
    */
   public function getCategoryList()
   {
      $retArray = array();
      foreach(self::$structure->xpath("//item") as $child) {
         array_push($retArray, self::createCatObject($child));
      }
      return $retArray;
   }

   /**
    * Metoda načte všechny kategorie i se strukturou
    * @return array of Model_ORM_Records -- pole s objekty
    */
   public function getStructure()
   {
      return self::$structure;
   }

   public static function getCategoryListByModule($module, $onlyWithRights = true)
   {

   }

}
?>