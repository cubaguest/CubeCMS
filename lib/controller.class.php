<?php
/**
 * Abstraktní třída tvorbu kontroleru modulu.
 * Třída slouží jako základ pro tvorbu kontroleru modulu. Poskytuje přístup
 * k vlastnostem modulu, práv, hláškám, článku a kontaineru(přenos dat do
 * pohledu a šablony)
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Abstraktní třída kontroleru modulu
 */

abstract class Controller {
/**
 * Název nového actionViewru
 * @var string
 */
   private $actionViewer = null;

   /**
    * Objket viewru
    * @var View
    */
   private $viewObj = null;

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
    * Konstruktor třídy vytvoří a naplní základní vlastnosti pro modul
    *
    * @param Category $category -- obejkt kategorie
    * @param Routes $routes -- objekt cest pro daný modul
    */
   public final function __construct(Category $category, Routes $routes) {
   //TODO odstranit nepotřebné věci v paramtrech konstruktoru
   //      $this->container = new Container();
      $this->category = $category;
      $this->routes = $routes;

      $link = new Url_Link_Module();
      $link->setModuleRoutes($routes);
      $link->category($this->category()->getUrlKey());
      $this->link = $link;
      // locales
      $this->locale = new Locale($category->getModule()->getName());

      //		Načtení třídy View
      $viewClassName = ucfirst($category->getModule()->getName()).'_View';
      //      if(!class_exists($viewClassName)){
      //         throw new BadClassException(sprintf(_('Třída viewru "%s" modulu "%s" neexistuje')
      //               , $viewClassName, $this->module()->getName()),1);
      //      }
      //	Vytvoření objektu pohledu
      $this->viewObj = new $viewClassName(clone $this->link(), $this->category());

      // Inicializace kontroleru modulu
      $this->init();
   }



   /**
    * Inicializační metoda pro kontroler. je spuštěna vždy při vytvoření objektu
    * kontroleru
    */
   protected function init() {}

   /**
    * Metoda vrací objekt systémové konfigurace
    * @return Config -- objekt systémové konfigurace
    */
   final public function getSysConfig() {
      return AppCore::sysConfig();
   }

   /**
    * MEtoda vrací objekt viewru modulu
    * @return View
    */
   final public function view() {
      return $this->viewObj;
   }

   /**
    * Metoda vrací odkaz na objekt pro práci s odkazy
    * @param boolean -- true pokud má byt link čistý
    * @param boolean -- true pokud má byt link bez kategorie
    * @return Links -- objekt pro práci s odkazy
    * @deprecated
    */
   //   final public function getLink($clear = false, $onlyWebRoot = false) {
   //      return $this->link($clear, $onlyWebRoot);
   //   }

   /**
    * Metoda vrací odkaz na objekt pro práci s odkazy
    * @param boolean -- true pokud má byt link čistý
    * @param boolean -- true pokud má byt link bez kategorie
    * @return Url_Link_Module -- objekt pro práci s odkazy
    */
   final public function link($clear = false) {
      $link = clone $this->link;
      if($clear) {
         $link->clear();
      }
      return $link;
   }

   /**
    * Metody vrací objekt modulu
    * @return Module -- objekt modulu
    * @deprecated
    */
   final public function getModule() {
      return $this->category()->getModule();
   }

   /**
    * Metody vrací objekt modulu
    * @return Module -- objekt modulu
    */
   final public function module() {
      return $this->category()->getModule();
   }

   /**
    * Metoda vrací objekt kategorie
    * @return Category
    */
   final public function category() {
      return $this->category;
   }

   /**
    * Metoda vrací objekt cest
    * @return Routes
    */
   final public function routes() {
      return $this->routes;
   }

   /**
    * Metoda vrací parametry požadavku vybrané z URL
    * @param string $name -- název parametru
    * @param mixed $def -- výchozí hodnota vrácená pokud nebyl parametr přenesen
    * @return mixed -- hodnota parametru
    */
   final public function getRequest($name, $def = null) {
      return $this->routes()->getRouteParam($name, $def);
   }

   /**
    * Metoda vrací parametr předaný v url za cestou
    * @param string $name -- název parametru
    * @param mixed $def -- výchozí hodnota vrácená pokud nebyl parametr přenesen
    * @return mixed -- hodnota parametru
    */
   final public function getRequestParam($name, $def = null) {
      if(isset ($_GET[$name])) {
         return urldecode($_GET[$name]);
      } else {
         return $def;
      }
   }

   /**
    * Metoda vrací objekt lokalizace
    * @return Locale
    */
   final public function locale() {
      return $this->locale;
   }

   /**
    * Metody vrací objekt autorizace (infoormace o přiihlášení)
    * @return Auth -- objekt autorizace
    * @deprecated
    */
   final public function getAuth() {
      return $this->auth();
   }

   /**
    * Metody vrací objekt autorizace (infoormace o přiihlášení)
    * @return Auth -- objekt autorizace
    */
   final public function auth() {
   //      return $this->sys()->rights()->getAuth();
   }

   /**
    * Metoda vrací objekt s právy na modul
    * @return Rights -- objekt práv
    * @deprecated
    */
   final public function getRights() {
      return $this->rights();
   }

