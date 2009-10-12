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
    * @var Category_Structure
    */
   protected $menu = null;

   /**
    * Pole s aktuální cestou
    * @var array
    */
   private $currentPath = array();

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
//      $menu = new Category_Structure(0);
//
//      $cat1 = new Category_Structure(1);
//      $menu->addChild(&$cat1);
//
//      $cat1->addChild(new Category_Structure(12));
//      $cat1->addChild(new Category_Structure(13));
//
//
//      $cat2 = new Category_Structure(50);
//      $menu->addChild(&$cat2);
//      $cat2->addChild(new Category_Structure(14));
//
//      $menu->saveStructure();

         // načtení menu z
      $menu = unserialize(VVE_CATEGORIES_STRUCTURE);

      $catModel = new Model_Category();
      $menu->setCategories($catModel->getCategoryList());

      $this->menu = $menu;
      var_dump(Category::getMainCategory()->getId());
      // cesta ke kategorii
      $menu->getPath(Category::getMainCategory()->getId(), $this->currentPath);
//      rsort($this->currentPath);
//      foreach ($this->currentPath as $path) {
//         print ($path->getCatObj()->{Model_Category::COLUMN_CAT_LABEL}." >> ");
//      }

//      $menu->render();
   }

   public function view() {
      $this->template()->setPVar('CATEGORY_PATH', $this->currentPath);
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