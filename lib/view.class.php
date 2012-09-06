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

   private $actionName = 'main';

   /**
    * Konstruktor Viewu
    *
    * @param Url_Link_Module/Controller $base -- objekt odkazů modulu nebo kontroler (pak již nění nutné předávat ostatní parametry)
    * @param Category_Core $category --  objekt kategorie
    * @param Translator $category --  objekt kategorie
    */
   function __construct($base, Category_Core $category = null, Translator $trs = null) {
      if($base instanceof Controller){
         $this->link = $base->link();
         $this->category = $base->category();
         $trs = $base->translator();
         $this->template = $base->view()->template();
      } elseif ($base instanceof Url_Link_Module) {
         $this->link = $base;
         $this->category = $category;
         $this->template = new Template_Module($this->link, $this->category);
         $this->template->setTranslator($trs);
      }
      
      $this->locale = new Locales($this->category->getModule()->getName());
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

   final public function runView($actionName, $output)
   {
      $this->actionName = $actionName;
      $viewName = null;
      //	zvolení viewru modulu pokud existuje
      if(method_exists($this, $actionName.ucfirst($output).'View')) {
         $viewName = $actionName.ucfirst($output).'View';
      } else if(method_exists($this, $actionName.'View')) {
         $viewName = $actionName.'View';
      } 
      else {
         return;
      }
      
      $variables = $this->template()->getTemplateVars();
      foreach ($variables as $var){
         if($var instanceof Component){
            $var->mainController();
         }
      }
      $this->{$viewName}();
      if ($this->actionName == "main" AND $this->category()->getRights()->isControll() ) {
         $this->addBaseToolBox();
      }
   }

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
      Template_Navigation::addItem($this->tr('Nastavení'), $this->link());
   }
   
   final public function viewMetadataView() {
      $this->template()->addFile('tpl://engine:vmetadata.phtml');
      Template_Module::setEdit(true);
      Template_Navigation::addItem($this->tr('Úprava metadat'), $this->link());
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
   protected function setTinyMCE($formElement, $theme = 'simple', $editorSettings = array()) {
      if( ($formElement instanceof Form_Element) == false OR (is_string($theme) && $theme == 'none' ) ){
         return;
      }
      
      $this->tinyMCE = new Component_TinyMCE();
      if($theme instanceof Component_TinyMCE_Settings){
         $formElement->html()->addClass("mceEditor_".$theme->getThemeName());
         $theme->setSetting('editor_selector', 'mceEditor_'.$theme->getThemeName());
         $this->tinyMCE->setEditorSettings($theme);
      } else {
         $formElement->html()->addClass("mceEditor_".$theme);
         switch ($theme) {
            case 'simple':
               $settings = new Component_TinyMCE_Settings_AdvSimple();
               break;
            case 'simple2':
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
         if(!empty ($editorSettings)){
            foreach ($editorSettings as $name => $value) {
               $settings->setSetting($name, $value);
            }
         }
         if(!$this->category()->getRights()->isControll() OR !Auth::isLogin()){
            $this->tinyMCE->setConfig(Component_TinyMCE::CFG_ALLOW_INTERNAL_SOURCES, false);
            $this->tinyMCE->setConfig(Component_TinyMCE::CFG_ALLOW_INTERNAL_TPLS, false);
         }
         
         $this->tinyMCE->setEditorSettings($settings);
      }
      $this->tinyMCE->mainView();
   }
 
   final public function addBaseToolBox()
   {
      // pokud je hlavní pohled přidáme toolboxy s nastavením (modí být vložen)
      if ($this->category()->getRights()->isControll() AND (Category::getSelectedCategory() instanceof Category_Admin) == false) {
         if(($this->template()->toolbox instanceof Template_Toolbox2) == false){
            $this->template()->toolbox = new Template_Toolbox2();
         }
         
         // pokud není vložen nástroj pro nastavení
         if ($this->template()->toolbox->edit_view) {
            unset($this->template()->toolbox->edit_view);
         }
         $this->template()->toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
         if(!isset ($this->template()->toolbox->edit_view)){
            $toolEView = new Template_Toolbox2_Tool_PostRedirect('edit_view', $this->tr("Nastavení"),
               $this->link()->route(Routes::MODULE_SETTINGS));
            $toolEView->setIcon(Template_Toolbox2::ICON_WRENCH)->setTitle($this->tr('Upravit nastavení kategorie'));
            $this->template()->toolbox->addTool($toolEView);
         }
         if(!isset ($this->template()->toolbox->edit_metadata)){
            $toolEMetaData = new Template_Toolbox2_Tool_PostRedirect('edit_metadata', $this->tr("Metadata"),
               $this->link()->route(Routes::MODULE_METADATA));
            $toolEMetaData->setIcon(Template_Toolbox2::ICON_PEN)->setTitle($this->tr('Upravit metadata kategorie'));
            $this->template()->toolbox->addTool($toolEMetaData);
         }
      }
   }
   
}
?>