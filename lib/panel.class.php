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
   /**
    * Objekt s šablonovacím systémem
    * @var Template
    */
   private $_template = null;

   /**
    * Proměnná obsahuje objekt systému modulu
    * @var Module_Sys
    */
   private $_moduleSys = null;

   /**
    * Konstruktor
    * @param Module_Sys $sys -- systémový objekt modulu
    */
   function __construct(Module_Sys $sys) {
      $this->_template = new Template($sys);
      $this->_moduleSys = $sys;

      //$action = null;
      // název třídy s akcí
      $actionClassName = ucfirst($this->module()->getName()).'_Action';
      //		Pokud ještě nebyla třída načtena
      //      if(!class_exists($actionClassName,false)){
      //         //			načtení souboru s akcemi modulu
      //         if(!file_exists('.' . DIRECTORY_SEPARATOR . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR . $this->module()->getName() . DIRECTORY_SEPARATOR . 'action.class.php')){
      //            throw new BadClassException(sprintf(_('Nepodařilo se nahrát akci modulu '), $this->module()->getName()),1);
      //         }
      //         require '.' . DIRECTORY_SEPARATOR . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR . $this->module()->getName() . DIRECTORY_SEPARATOR . 'action.class.php';
      //      }


      //               var_dump($actions);flush();
      if(class_exists($actionClassName)){
         $this->sys()->setAction(new $actionClassName($this->sys()->module()));
      } else {
         $panelSys->setAction(new Action($this->module()->getName(), $panelSys->article()));
      }

      //		Cesty
      $routes = null;
      $routesClassName = ucfirst($this->module()->getName()).'_Routes';
      //		Pokud ještě nebyla třída načtena
      //      if(!class_exists($routesClassName, false)){
      //         //			načtení souboru s cestami (routes) modulu
      //         if(!file_exists('.' . DIRECTORY_SEPARATOR . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR . $this->module()->getName() . DIRECTORY_SEPARATOR . 'routes.class.php')){
      //            throw new BadClassException(sprintf(_('Nepodařilo se nahrát akci modulu '), $this->module()->getName()),2);
      //         }
      //         require '.' . DIRECTORY_SEPARATOR . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR . $this->module()->getName() . DIRECTORY_SEPARATOR . 'routes.class.php';
      //      }
      if(class_exists($routesClassName)){
         $this->sys()->setRoute(new $routesClassName($this->article()));
      } else {
         $this->sys()->setRoute(new Routes($this->article()));
      }
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
    * Metoda vrací systémový objekt modulu
    * @return Module_Sys
    */
   final public function sys() {
      return $this->_moduleSys;
   }

   /**
    * Metoda vrací odkaz na objekt pro práci s odkazy
    * @return Links -- objekt pro práci s odkazy
    */
   final public function link($clear = true) {
      //$link = new Links($clear);
      //return $link->category($this->_category[Category::COLUMN_CAT_LABEL],
      //	$this->_category[Category::COLUMN_CAT_ID]);
      $link = $this->sys()->link();
      if($clear){
         $link->clear();
      }
      return $link;
   }

   /**
    * Metody vrací objekt modulu
    * @return Module -- objekt modulu
    */
   final public function module() {
      return $this->sys()->module();
   }

   /**
    * Metoda vrací objekt na akci
    * @return ModuleAction -- objekt akce
    */
   final public function action() {
      return $this->sys()->action();
   }

   /**
    * Metoda vrací objekt na akci
    * @return ModuleAction -- objekt akce
    */
   final public function route() {
      return $this->sys()->route();
   }

   /**
    * Metoda vrací objekt s právy na modul
    * @return Rights -- objekt práv
    */
   final public function rights() {
      return $this->sys()->rights();
   }

   /**
    * Metoda vrací objekt s článkem
    * @return Article -- objekt článku
    */
   final public function article() {
      return $this->sys()->rights();
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
    * @return Template -- objekt šablony
    */
   final public function template(){
      return $this->_template;
   }

   /**
    * Metoda vrací objekt šablony
    * @return Template
    */
   final public function _getTemplateObj() {
      return $this->template();
   }

   /**
    * Metoda vytvoří objekt modelu
    * @param string $name --  název modelu
    * @return Objekt modelu
    */
   final public function createModel($name) {
      return new $name($this->sys());
   }
}
?>