   /**
    * Metoda vrací objekt s právy na modul
    * @return Rights -- objekt práv
    */
   final public function rights() {
      return $this->category()->getRights();
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
    * Metoda volaná při destrukci objektu
    */
   function __destruct() {
   }

   /**
    * Metoda změní výchozí actionViewer pro danou akci na zadaný viewer
    * @param string -- název actionViewru
    */
   final public function changeActionView($newActionView) {
      $this->actionViewer = $newActionView;
   }

   /**
    * Metoda vrací zvolený actionViewer nebo false pokud je nulový
    * @return string -- název nového actionViewru
    */
   final public function getActionView() {
      return $this->actionViewer;
   }

   /**
    * Metoda kontroluje práva pro čtení modulu. v opačném případě vyvolá přesměrování
    */
   final public function checkReadableRights() {
      if(!$this->rights()->isReadable()) {
         $this->errMsg()->addMessage(_("Nemáte dostatčná práva pro přístup ke
kategorii nebo jste byl(a) odhlášen(a)"), true);
         $this->link(true)->reload();
      }
   }
   /**
    * Metoda kontroluje práva pro zápis do modulu. v opačném případě vyvolá přesměrování
    */
   final public function checkWritebleRights() {
      if(!$this->rights()->isWritable()) {
         $this->errMsg()->addMessage(_("Nemáte dostatčná práva pro přístup ke
kategorii nebo jste byl(a) odhlášen(a)"), true);
         $this->link(true)->reload();
      }
   }
   /**
    * Metoda kontroluje práva pro plný přístup k modulu. v opačném případě vyvolá přesměrování
    */
   final public function checkControllRights() {
      if(!$this->rights()->isControll()) {
         $this->errMsg()->addMessage(_("Nemáte dostatčná práva pro přístup ke
kategorii nebo jste byl(a) odhlášen(a)"), true);
         $this->link(true)->reload();
      }
   }

   /**
    * Metoda nastaví název použitého viewru (bez sufixu View)
    *
    * @param string -- název viewru
    */
   final public function setView($view) {
      $this->actionViewer = $view;
   }

   /**
    * Metoda vrací objekt šablony
    * @return Template
    */
   final public function _getTemplateObj() {
      return $this->view()->template();
   }

   /**
    * Metoda spustí metodu kontroleru a viewer modulu
    */
   final public function runCtrl() {
      if(!method_exists($this, $this->routes()->getActionName().'Controller')) {
         throw new BadMethodCallException(sprintf(_('neimplementovaná akce "%sController" v kontroleru modulu "%s"'),
         $this->routes()->getActionName(), $this->module()->getName()), 1);
      }

      if($this->actionViewer === null) {
         $viewName = $this->routes()->getActionName().'View';
      } else {
         $viewName = $this->actionViewer.'View';
      }
      //	zvolení viewru modulu pokud existuje
//      if(!method_exists($this->view(), $viewName)) {
//         throw new BadMethodCallException(sprintf(
//         _("Action Viewer \"%sView\" v modulu \"%s\" nebyl implementován"),
//         $viewName, $this->module()->getName()), 2);
//      }
      // spuštění kontroleru
      $ctrlResult = $this->{$this->routes()->getActionName().'Controller'}();

      if(method_exists($this->view(), $viewName) AND $ctrlResult !== false) {
         $this->view()->$viewName();
      }
   }

   /**
    * Metoda spustí zadanou akci na daném kontroleru. Kontroluje, jestli existuje
    * metoda viewru pro daný výstup, nebo ne
    * @param string $actionName -- název akce
    * @param string $outputType -- typ výstupu
    */
   final public function runCtrlAction($actionName, $outputType) {
      if(!method_exists($this, $actionName.'Controller')) {
         trigger_error(sprintf(_('neimplementovaná akce "%sController" v kontroleru modulu "%s"'),
             $actionName, $this->module()->getName()));
      }

      $viewName = null;
      //	zvolení viewru modulu pokud existuje
      if(method_exists($this->view(), $actionName.ucfirst($outputType).'View')) {
         $viewName = $actionName.ucfirst($outputType).'View';
      } else if(method_exists($this->view(), $actionName.'View')) {
         $viewName = $actionName.'View';
      } else {
         trigger_error(sprintf(_("Action Viewer \"%sView\" nebo \"%s\" v modulu \"%s\" nebyl implementován"),
             $actionName, $actionName.ucfirst($outputType).'View', $this->module()->getName()));
      }

      // spuštění Viewru
      if($this->{$actionName.'Controller'}() !== false AND $viewName != null) {
      // pokud je kontrolel v pořádku spustíme view
         $this->view()->{$viewName}();
      }
   }

   /**
    * Metoda přeloží zadaný řetězec
    * @param string $message -- řetězec k přeložení
    * @return string -- přeložený řetězec
    */
   final public function _($message) {
      return $this->locale()->_m($message);
   }

   /**
    * Metoda přeloží zadaný řetězec alias pro _()
    * @param string $message -- řetězec k přeložení
    * @return string -- přeložený řetězec
    */
   final public function _m($message) {
      return $this->locale()->_m($message);
   }
}
?>