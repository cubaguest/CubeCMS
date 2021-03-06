<?php
/**
 * Abstraktní třída hlavního menu.
 * Třída slouží pro vytvoření hlavního menu aplikace z uživatelem definované
 * třídy pro menu, a poskytuje základní přístup k prvkům menu.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id$ VVE3.9.2 $Revision$
 * @author			$Author$ $Date$
 *						$LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro vytvoření hlavního menu
 */

class Menu_Main {
   /**
    * Objekt s šablonou
    * @var Template
    */
   private $template = null;

   /**
    * Objekt s menu
    * @var Category_Structure
    */
   protected static $menu = null;

   /**
    * Konstruktor vytvoří objek menu
    * @param Db -- objekt databázového konektoru
    */
   function __construct() {
      // šablona
      $this->template = new Template(new Url_Link(true));
      $this->init();
   }

   protected function init() {
      self::$menu = Category_Structure::getStructure(Category_Structure::VISIBLE_ONLY);
   }

   public function controller() {

   }

   public function view() {
      $this->template()->menu = self::$menu;
      $this->template()->addTplFile("menu.phtml", true);
      $this->template()->addJsPlugin(new JsPlugin_JQuery());
   }

   /**
    * Metoda vrací šablony a proměné menu
    * @return Template
    */
   public function template() {
      return $this->template;
   }

   /**
    * Metoda vrací objekt pro práci s odkazy
    * @return Links -- objekt pro prái s odkazy
    */
   protected function getLink() {
      return new Url_Link(true);
   }

   /**
    * Metoda vrací objekt menu
    * @return Category_Structure
    */
   public static function getMenuObj() {
      return self::$menu;
   }
}
?>