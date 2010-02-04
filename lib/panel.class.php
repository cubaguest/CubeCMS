<?php
/**
 * Abstraktní třída pro práci s panely.
 * Základní třída pro tvorbu tříd panelů jednotlivých modulu. Poskytuje prvky
 * základního přístu jak k vlastnostem modelu tak pohledu. Pomocí této třídy
 * se také generují šablony panelů.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Abstraktní třída pro práci s panely
 * @todo				Není implementována práce s chybami
 */

abstract class Panel {
   const DATA_DIR = 'panels';

   const ICONS_DIR = 'icons';

   const PANEL_DEFAULT_TPL = 'panel.phtml';
   /**
    * Objekt šablony
    * @var Template_Module
    */
   private $template = null;

   /**
    * Objekt kategorie
    * @var Category
    */
   private $category = null;

   /**
    * Objket pro lokalizaci
    * @var Locale
    */
   private $locale = null;

   /**
    * Objekt cest modulu
    * @var Routes
    */
   private $routes = null;

   /**
    * objekt linku
    * @var Url_Link
    */
   private $link = null;

   /**
    * Konstruktor
    * @param Category $category -- obejkt kategorie
    * @param Routes $routes -- objekt cest pro daný modul
    */
   function __construct(Category $category, Routes $routes) {
      $this->category = $category;
      $this->routes = $routes;

      $link = new Url_Link_Module();
      $link->setModuleRoutes($routes);
      $link->clear(true)->category($this->category()->getUrlKey());
      $this->link = $link;
      // locales
      $this->locale = new Locale($category->getModule()->getName());
      $this->template = new Template_Module(clone $this->link, $this->category);

      if($category->getCatDataObj()->{Model_Panel::COLUMN_TPL} == null){
         $category->getCatDataObj()->{Model_Panel::COLUMN_TPL} = self::PANEL_DEFAULT_TPL;
      }
      $this->template->panel = $category->getCatDataObj();
   }

   /**
    * Metoda controleru panelu
    */
   abstract function panelController();

   /**
    * Metoda viewru panelu
    */
   abstract function panelView();

   /**
    * Metoda spustí metody pro obsluhu panelu
    */
   final public function run() {
      $this->panelController();
      $this->panelView();
   }

   /**
    * Metoda vrací objekt panelu
    * @return Object
    */
   final public function panelObj() {
      return $this->category()->getCatDataObj();
   }

   /**
    * Metoda vrací odkaz na objekt pro práci s odkazy
    * @return Links -- objekt pro práci s odkazy
    */
   final public function link($clear = true) {
      $link = clone $this->link;
      if($clear){
         $link->clear();
      }
      return $link;
   }

   /**
    * Metody vrací objekt kategorie
    * @return Category -- objekt kategorie
    */
   final public function category() {
      return $this->category;
   }

   /**
    * Metoda vrací objekt s informačními zprávami
    * @return Messages -- objekt zpráv
    */
   final public function infoMsg() {
      return AppCore::getInfoMessages();
   }

   /**
    * Metoda vrací objekt s chybovými zprávami
    * @return Messages -- objekt zpráv
    */
   final public function errMsg() {
      return AppCore::getUserErrors();
   }

   /**
    * Metoda vrací objekt šablony, přes kerý se přiřazují proměnné do šablony
    * @return Template_Module -- objekt šablony
    */
   final public function template(){
      return $this->template;
   }

   /**
    * Metoda vrací objekt šablony
    * @return Template
    */
   final public function _getTemplateObj() {
      return $this->template();
   }

   /**
    * Metoda přeloží zadaný řetězec
    * @param string $message -- řetězec k přeložení
    * @return string -- přeložený řetězec
    */
   final public function _($message) {
      return $this->locale()->_($message);
   }

   /**
    * Metoda přeloží zadaný řetězec v plurálu
    * @param string $message1 -- řetězec k přeložení - jč
    * @param string $message2 -- řetězec k přeložení - plurál
    * @param integer $int -- počet
    * @return string -- přeložený řetězec
    */
   final public function ngettext($message1, $message2, $int) {
      return $this->locale()->ngettext($message1, $message2, $int);
   }

   /**
    * Metoda vrací objekt lokalizace
    * @return Locale
    */
   final public function locale() {
      return $this->locale;
   }

   /**
    * Metodda vrací adresář s ikonami panelů
    * @return string
    */
   final static function getIconDir() {
      return Url_Request::getBaseWebDir().VVE_DATA_DIR.URL_SEPARATOR.self::DATA_DIR
      .URL_SEPARATOR.self::ICONS_DIR.URL_SEPARATOR;
   }

   /**
    * Metoda vrcí adresář s pozadím panelu
    * @return string
    */
   final static function getBackImgDir() {
      return Url_Request::getBaseWebDir().VVE_DATA_DIR.URL_SEPARATOR.self::DATA_DIR
      .URL_SEPARATOR;
   }
}
?>