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

abstract class Controller extends TrObject {
   const SETTINGS_GROUP_BASE = 'basic';
   const SETTINGS_GROUP_VIEW = 'view';
   const SETTINGS_GROUP_TEMPLATES = 'tpls';
   const SETTINGS_GROUP_SOCIAL = 'social';
   const SETTINGS_GROUP_IMAGES = 'images';
   const SETTINGS_GROUP_SHOP = 'shop';
   
   const METADATA_GROUP_BASE = 'basic';
   const METADATA_GROUP_SITEMAP = 'sitemap';
   const METADATA_GROUP_OTHER = 'other';
   const METADATA_GROUP_SEO = 'seo';
   const METADATA_GROUP_SHOP = 'seo';
   
   
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
    * @var Locales
    */
   public $locale = null;

   /**
    * Název lokální domény pro locale
    * @var string
    */
   private $localeDomain = null;

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
    * Natavení kontroleru
    * @var array
    */
   protected $options = array();

   /**
    * Název modulu -- lze použít pro načítání knihoven atd (např. Articles)
    * @var string
    */
   protected $moduleName = null;

   /**
    * Objekt odpovědi na ajax požadavek
    * @var Ajax_Data_Respond
    */
   private $moduleRespond = null;

   /**
    * Moduly, které se mají připojit ke stávajícímu modulu.
    * @var array 
    */
   protected $registeredModules = array();

   /**
    * Pole s popisem akcí - pro překlady
    * @var array
    */
   protected $actionsLabels = array();

   /**
    * Konstruktor třídy vytvoří a naplní základní vlastnosti pro modul
    *
    * @param Category_Core/Controller $base -- objekt kategorie nebo kontroleru
    * @param Routes $routes -- objekt cest pro daný modul
    */
   public final function __construct($base, Routes $routes = null, View $view = null, Url_Link_Module $link = null) 
   {
      //TODO odstranit nepotřebné věci v paramtrech konstruktoru
      if($base instanceof Controller) {
         $link = $link == null ? $base->link() : $link;
         $view = $view == null ? $base->view() : $view;
         $routes = $routes == null ? $base->routes() : $routes;
         // merge config
         $this->options = array_merge($this->options, $base->getOptions());
         $base = $base->category();
      }
      
      $this->category = $base;
      $this->routes = $routes;
      $this->actionViewer = $routes->getActionName();
      
      // název modulu
      $className = get_class($this);
      $this->moduleName = substr($className, 0, strpos($className,'_'));

      if($link == null){
         $link = new Url_Link_Module();
         $link->setModuleRoutes($routes);
         $link->category(Url_Request::getInstance()->getCategoryUrlKey($this->category()->getUrlKey(), $this->category()->getId()));
      }
      $this->link = $link;
      
      // kontrola oprávnění
      $this->checkReadableRights();
      
      // pokud se jedná o zděděný kontroler tak načíst všechny překlady děděných modulů
      $class = get_class($this);
      $modulesTrs = array();
      while ($class != 'Controller') {
         $modulesTrs[] = strtolower( substr($class, 0, -11)); // remove _Controller word
         $class = get_parent_class($class);
      }
      $modulesTrs = array_reverse($modulesTrs);
      
      foreach ($modulesTrs as $m) {
         if($this->translator instanceof Translator_Module){
            $this->translator()->apppendDomain($m);
         } else {
            $this->setTranslator(new Translator_Module($m));
         }
      }
            
      $this->localeDomain = strtolower($this->moduleName);
      $this->locale = new Locales(strtolower($this->moduleName));
      
      //	Vytvoření objektu pohledu
      if($view !== null){
         $this->viewObj = $view;
      } else {
         $this->initView();
      }

      // donačtení šablon modulu
      $this->module()->loadTemplates();
      
      // Inicializace kontroleru modulu
      $this->init();
   }

   private function initView() 
   {
      //	Načtení třídy View
      $viewClassName = ucfirst($this->moduleName).'_View';
      $this->viewObj = new $viewClassName(clone $this->link(), $this->category(), $this->translator());
   }

   /**
    * Inicializační metoda pro kontroler. je spuštěna vždy při vytvoření objektu
    * kontroleru
    */
   protected function init() {}

