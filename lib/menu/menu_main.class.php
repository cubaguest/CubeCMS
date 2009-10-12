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
 * @todo          Předělat načítání SQL položek do modelu
 */

class Menu_Main {
   /**
    * Objekt s šablonou
    * @var Template
    */
   private $template = null;

   /**
    * Objekt menu se sekcemi
    * @var Menu_Sections
    */
   protected $menu = null;

   /**
    * Konstruktor vytvoří objek menu
    * @param Db -- objekt databázového konektoru
    */
   function __construct() {
      // šablona
      $this->template = new Template(new Url_Link(true));
      $this->init();
   }

   protected function init() {}

   public function controller() {
         // načtení menu z
      $menu = unserialize(VVE_CATEGORIES_STRUCTURE);

      $catModel = new Model_Category();
      $categories = $catModel->getCategoryList();
      $catArray = array();
      foreach ($categories as $row) {
         $catArray[$row[Model_Category::COLUMN_CAT_ID]] = $row;
      }

      $menu->setCategories($catArray);

      $this->menu = $menu;
   }

   public function view() {
      $this->template()->menu = $this->menu;
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
}
?>