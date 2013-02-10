<?php
/**
 * Třída pro vytvoření požadavku pro naplnění jqgrid
 */
abstract class Component_TinyMCE_Settings extends TrObject implements ArrayAccess {
   const SETTING_EXTERNAL_TPL_LIST = 'template_external_list_url';
   const SETTING_EXTERNAL_LINK_LIST = 'external_link_list_url';
   const SETTING_EXTERNAL_IMAGE_LIST = 'external_image_list_url';
   const SETTING_EXTERNAL_MEDIA_LIST = 'external_media_list_url';

   protected $settingName = 'simple';

   protected $defaultSettings = array(
      'theme' => 'simple',
      'mode' => "specific_textareas",
      'skin' => "o2k7",
      'skin_variant' => "silver",
      'editor_selector' => 'mceEditor',
      'root_element' => true,
      'relative_urls' => true,
      'height' => 'auto',
      'language' => 'en',
      'width' => "100%",
      'content_css' => null,
      'document_base_url' => null
   );
   protected $settings = array();

   protected $defaultPlugins = array();
   protected $plugins = array();

   protected $defaultButtons = array();
   protected $buttons = array();

   public function  __construct() {
      $this->setDefaultValues();
      $this->settings['language'] = Locales::getLang();
   }
   
   protected function setDefaultValues()
   {
      $this->settings = $this->defaultSettings;
      $this->buttons = $this->defaultButtons;
      $this->plugins = $this->defaultPlugins;
   }

   final public function getSetting($name) 
   {
      return $this->settings[$name];
   }

   /**
    * Metoda vrací název theme
    * @return string
    */
   final public function getThemeName() 
   {
      return $this->settingName;
   }
   
   /**
    * Metoda nasatvuje parametr editoru
    * @param string $name -- název parametru
    * @param mixed $value -- hodnota parametru
    * @return Component_TinyMCE_Settings
    */
   final public function setSetting($name, $value) 
   {
      $this->settings[$name] = $value;
      return $this;
   }

   final public function getParamsForUrl() 
   {
      $urlParams = array();
      $urlParams['set'] = $this->settingName;

      // kontrola pluginu
      $additional = array();
      if($this->defaultPlugins != $this->plugins){ // pokud byl upraven plugins
         $additional['plugins'] = $this->plugins;
      }

      // kontrola buttonů
      if(!empty ($this->buttons)){ // pokud byly upraveny buttons
         foreach ($this->buttons as $row => $buttons) {
            if($buttons != $this->defaultButtons[$row]){ // byl upraven řádek
               $additional['buttons'][$row] = $buttons;
            }
         }
      }
      
      // create diferents
      $tmp = array();
      foreach ($this->defaultSettings as $key => $value) {
         if(isset($this->settings[$key]) && $this->defaultSettings[$key] != $this->settings[$key]){
            $tmp[$key] = $this->settings[$key];
         }
      }
      return array_merge($urlParams, $tmp, $additional);
   }

   /**
    * metoda vrací vybrané pluginy
    * @return array -- pol epluginů
    */
   public function getPlugins() 
   {
      return $this->plugins;
   }

   /**
    * Metoda nastavuje pluginy
    * @param array $pluginsarr -- pole pluginů
    */
   public function setPlugins($pluginsarr) 
   {
      $this->plugins = $pluginsarr;
   }

   /**
    * Metoda vrací tlačítka v zadanám řádku
    * @param int $row -- číslo řádku
    */
   public function getButtons($row = 1) 
   {
      return $this->buttons['theme_advanced_buttons'.$row];
   }

   /**
    * Metoda nastavuje tlačítka v zadanám řádku
    * @param int $row -- číslo řádku
    * @param array $buttons -- tlačítka
    */
   public function setButtons($buttons, $row = 1) 
   {
      $this->buttons['theme_advanced_buttons'.$row] = $buttons;
   }

   public function prepareSettingsFromUrl()
   {
      $settings = $_GET;
      unset($settings['set']);
      unset($settings['buttons']);
      unset($settings['plugins']);
      unset($settings['tplslist']);

      // create tpl list url
      if($this instanceof Component_TinyMCE_Settings_Advanced){
         $this->setTemplatesList($settings['template_external_list_url']);
         unset($settings['template_external_list_url']);
      }
      foreach ($settings as $key => $value) {
         if($value == '0' OR $value == '1'){
            $settings[$key] = (bool)$value;
         } 
      }
      
      $this->settings = array_merge($this->settings, $settings);
      
      // remove empty and null values
      foreach ($this->settings as $key => $value) {
         if($value === null) unset ($this->settings[$key]);
      }
      // plugins
      if(isset ($_GET['plugins'])){
         $this->plugins = $_GET['plugins'];
      }
      // buttons
      if(isset ($_GET['buttons'])){
         foreach ($_GET['buttons'] as $row => $buttons) {
            $this->buttons[$row] = $buttons;
         }
      }
   }
   