   /**
    * Metoda registruje modul, který se má použít pro obsluhu metod, které nemá modul registrovány a ani jej nelze dědit
    * @param string $module -- název modulu
    * @param string $params -- název metody, která provede test jestli má být použit daný modul
    */
   final public function registerModule($module, $testMetod = null)
   {
      $this->registeredModules[$module] = $testMetod;
   }

   /**
    * Metoda vrací objekt viewru modulu
    * @return View
    */
   final public function view() 
   {
      return $this->viewObj;
   }

   /**
    * Metoda vrací konfigurační volbu
    * @param string $name -- název volby
    * @param mixed $defaultValue -- výchozí hodnota
    * @return mixed -- obsah volby
    */
   final public function getOption($name, $defaultValue = null) 
   {
      if(isset ($this->options[$name])) {
         return $this->options[$name];
      }
      return $defaultValue;
   }
   
   /**
    * Metoda vrací konfigurační volby
    * @return array -- obsah voleb
    */
   final public function getOptions() 
   {
      return $this->options;
   }

   /**
    * Metoda nasatvuje konfigurační volbu
    * @param string $name -- název volby
    * @param mixed $value -- výchozí hodnota
    * @return mixed -- obsah volby
    */
   final public function setOption($name, $value = null) 
   {
      $this->options[$name] = $value;
   }
   
   /**
    * Metoda nasatvuje konfigurační volbu
    * @param string $name -- název volby
    * @param mixed $value -- výchozí hodnota
    * @return mixed -- obsah volby
    */
   final public function setOptions($opts) 
   {
      $this->options = $opts;
   }

