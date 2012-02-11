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

abstract class Panel extends TrObject {
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
    * @var Locales
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
    * Objekt s informacemi o panelu
    * @var Panel_Obj
    */
   private $panelObj = null;

   /**
    * Konstruktor
    * @param Category $category -- obejkt kategorie
    * @param Routes $routes -- objekt cest pro daný modul
    */
   function __construct(Category $category, Routes $routes, $template = null, Url_Link $link = null) {
      $this->category = $category;
      $this->panelObj = new Panel_Obj($category->getCatDataObj());
      $this->routes = $routes;
      // locales
      $this->setTranslator(new Translator_Module($category->getModule()->getName()));

      if($link == null){
         $link = new Url_Link_Module();
         $link->setModuleRoutes($routes);
         $link->clear(true)->category($this->category()->getUrlKey());
      }
      $this->link = $link;
      // locales
      $this->locale = new Locales($category->getModule()->getName());
      if($template instanceof Template_Panel) {
         $this->template = $template;
      } else {
         $this->template = new Template_Panel(clone $this->link, $this->category, $this->panelObj);
      }
      $this->template->setTranslator($this->translator());
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
    * @return Panel_Obj
    */
   final public function panelObj() {
      return $this->panelObj;
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
    * @return Locales
    */
   final public function locale() {
      return $this->locale;
   }

   /**
    * Metoda pro vatvoření formuláře pro nastavení modulu a ajeho zpracování
    * @param array $settings -- pole s parametry
    * @return array -- fomulár a pole s parametry
    */
   public static function settingsController(&$settings,Form &$form) {
   }
   
   public function viewSettingsController(){
      $form = new Form('settings_', true);
      $grpBasic = $form->addGroup('basic', $this->tr('Základní nastavení'));
      $grpView = $form->addGroup('view', $this->tr('Nastavení vzhledu'));

      $settings = $this->panelObj()->getParams();

      $settings['_module'] = $this->category()->getModule()->getName();

      if(method_exists($this, 'settings')){
         $this->settings($settings, $form);
      } else if(method_exists(ucfirst($this->category()->getModule()->getName()).'_Panel','settingsController')) {
         Debug::log("settings - static");
         $func = array(ucfirst($this->category()->getModule()->getName()).'_Panel','settingsController');
         call_user_func_array($func, array(&$settings, &$form));
      }

      unset($settings['_module']);

      
      /* BUTTONS SAVE AND CANCEL */
      $submitButton = new Form_Element_SaveCancel('send');
      $form->addElement($submitButton);

      if($form->isSend() AND $form->send->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }
      
      if($form->isValid()){
         $this->panelObj()->setParams($settings);
         $this->infoMsg()->addMessage($this->tr('Nastavení bylo uloženo'));
         $this->link()->route()->reload();
      }

      $this->template()->form = $form;
   }
}
?>