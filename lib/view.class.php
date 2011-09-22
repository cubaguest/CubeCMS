<?php
/**
 * Abstraktní třída pro objektu viewru.
 * Třídá slouží jako základ pro tvorbu Viewrů jednotlivých modulů. Poskytuje základní paramtery a metody k vytvoření pohledu modulu.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Abstraktní třída kontroleru modulu
 */

abstract class View extends TrObject {
   /**
    * Objekt pro práci s šablonovacím systémem
    * @var Template
    */
   private $template = null;

   /**
    * Objekt s pro lokalizaci
    * @var Locales
    */
   private $locale = null;

   /**
    * Objekt s odkazem
    * @var Url_Link
    */
   private $link = null;

   /**
    * Objekt c kategorií
    * @var Category
    */
   private $category = null;

   /**
    * Konstruktor Viewu
    *
    * @param Url_Link_Module $link -- objekt odkazů modulu
    * @param Category $category --  objekt kategorie
    */
   function __construct(Url_Link_Module $link, Category_Core $category, Translator $trs) {
      $this->template = new Template_Module($link, $category);
      $this->template->setTranslator($trs);
      $this->link = $link;
      $this->category = $category;
      $this->locale = new Locales($category->getModule()->getName());
      //		inicializace viewru
      $this->init();
   }

   /**
    * Destruktor při vyčištění viewru převede všechny interní proměnné do šablony
    */
   public function  __destruct() {
   }

   /**
    * Magická metoda pro vložení neinicializované proměné do objektu
    * @param string $name -- název proměnné
    * @param mixed $value -- hodnota proměnné
    */
   public function  __set($name, $value) {
      $this->template()->{$name} = $value;
   }

   /**
    * Metoda vraci inicializovanou proměnnou, pokud je
    * @param string $name -- název proměnné
    * @return mixed -- hodnota proměnné
    */
   public function  __get($name) {
      return $this->template()->$name;
   }

   /**
    * Metoda kontroluje jestli byla daná proměnná inicializována
    * @param string $name -- název proměnné
    * @return mixed -- hodnota proměnné
    */
   public function  __isset($name) {
      //return isset($this->viewVars[$name]);
      return isset ($this->template()->{$name});
   }

   /**
    * Metoda maže danou proměnnou z objektu
    * @param string $name -- název proměnné
    */
   public function  __unset($name) {
      if(isset ($this->template()->{$name})){
         unset ($this->template()->{$name});
      }
   }

   /**
    * Metoda, která se provede vždy
    */
   public function init() {}

   /**
    * Hlavní abstraktní třída pro vytvoření pohledu
    */
   public function mainView(){}

   /**
    * Metoda vrací objekt šablony, přes kerý se přiřazují proměnné do šablony
    * @return Template_Module -- objekt šablony
    */
   public function template(){
      return $this->template;
   }

   /**
    * Metoda vrací objekt s kategorií
    * @return Category
    */
   final public function category() {
      return $this->category;
   }

   /**
    * Metoda vrací název modulu
    * @return string
    */
   final public function module() {
      return $this->category()->getModule()->getName();
   }

   /**
    * Metoda vrací objekt k právům uživatele
    * @return Rights -- objekt práv
    */
   final public function rights() {
      return $this->category()->getRights();
   }

   /**
    * Metoda vrací objekt odkazu na  danou stránku (alias pro metodu l())
    * @return Url_link_Module -- objek odkazů
    */
   final public function link() {
      return clone $this->l();
   }

   /**
    * Metoda vrací objekt odkazu na  danou stránku
    * @return Url_link_Module -- objek odkazů
    */
   final public function l() {
      return clone $this->link;
   }

   /**
    * Metoda vrací objekt Locales pro překlady
    * @return Locales -- objek lokalizace
    */
   final public function locale() {
      return $this->locale;
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

   final public function viewSettingsView() {
      $this->template()->addFile('tpl://engine:vsettings.phtml');
      Template_Module::setEdit(true);
   }
   
   final public function viewMetadataView() {
      $this->template()->addFile('tpl://engine:vmetadata.phtml');
      Template_Module::setEdit(true);
   }

   /**
    * Metoda vrací objekt překladatele
    * @return Translator
    */
   public function translator() {
      return $this->template->translator();
   }

   /**
    * Přímá metoda pro překlad
    * @param mixed $str -- řetězec nebo pole pro překlady
    * @param int $count -- (nepovinné) počet, podle kterého se volí překlad
    */
   public function tr($str, $count = 0) {
      return $this->translator()->tr($str, $count);
   }
   
   /**
    * Metody které se využívají často ve viewru
    */
   
   /**
    * Metoda přidá do požadovaného form elementu TinyMCE editor
    * @param Form_Element $formElement -- element kterému se má přidat ditor
    * @param string/Component_TinyMCE_Settings $theme -- typ editoru (none, simple, full, advanced) nebo nastavení editoru
    */
   protected function setTinyMCE($formElement, $theme = 'simple') {
      if( ($formElement instanceof Form_Element) == false OR $theme == 'none'){
         return;
      }
      
      $formElement->html()->addClass("mceEditor_".$theme);
      
      $this->tinyMCE = new Component_TinyMCE();
      if($theme instanceof Component_TinyMCE_Settings){
         $this->tinyMCE->setEditorSettings($theme);
      } else {
         switch ($this->category()->getParam(Articles_Controller::PARAM_EDITOR_TYPE, $theme)) {
            case 'simple':
               $settings = new Component_TinyMCE_Settings_AdvSimple2();
               break;
            case 'full':
               // TinyMCE
               $settings = new Component_TinyMCE_Settings_Full();
               $settings->setSetting('height', '600');
               break;
            case 'advanced':
            default:
               $settings = new Component_TinyMCE_Settings_Advanced();
               $settings->setSetting('height', '600');
               break;
         }
         $settings->setSetting('editor_selector', 'mceEditor_'.$theme);
         $this->tinyMCE->setEditorSettings($settings);
      }
      $this->tinyMCE->mainView();
   }
   
}
?>