   /**
    * Metoda vrací odkaz na objekt pro práci s odkazy
    * @param boolean -- true pokud má byt link čistý
    * @param boolean -- true pokud má byt link bez kategorie
    * @return Url_Link_Module -- objekt pro práci s odkazy
    */
   final public function link($clear = false) 
   {
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
   final public function getModule() 
   {
      return $this->category()->getModule();
   }

   /**
    * Metody vrací objekt modulu
    * @return Module -- objekt modulu
    */
   final public function module() 
   {
      return $this->category()->getModule();
   }

   /**
    * Metoda vrací objekt kategorie
    * @return Category
    */
   final public function category() 
   {
      return $this->category;
   }

   /**
    * Metoda vrací objekt cest
    * @return Routes
    */
   final public function routes()
   {
      return $this->routes;
   }

   /**
    * Metoda vrací parametry požadavku vybrané z URL
    * @param string $name -- název parametru
    * @param mixed $def -- výchozí hodnota vrácená pokud nebyl parametr přenesen
    * @return mixed -- hodnota parametru
    */
   final public function getRequest($name, $def = null) 
   {
      return $this->routes()->getRouteParam($name, $def);
   }

   /**
    * Metoda vrací parametr předaný v url za cestou
    * @param string $name -- název parametru
    * @param mixed $def -- výchozí hodnota vrácená pokud nebyl parametr přenesen
    * @return mixed -- hodnota parametru
    * @todo dodělat rekurzivní kontrolu
    */
   final public function getRequestParam($name, $def = null) 
   {
      if(isset ($_REQUEST[$name]) AND !is_array($_REQUEST[$name])) {
         return rawurldecode($_REQUEST[$name]);
      } else if(isset ($_REQUEST[$name])) {
         return $_REQUEST[$name];
      } else {
         return $def;
      }
   }

   /**
    * Metoda vrací objekt lokalizace
    * @return Locales
    */
   final public function locale() 
   {
      return $this->locale;
   }

   /**
    * Metoda vraci lokální doménu pro daný modul
    * @return string -- název domény
    * <p>Vhodné při dědění modulů, pro zadávání nových překladových textů</p>
    */
   final public function getLocaleDomain()
   {
      return $this->localeDomain;
   }

   /**
    * Metoda vrací objekt s právy na modul
    * @return Rights -- objekt práv
    * @deprecated
    */
   final public function getRights() 
   {
      return $this->rights();
   }

   /**
    * Metoda vrací objekt s právy na modul
    * @return Rights -- objekt práv
    */
   final public function rights() 
   {
      return $this->category()->getRights();
   }

   /**
    * Metoda vrací objekt s informačními zprávami
    * @return Messages -- objekt zpráv
    */
   final public function infoMsg() 
   {
      return AppCore::getInfoMessages();
   }

   /**
    * Metoda vrací objekt s chybovými zprávami
    * @return Messages -- objekt zpráv
    */
   final public function errMsg() 
   {
      return AppCore::getUserErrors();
   }

   /**
    * Metoda zaloguje událost
    * @param string  $msg -- Zpráva (nemusí mít překlad)
    */
   final public function log($msg) 
   {
      Log_Module::msg($msg, $this->module()->getName(), $this->category()->getLabel());
   }

   /**
    * Metoda volaná při destrukci objektu
    */
   function __destruct() 
   {
   }

   /**
    * Metoda změní výchozí actionViewer pro danou akci na zadaný viewer
    * @param string -- název actionViewru
    * @deprecated používat setView()
    */
   final public function changeActionView($newActionView) 
   {
      $this->actionViewer = $newActionView;
   }

   /**
    * Metoda vrací zvolený actionViewer nebo false pokud je nulový
    * @return string -- název nového actionViewru
    */
   final public function getActionView() 
   {
      return $this->actionViewer;
   }

   /**
    * Metoda kontroluje práva pro čtení modulu. v opačném případě vyvolá přesměrování
    */
   final public function checkReadableRights()
   {
      if(!$this->rights()->isReadable()) {
//         $this->errMsg()->addMessage(sprintf(
//            $this->tr("Nemáte dostatčná práva pro přístup ke kategorii \"%s\" nebo jste byl(a) odhlášen(a)"),
//            $this->category()->getName()), true);
         throw new UnauthorizedAccessException();
//         $this->link(true)->clear(true)->reload(null, 401);
      }
   }
   /**
    * Metoda kontroluje práva pro zápis do modulu. v opačném případě vyvolá přesměrování
    */
   final public function checkWritebleRights()
   {
      if(!$this->rights()->isWritable()) {
//         $this->errMsg()->addMessage(sprintf(
//            $this->tr("Nemáte dostatčná práva pro přístup ke kategorii \"%s\" nebo jste byl(a) odhlášen(a)"),
//            $this->category()->getName()), true);
//         $this->link(true)->clear(true)->reload();
         throw new UnauthorizedAccessException();
      }
   }
   /**
    * Metoda kontroluje práva pro plný přístup k modulu. v opačném případě vyvolá přesměrování
    */
   final public function checkControllRights()
   {
      if(!$this->rights()->isControll()) {
//         $this->errMsg()->addMessage(sprintf(
//            $this->tr("Nemáte dostatčná práva pro přístup ke kategorii \"%s\" nebo jste byl(a) odhlášen(a)"),
//            $this->category()->getName()), true);
//         $this->link(true)->clear(true)->reload();
         throw new UnauthorizedAccessException();
      }
   }

   /**
    * Metoda nastaví název použitého viewru (bez sufixu View)
    *
    * @param string -- název viewru
    */
   final public function setView($view) 
   {
      $this->actionViewer = $view;
   }

   /*
    * @todo -- dořešit jestli je třeba několika objektů šablon
    */
   /**
    * Metoda vrací objekt šablony
    * @return Template
    */
   final public function _getTemplateObj() 
   {
      return $this->view()->template();
   }

   /**
    * Metoda vrací pole s šablonami
    * @return array
    */
   final public function _getTemplateObjs() 
   {
      return $this->view()->template();
   }

   /**
    * Metoda spustí metodu kontroleru a viewer modulu
    */
   final public function runCtrl() 
   {
      $ctrlResult = true;
      $ctrlAct = $this->actionViewer.'Controller';
      // spuštění kontroleru
      if(method_exists($this, $ctrlAct)) {
         $reflect = new ReflectionClass($this);
         $methodParametres = $reflect->getMethod($ctrlAct)->getParameters();
         $requestParams = $this->routes()->getRouteParams();
         $transmitParams = array();
         // vytvoření parametrů pro předání do kontroleru
         foreach ($methodParametres as $param) {
            $transmitParams[$param->getName()] = isset($requestParams[$param->getName()]) ? $requestParams[$param->getName()] : null;
         }
         // spuštění kontroleru
         $result = call_user_func_array(array($this, $ctrlAct), $transmitParams);
         if($result === false){
            // backward compatibility
            throw new UnexpectedPageException();
         }
         $this->view()->runView($this->actionViewer, Template_Output::getOutputType());
      } else if(!empty ($this->registeredModules)) { // pokus spustit kontroller z jiného modulu
         foreach ($this->registeredModules as $module => $params) {
            $ctrlName = ucfirst($module).'_Controller';
            $viewName = ucfirst($module).'_View';
            if(method_exists($ctrlName, $ctrlAct)){
               // new Crtl
               $ctrl = new $ctrlName($this, $this->routes(), new $viewName($this));
               // pre callback funkce pro spuštění před spuštění externího modulu
               if($this->callRegisteredModule($ctrl, $module, $this->actionViewer) === false){
                  AppCore::setErrorPage(true);
                  return;
               }
               $ctrlResult = $ctrl->runCtrl();
               $this->viewObj = $ctrl->view();
               break;
            }
         }
      } else {
         throw new BadMethodCallException(sprintf($this->tr('neimplementovaná akce "%sController" v kontroleru modulu "%s"'),
         $this->routes()->getActionName(), $this->module()->getName()), 1);
      }
   }
   
   protected function callRegisteredModule(Controller $ctrl, $module, $action) 
   {}

   /**
    * Metoda vrací objekt požadavku (pouze pro ajax požadavky a požadavky na moduly)
    * @return Ajax_Data_Respond
    */
   final public function respond()
   {
      return $this->moduleRespond;
   }

   /**
    * Metoda přeloží zadaný řetězec
    * @param string $message -- řetězec k přeložení
    * @return string -- přeložený řetězec
    * @deprecated use $this->tr()
    */
   final public function _($message, $domain = null) 
   {
      return $this->tr($message);
//      return $this->locale()->_($message, $domain = null);
   }

   /**
    * Metoda přeloží zadaný řetězec v plurálu
    * @param string $message1 -- řetězec k přeložení - jč
    * @param string $message2 -- řetězec k přeložení - plurál
    * @param integer $int -- počet
    * @return string -- přeložený řetězec
    * @deprecated use $this->tr()
    */
   final public function ngettext($message1, $message2, $int, $domain = null) 
   {
      return $this->tr(array($message1,$message2,$message2), $int);
//      return $this->locale()->ngettext($message1, $message2, $int, $domain);
   }

   /**
    * Metoda implementující mazání při odstranění kategori. Je určena k vičištění.
    * @param Category $category -- objekt kategorie
    */
   public static function clearOnRemove(Category $category) 
   {}

   public function viewSettingsController() 
   {
      $this->checkControllRights();

      $form = new Form('settings_', true);
      $grpBasic = $form->addGroup(self::SETTINGS_GROUP_BASE, $this->tr('Základní nastavení'));
      $grpView = $form->addGroup(self::SETTINGS_GROUP_VIEW, $this->tr('Nastavení vzhledu'));
      $grpTemplates = $form->addGroup(self::SETTINGS_GROUP_TEMPLATES, $this->tr('Nastavení šablon stránek'));
      $grpSocial = $form->addGroup(self::SETTINGS_GROUP_SOCIAL, $this->tr('Sociální obsah'));
      $grpImages = $form->addGroup(self::SETTINGS_GROUP_IMAGES, $this->tr('Obrázky'));


      if($this->category()->getCatDataObj()->{Model_Category::COLUMN_PARAMS}!= null){
         $settings = unserialize($this->category()->getCatDataObj()->{Model_Category::COLUMN_PARAMS});
      } else {
         $settings = array();
      }

      $settings['_module'] = $this->category()->getModule()->getName();

      if(method_exists($this, 'settings')){
         $this->settings($settings, $form);
      } else if(method_exists(ucfirst($this->category()->getModule()->getName()).'_Controller','settingsController')) {
         $func = array(ucfirst($this->category()->getModule()->getName()).'_Controller','settingsController');
         call_user_func_array($func, array(&$settings, &$form));
      }

      unset($settings['_module']);

      // nastavení šablon
      $tplElements = array();
      $templates = $this->module()->getAllTemplates();
      foreach ($templates as $action => $tpls) {
         if($tpls /*&& sizeof($tpls) > 1*/){ // má smysl zobrazovat výběr z jedné šablony?
            $label = isset($this->actionsLabels[$action]) ? $this->actionsLabels[$action] : $action;
            $name = 'tpl_action_'.$action;
            $tplSelect = new Form_Element_Select($name, $this->tr('Šablona pro stránku ').'"'.$label.'"');
            foreach ($tpls as $file => $arr) {
               $tplSelect->addOption($arr['name'], $file);
            }
            if(isset($settings[$name])){
               $tplSelect->setValues($settings[$name]);
            }
            $tplElements[] = $name;
            $form->addElement($tplSelect, $grpTemplates);
         }
      }
      
      // ostatní nastavení
      if(Face::getCurrent()->getParam('category_title_image', null, true)){
         $elemImage = new Form_Element_ImageSelector('image', $this->tr('Titulní obrázek'));
         $elemImage->setUploadDir(Category::getImageDir(Category::DIR_IMAGE, true));
         $elemImage->setValues($this->category()->getCatDataObj()->{Model_Category::COLUMN_IMAGE});
         $form->addElement($elemImage, $grpImages);
      }
      
      if(Face::getCurrent()->getParam('category_bg_image', null, true)){
         $elemBg = new Form_Element_ImageSelector('background', $this->tr('Přiřazené pozadí'));
         $elemBg->setUploadDir(Category::getImageDir(Category::DIR_BACKGROUND, true));
         $elemBg->setValues($this->category()->getCatDataObj()->{Model_Category::COLUMN_BACKGROUND});
         $form->addElement($elemBg, $grpImages);
      }
      
      /* nástroje pro sdílení */
      $elemShareTools = new Form_Element_Checkbox('shareTools', $this->tr('Nástroje pro sdílení obsahu'));
      $elemShareTools->setSubLabel($this->tr('Zapnutí nástrojů pro sdílení obsahu na sociálních sítích.'));
      $elemShareTools->setValues(true);
      if(isset($settings['shareTools'])){
         $elemShareTools->setValues($settings['shareTools']);
      }
      $form->addElement($elemShareTools, $grpView);
      
      if(function_exists('extendCategorySettings')){
         extendCategorySettings($this->category, $form, $settings, $this->translator);
      }
      
      /* BUTTONS SAVE AND CANCEL */
      $submitButton = new Form_Element_SaveCancel('send');
      $form->addElement($submitButton);

      if($form->isSend() AND $form->send->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }
      
      if($form->isValid()){
         $settings['shareTools'] = $form->shareTools->getValues();
         
         // čištění nulových hodnot
         foreach ($settings as $key => $option){
            if($option === null OR $option === ''){
               unset($settings[$key]);
            }
         }

         $categoryM = new Model_Category();
         $catRec = $categoryM->record($this->category()->getId());
         
         // titulní obrázek
         if(isset($form->image)){
            $image = $form->image->getValues();
            $catRec->{Model_Category::COLUMN_IMAGE} = $image ? $image['name'] : "";
         }

         // background
         if(isset($form->background)){
            $bg = $form->background->getValues();
            $catRec->{Model_Category::COLUMN_BACKGROUND} = $bg ? $bg['name'] : "";
         }
         
         // uložení šablon
         foreach ($tplElements as $eName) {
            if(isset($form->$eName)){
               $settings[$eName] = $form->$eName->getValues();
            }
         }
         
         /* serializace volitelných parametrů */
         $catRec->{Model_Category::COLUMN_PARAMS} = serialize($settings);

         $categoryM->save($catRec);
         $this->infoMsg()->addMessage($this->tr('Nastavení bylo uloženo'));
         $this->log('Upraveno nastavení kategorie "'.$this->category()->getName().'"');
         $this->link()->route()->reload();
      }

      $this->view()->form = $form;

   }
   
   public function viewMetadataController() 
   {
      $this->checkControllRights();
      
      $catModel = new Model_Category();
      $cat = $catModel->record($this->category()->getId());
      
      if($this->category()->getCatDataObj()->{Model_Category::COLUMN_PARAMS}!= null){
         $settings = unserialize($this->category()->getCatDataObj()->{Model_Category::COLUMN_PARAMS});
      } else {
         $settings = array();
      }
      
      $form = new Form('metadata_', true);
      $grpBasic = $form->addGroup(self::METADATA_GROUP_BASE, $this->tr('Základní'), $this->tr('Základní popisky'));
      $grpSeo = $form->addGroup(self::METADATA_GROUP_SITEMAP, $this->tr('SEO nastavení'), $this->tr('SEO nastavení kateogire'));
      $grpSitemap = $form->addGroup(self::METADATA_GROUP_SITEMAP, $this->tr('Mapa stránek'), $this->tr('Nastavení mapy stránek pro vyhledávače'));
      $grpOther = $form->addGroup(self::METADATA_GROUP_OTHER, $this->tr('Ostatní'), $this->tr('Ostatní metadata kategorie'));
      $grpShop = $form->addGroup(self::METADATA_GROUP_SHOP, $this->tr('Nastavení obchodu'), $this->tr('Nastavení obchodu a exportu kateogire'));
      
      $eName = new Form_Element_Text('name', $this->tr('Název kategorie'));
      $eName->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      $eName->setLangs();
      $eName->setValues($cat->{Model_Category::COLUMN_NAME});
      $form->addElement($eName, $grpBasic);
      
      // popisek kategorie
      $catAlt = new Form_Element_Text('alt', $this->tr('Alternativní název'));
      $catAlt->setLangs();
      $catAlt->setSubLabel($this->tr('Bývá využit u názvů obrázků kategorie či jako delší název kategorie.'));
      $catAlt->setValues($cat->{Model_Category::COLUMN_ALT});
      $form->addElement($catAlt, $grpBasic);
      
      $eKeywords = new Form_Element_Text('keywords', $this->tr('Klíčová slova'));
      $eKeywords->setLangs();
      $eKeywords->setSubLabel($this->tr('Klíčová slova, které kategorii nejlépe vystihují oddělené mezerou nebo čárkou.'));
      $eKeywords->setValues($cat->{Model_Category::COLUMN_KEYWORDS});
//      $eKeywords->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($eKeywords, $grpSeo);
      
      $eDesc = new Form_Element_TextArea('desc', $this->tr('Popis kategorie'));
      $eDesc->setLangs();
      $eDesc->setSubLabel($this->tr('Krátký popisek kategorie. (Používá jej například Google u krátkého textu ve výsledcích hledání.)'));
      $eDesc->setValues($cat->{Model_Category::COLUMN_DESCRIPTION});
//      $eDesc->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($eDesc, $grpSeo);
      
      if(Auth::isAdmin()){
         $ePrior = new Form_Element_Text('priority', $this->tr('Priorita ve struktuře'));
         $ePrior->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
         $ePrior->setSubLabel('Čím větší tím bude větší šance, že kategorie bude vybrána jako výchozí s ohledem na oprávnění uživatele.');
         $ePrior->setValues($cat->{Model_Category::COLUMN_PRIORITY});
         $form->addElement($ePrior, $grpOther);
      }
      
      
//      frekvence změny
      $freqOptions = array($this->tr('Vždy') => 'always', $this->tr('každou hodinu') => 'hourly',
         $this->tr('Denně') => 'daily', $this->tr('Týdně') => 'weekly', $this->tr('Měsíčně') => 'monthly',
         $this->tr('Ročně') => 'yearly', $this->tr('Nikdy') => 'never');
      $catSitemapChangeFrequency = new Form_Element_Select('sitemap_frequency', $this->tr('Frekvence změn'));
      $catSitemapChangeFrequency->setOptions($freqOptions);
      $catSitemapChangeFrequency->setSubLabel($this->tr('Jak často se aktualizuje obsah kategorie.'));
      
      $catSitemapChangeFrequency->setValues($cat->{Model_Category::COLUMN_SITEMAP_CHANGE_FREQ});
      $form->addElement($catSitemapChangeFrequency, $grpSitemap);
      
      $eSitemapPrior = new Form_Element_Text('sitemap_priority', $this->tr('Priorita'));
      $eSitemapPrior->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_FLOAT));
      $eSitemapPrior->addValidation(new Form_Validator_NotEmpty());
      $eSitemapPrior->setSubLabel('0 - 1, čím větší, tím bude kategorie označena jako důležitější.');
      $eSitemapPrior->setValues($cat->{Model_Category::COLUMN_SITEMAP_CHANGE_PRIORITY});
      $form->addElement($eSitemapPrior, $grpSitemap);

      
      if(defined('CUBE_CMS_SHOP') && CUBE_CMS_SHOP && $this instanceof Shop_Product_Controller){
         $eHeurekaCat = new Form_Element_Text('heureka_cat', $this->tr('Heureka kategorie'));
         $eHeurekaCat->setSubLabel($this->tr('Řetezec kateogrie pro heureku'));
         $eHeurekaCat->setValues($cat->getParam('heureka_cat'));
         $form->addElement($eHeurekaCat, $grpShop);
         
         $eGoogleCat = new Form_Element_Text('google_cat', $this->tr('Google kategorie'));
         $eGoogleCat->setSubLabel($this->tr('Řetezec kateogrie pro Google'));
         $eGoogleCat->setValues($cat->getParam('google_cat'));
         $form->addElement($eGoogleCat, $grpShop);
         
         $eZboziCat = new Form_Element_Text('zbozi_cat', $this->tr('Zboží.cz kategorie'));
         $eZboziCat->setSubLabel($this->tr('Řetezec kateogriše pro Zboží.cz'));
         $eZboziCat->setValues($cat->getParam('zbozi_cat'));
         $form->addElement($eZboziCat, $grpShop);
         
      }
      