   public function  settingsAsString() 
   {
      if($this->settings['document_base_url'] == null){
         $this->settings['document_base_url'] = Url_Request::getBaseWebDir();
      }
      
      if(!empty ($this->plugins)){
         $this->settings['plugins'] = implode(',', $this->plugins);
      }
      if(!empty ($this->buttons)){
         foreach ($this->buttons as $row => $buttons) {
            if($buttons != 'null'){
               $this->settings[$row] = implode(',', $buttons);
            }
         }
      }
      // css content
      if($this->settings['content_css'] == null){
         $this->settings['content_css'] = $this->getContentCssFile();
         if($this->settings['content_css'] == null){
            unset($this->settings['content_css']);
         }
      }
      
      $cnt = "tinyMCE.init({\n";
      // uživ změny
      
      $cnt .= $this->generateJsSettings($this->settings);
      $cnt .= "});\n";
      return $cnt;
   }

   /**
    * Metoda vygeneruje řetězec s parametry
    *
    * @param array -- pole parametrů
    * @return string -- řetězec s generovaným souborem
    * @todo Kompletně přepsat systém generace konfigu, tohle stojí za hovno
    */
   private function generateJsSettings($params) 
   {
      $content = null;
      foreach ($params as $paramName => $paramValue) {
         if(is_string($paramValue) && strpos ($paramValue, "function") !== false) { // je vložena funkce
            $v = str_replace(
                  array('{CATID}'),
                  array(Category::getSelectedCategory()->getId())
                  ,(string)$paramValue);
         } else if(is_string($paramValue) && ($paramValue[0] == '[' OR strpos ($paramValue, "{") !== false)) {
            $v = $paramValue;
         } else {
            $v = str_replace('\/', '/', json_encode($paramValue));
         }
         $content .= $paramName." : ".$v.",\n";
                  
         continue;
         /* ================== END ======================= */
         
         /*if(is_array($paramValue)) {
            $content .= $paramName." = ".json_encode($paramValue).",\n";
         } else {
//            if($paramValue == null) continue;
            if(is_bool($paramValue)) {
               if($paramValue) {
                  $v = "true";
               } else {
                  $v = "false";
               }
            } else if(is_int($paramValue) ){
               $v = $paramValue;
            } else if(strpos ($paramValue, "function") !== false) { // je vložena funkce
               $v = str_replace(
                     array('{CATID}'), 
                     array(Category::getSelectedCategory()->getId())
                     ,(string)$paramValue);
            } else if($paramValue[0] == '[' OR strpos ($paramValue, "{") !== false) {
               $v = $paramValue;
            } else {
               $v = "\"".$paramValue."\"";
            }
            $content .= $paramName." : ".$v.",\n";
         }*/
      }
      // odstraní poslední čárku
      $content = substr($content, 0, strlen($content)-2);
      $content .= "\n";
      return $content;
   }
   
   protected function getContentCssFile()
   {
      $contentCss = null;
      if(is_file(Template::faceDir().Template::STYLESHEETS_DIR.DIRECTORY_SEPARATOR.'style.less.css' ) ){
         // check less css file form face
         $contentCss = Template::faceUrl().Template::STYLESHEETS_DIR."/style.less.css";
      } else if(is_file(Template::faceDir(true).Template::STYLESHEETS_DIR.DIRECTORY_SEPARATOR.'style.less.css' ) ){
         // check less css file form parent face
         $contentCss = Template::faceUrl().Template::STYLESHEETS_DIR."/style.less.css";
      }
      // old for normal content css file
      else if(is_file(Template::faceDir().Template::STYLESHEETS_DIR.DIRECTORY_SEPARATOR.'style-content.css')) {
         // from face
         $contentCss = Template::faceUrl().Template::STYLESHEETS_DIR.'/style-content.css';
      } else if(VVE_SUB_SITE_DIR != null AND
            is_file(Template::faceDir(true) .Template::STYLESHEETS_DIR.DIRECTORY_SEPARATOR.'style-content.css')) {
         // from parent face
         $contentCss = Template::faceUrl(true).Template::STYLESHEETS_DIR.'/style-content.css';
      } else {
         // from core
         $contentCss = Url_Request::getBaseWebDir().Template::STYLESHEETS_DIR.'/style-content.css';
      }
      return $contentCss;
   }

   /* ArrayAccess */
   public function offsetSet($offset, $value) 
   {
      if (is_null($offset)) {
         $this->settings[] = $value;
      } else {
         $this->settings[$offset] = $value;
      }
   }
   public function offsetExists($offset) 
   {
      return isset($this->settings[$offset]);
   }
   public function offsetUnset($offset) 
   {
      unset($this->settings[$offset]);
   }
   public function offsetGet($offset) 
   {
      return isset($this->settings[$offset]) ? $this->settings[$offset] : null;
   }
   
   /* MAGIC */
   public function __set($name, $value)
   {
      $this->settings[$name] = $value;
   }
   
   public function __get($name)
   {
      return isset($this->settings[$name]) ? $this->settings[$name] : null;
   }
}
