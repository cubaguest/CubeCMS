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
    * Objekt se systémovými informacemi o modulu
    * @var Module_Sys
    */
   private $moduleSys = null;

   /**
    * Objekt s šablonou
    * @var Template
    */
   private $viewTemplate = null;

   /**
    * Objekt kontejneru pro přenos dat do pohledu (viewru) a šablony
    * @var Container
    */
   //	private $container = null;

   /**
    * Konstruktor třídy vytvoří a naplní základní vlastnosti pro modul
    *
    * @param Module_Sys $moduleSys -- systémový objekt modulu
    */
   public final function __construct(Module_Sys $moduleSys) {
      //TODO odstranit nepotřebné věci v paramtrech konstruktoru
      //      $this->container = new Container();
      $this->moduleSys = $moduleSys;
      $this->viewTemplate = new Template();
      // donastavení systému do šablony
      $this->viewTemplate->_setSysModule($this->moduleSys);

      //		Načtení třídy View
      $viewClassName = ucfirst($this->module()->getName()).'_View';
      if(!class_exists($viewClassName)){
         throw new BadClassException(sprintf(_('Třída viewru "%s" modulu "%s" neexistuje')
               , $viewClassName, $this->module()->getName()),1);
      }
      //	Vytvoření objektu pohledu
      $this->viewObj = new $viewClassName($this->viewTemplate, $this->sys());

      // nastavení titulku kategorie šablony
      $this->viewTemplate->setCategoryName($this->sys()->module()->getLabel());

      // Inicializace kontroleru modulu
      $this->init();

      /**
       * funkce pro aautomatické načítání modelu
       */
//      function __autoload($classOrigName){
//         $modelFileName = substr($className, 0, strpos(strtolower($classOrigName), 'model'));
//         if (file_exists('.' . DIRECTORY_SEPARATOR . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR
//               . Module::getCurrentModule()->getName() . DIRECTORY_SEPARATOR
//               . AppCore::ENGINE_MODELS_DIR . DIRECTORY_SEPARATOR . $modelFileName . '.php')){
//            require_once ('.' . DIRECTORY_SEPARATOR . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR
//               . Module::getCurrentModule()->getName() . DIRECTORY_SEPARATOR
//               . AppCore::ENGINE_MODELS_DIR . DIRECTORY_SEPARATOR . $modelFileName . '.php');
//         }
//      }
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
   final public function getSysConfig(){
      return AppCore::sysConfig();
   }

   /**
    * Metoda vrací objekt systému modulu
    * @return Module_Sys
    */
   final public function sys() {
      return $this->moduleSys;
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
   final public function getLink($clear = false, $onlyWebRoot = false) {
      return $this->link($clear, $onlyWebRoot);
   }

   /**
    * Metoda vrací odkaz na objekt pro práci s odkazy
    * @param boolean -- true pokud má byt link čistý
    * @param boolean -- true pokud má byt link bez kategorie
    * @return Links -- objekt pro práci s odkazy
    */
   final public function link($clear = false, $onlyWebRoot = false) {
      $link = clone $this->sys()->link();
      if($clear){
         $link->clear();
      }
      if($onlyWebRoot){
         $link->clear()->category();
      }
      return $link;
   }

   /**
    * Metody vrací objekt modulu
    * @return Module -- objekt modulu
    * @deprecated
    */
   final public function getModule() {
      return $this->module();
   }

   /**
    * Metody vrací objekt modulu
    * @return Module -- objekt modulu
    */
   final public function module() {
      return $this->sys()->module();
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
      return $this->sys()->rights()->getAuth();
   }

   /**
    * Metoda vrací objekt na akci
    * @return ModuleAction -- objekt akce
    * @deprecated
    */
   final public function getAction() {
      return $this->action();
   }

   /**
    * Metoda vrací objekt na akci
    * @return ModuleAction -- objekt akce
    */
   final public function action() {
      return $this->sys()->action();
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
      return $this->sys()->rights();
   }

   /**
    * Metoda vrací objekt s článkem
    * @return Article -- objekt článku
    * @deprecated
    */
   final public function getArticle() {
      return $this->article();
   }

   /**
    * Metoda vrací objekt s článkem
    * @return Article -- objekt článku
    */
   final public function article() {
      return $this->sys()->article();
   }

   /**
    * Metoda vrací objekt s cetsami - routes
    * @return Routes -- objekt Rout
    * @deprecated
    */
   final public function getRoutes() {
      return $this->routes();
   }

   /**
    * Metoda vrací objekt s cetsami - routes
    * @return Routes -- objekt Rout
    */
   final public function routes() {
      return $this->sys()->route();
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
      unset ($this->viewTemplate);
   }

   /**
    * Hlavní metoda třídy kontroleru, provádí se pokud není žádná akce ani
    * článek. Musí být implementována v každém modulu
    */
   abstract function mainController();

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
      if(!$this->getRights()->isReadable()){
         $this->errMsg()->addMessage(_("Nemáte dostatčná práva pro přístup ke
kategorii nebo jste byl(a) odhlášen(a)"), true);
         $this->getLink(true)->reload();
      }
   }
   /**
    * Metoda kontroluje práva pro zápis do modulu. v opačném případě vyvolá přesměrování
    */
   final public function checkWritebleRights() {
      if(!$this->getRights()->isWritable()){
         $this->errMsg()->addMessage(_("Nemáte dostatčná práva pro přístup ke
kategorii nebo jste byl(a) odhlášen(a)"), true);
         $this->getLink(true)->reload();
      }
   }
   /**
    * Metoda kontroluje práva pro plný přístup k modulu. v opačném případě vyvolá přesměrování
    */
   final public function checkControllRights() {
      if(!$this->getRights()->isControll()){
         $this->errMsg()->addMessage(_("Nemáte dostatčná práva pro přístup ke
kategorii nebo jste byl(a) odhlášen(a)"), true);
         $this->getLink(true)->reload();
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
      return $this->viewTemplate;
   }

   /**
    * Metoda spustí viewer modulu
    *
    * @param Template -- objetk šablony (odkaz)
    */
   final public function runView($actionName) {
      if($this->actionViewer == null){
         $viewName = $actionName;
      } else {
         $viewName = $this->actionViewer.AppCore::MODULE_VIEWER_SUFIX;
      }
      //			Doplnění sufixu View pro jistotu
      if (strpos($this->actionViewer, AppCore::MODULE_VIEWER_SUFIX) === false) {
         $this->actionViewer .= AppCore::MODULE_VIEWER_SUFIX;
      }

      //	zvolení viewru modulu pokud existuje
      try {
         if(!method_exists($this->view(), $viewName)){//TODO doladit jesli se správně dělají akce
            throw new BadMethodCallException(sprintf(
                  _("Action Viewer \"%s\" v modulu \"%s\" nebyl implementován"),
                  $viewName, $this->getModule()->getName()), 2);
         }
         $this->view()->$viewName();
      } catch (BadMethodCallException $e) {
         new CoreErrors($e);
      }
      unset ($this->viewObj);
   }

   /**
    * Metoda vytvoří objekt modelu
    * @param string $name --  název modelu
    * @return Objekt modelu
    */
   final public function createModel($name) {
      return new $name($this->sys());
   }

   /**
    * Metoda přeloží zadaný řetězec
    * @param string $message -- řetězec k přeložení
    * @return string -- přeložený řetězec
    */
   final public function _($message) {
      return $this->sys()->locale()->_m($message);
   }

   /**
    * Metoda přeloží zadaný řetězec alias pro _()
    * @param string $message -- řetězec k přeložení
    * @return string -- přeložený řetězec
    */
   final public function _m($message) {
      return $this->sys()->locale()->_m($message);
   }
}
?>