      if(function_exists('extendCategoryMetadata')){
         extendCategoryMetadata($this->category, $form, $this->translator);
      }
      
      $eSave = new Form_Element_SaveCancel('save');
      $form->addElement($eSave);
      
      if($form->isSend()){
         if($form->save->getValues() == false){
            $this->link()->route()->reload();
         }
         if((float)$form->sitemap_priority->getValues() < 0 || (float)$form->sitemap_priority->getValues() > 1){
            $eSitemapPrior->setError($this->tr('Zadaná hodnota není v požadovaném rozsahu 0 až 1'));
         }
      }
      
      if($form->isValid()){
         $cat->{Model_Category::COLUMN_NAME} = $form->name->getValues();
         $cat->{Model_Category::COLUMN_ALT} = $form->alt->getValues();
         $cat->{Model_Category::COLUMN_KEYWORDS} = $form->keywords->getValues();
         $cat->{Model_Category::COLUMN_DESCRIPTION} = $form->desc->getValues();
         if(Auth::isAdmin()){
            $cat->{Model_Category::COLUMN_PRIORITY} = $form->priority->getValues();
         }
         $cat->{Model_Category::COLUMN_SITEMAP_CHANGE_FREQ} = $form->sitemap_frequency->getValues();
         $cat->{Model_Category::COLUMN_SITEMAP_CHANGE_PRIORITY} = $form->sitemap_priority->getValues();
         if(isset($form->heureka_cat)){
            $settings['heureka_cat'] = $form->heureka_cat->getValues();
         }
         if(isset($form->google_cat)){
            $settings['google_cat'] = $form->google_cat->getValues();
         }
         if(isset($form->zbozi_cat)){
            $settings['zbozi_cat'] = $form->zbozi_cat->getValues();
         }
            
         $cat->{Model_Category::COLUMN_SITEMAP_CHANGE_PRIORITY} = $form->sitemap_priority->getValues();
         
         $cat->{Model_Category::COLUMN_PARAMS} = serialize($settings);
         
         $catModel->save($cat);
         $this->infoMsg()->addMessage($this->tr('Metadata byla uložena'));
         $this->link()->route()->reload();
      }
      $this->view()->form = $form;
   }

   public static function categoryDuplicate(Category_Core $oldCat, Category_Core $newCat) {}
}