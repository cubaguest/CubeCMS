<?php
/**
 * Třída pro vytvoření objektu toolboxu (verze 2)
 * Třída vytváří objekt toolboxu, do ketrého se přidávají další nástroje. Samotný
 * Objekt je na stránce vykreslen jako lišta nástrojů
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id: $ VVE 6.1.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro tvorbu toolboxu verze 2
 */

class Template_Toolbox2 extends Template {
   /**
    * Adresář s ikonami
    */
   const ICONS_DIR = 'icons';

   /**
    * Pole s nástroji
    * @var array
    */
   private $tools = array();

   /**
    * Konstruktor, vytvoří základní šablonu pro nástroje.
    */
   public function  __construct() {
      parent::__construct(new Url_Link());
      $this->createTemplate();
   }

   /**
    * Magická metoda pro vložení nástroje
    * @param string $name -- název nástroje
    * @param mixed $value -- hodnota proměnné nebo nástroj
    */
   public function  __set($name, $value) {
      if($value instanceof Template_Toolbox2_Tool){
         $this->tools[$name] = $value;
      } else {
         $this->privateVars[$name] = $value;
      }
   }

   /**
    * Metoda vraci inicializovanou proměnnou, pokud je
    * @param string $name -- název proměnné
    * @return mixed -- hodnota proměnné
    */
   public function  &__get($name) {
      if(isset($this->tools[$name])) {
         return $this->tools[$name];
      } else if(isset($this->privateVars[$name])){
         return $this->privateVars[$name];
      }
      $null = null;
      return $null;
   }

   /**
    * Metoda kontroluje jestli byla daná proměnná inicializována
    * @param string $name -- název proměnné
    * @return mixed -- hodnota proměnné
    */
   public function  __isset($name) {
      if(isset($this->tools[$name]) OR isset ($this->privateVars[$name])){
         return true;
      }
      return false;
   }

   /**
    * Metoda maže danou proměnnou z objektu
    * @param string $name -- název proměnné
    */
   public function  __unset($name) {
      if(isset ($this->tools[$name])) {
         unset ($this->tools[$name]);
      } else if(isset ($this->privateVars[$name])){
         unset ($this->privateVars[$name]);
      }
   }

   /**
    * Metoda zjišťuje jestli se jedná o objekt nástroje (tool)
    * @param string $name -- název nástroje
    */
   public function isTool($name) {
      return isset ($this->tools[$name]);
   }

   /**
    * Metoda vytvoří šablonu
    */
   private function createTemplate() {
      $this->addTplFile("toolbox/toolbox.phtml", true);
      $this->addJsPlugin(new JsPlugin_JQuery());

      $this->iconsDir = Url_Request::getBaseWebDir().Template::IMAGES_DIR.URL_SEPARATOR.self::ICONS_DIR.URL_SEPARATOR;
   }

   /**
    * Metoda přidá do šablony nástroj
    * @param Template_Toolbox2_Tool $tool -- nástroj
    * @return Template_Toolbox2 -- vrací sám sebe
    */
   public function addTool(Template_Toolbox2_Tool $tool) {
      $this->{$tool->getName()} = $tool;
      return $this;
   }

   /**
    * Metoda vrací pole se všemi nástroji
    * @return array
    */
   public function getTools() {
      return $this->tools;
   }
}
?>