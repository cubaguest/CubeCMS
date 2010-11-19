<?php
/**
 * Třída pro vytvoření požadavku pro naplnění jqgrid
 */
abstract class Component_TinyMCE_Settings {
   const SETTING_EXTERNAL_TPL_LIST = 'template_external_list_url';


   protected $settingName = null;

   protected $settings = array(
      'theme' => 'simple',
      'mode' => "specific_textareas",
      'skin' => "o2k7",
      'skin_variant' => "black",
      'editor_selector' => 'mceEditor',
      'root_element' => true,
      'relative_urls' => true,
      'height' => 'auto',
      'language' => 'en',
      'width' => 500,
      'document_base_url' => null
   );

   protected $defaultPlugins = array();
   protected $plugins = array();

   protected $defaultButtons = array();
   protected $buttons = array();

   /**
    * Pole s upravenými hodnotami (jen ty se budou přenášet v url adrese)
    * @var array
    */
   protected $userSettings = array();

   public function  __construct() {
      $this->settings['language'] = Locales::getLang();
      $this->settings['document_base_url'] = Url_Request::getBaseWebDir();
      $this->settings['content_css'] = Url_Request::getBaseWebDir();
      $this->buttons = $this->defaultButtons;
      $this->plugins = $this->defaultPlugins;
   }

   final public function getSetting($name) {
      return $this->settings[$name];
   }

   /**
    * Metoda nasatvuje parametr editoru
    * @param string $name -- název parametru
    * @param mixed $value -- hodnota parametru
    * @return Component_TinyMCE_Settings
    */
   final public function setSetting($name, $value) {
      $this->userSettings[$name] = $value;
      $this->settings[$name] = $value;
      return $this;
   }

   final public function getParamsForUrl() {
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
      return array_merge($urlParams, $this->userSettings, $additional);
   }

   /**
    * metoda vrací vybrané pluginy
    * @return array -- pol epluginů
    */
   public function getPlugins() {
      return $this->plugins;
   }

   /**
    * Metoda nastavuje pluginy
    * @param array $pluginsarr -- pole pluginů
    */
   public function setPlugins($pluginsarr) {
      $this->plugins = $pluginsarr;
   }

   /**
    * Metoda vrací tlačítka v zadanám řádku
    * @param int $row -- číslo řádku
    */
   public function getButtons($row = 1) {
      return $this->buttons['theme_advanced_buttons'.$row];
   }

   /**
    * Metoda nastavuje tlačítka v zadanám řádku
    * @param int $row -- číslo řádku
    * @param array $buttons -- tlačítka
    */
   public function setButtons($buttons, $row = 1) {
      $this->buttons['theme_advanced_buttons'.$row] = $buttons;
   }

   final public function  __toString() {
      $cnt = "tinyMCE.init({\n";
      // uživ změny
      $this->mergeUserSettings();
      // remove empty and null values
      foreach ($this->settings as $key => $value) {
         if($value == null) unset ($this->settings[$key]);
      }
      // plugins
      $this->mergeUserPlugins(); // spojení uživatelksých buttonu
      if(!empty ($this->plugins)){
         $this->settings['plugins'] = implode(',', $this->plugins);
      }
      // buttons
      $this->mergeUserButtons(); // spojení uživatelksých tlačítek
      if(!empty ($this->buttons)){
         foreach ($this->buttons as $row => $buttons) {
            if($buttons != 'null'){
               $this->settings[$row] = implode(',', $buttons);
            }
         }
      }
      $cnt .= $this->generateJsSettings($this->settings);
      $cnt .= "});\n";
      return $cnt;
   }

   /**
    * Metoda spojí pole s předanými buttony
    */
   private function mergeUserButtons() {
      if(isset ($_GET['buttons'])){
         foreach ($_GET['buttons'] as $row => $buttons) {
            $this->buttons[$row] = $buttons;
         }
      }
   }

   /**
    * Metoda spojí pole s předanými pluginy
    */
   private function mergeUserPlugins() {
      if(isset ($_GET['plugins'])){
         $this->plugins = $buttons;
      }
   }

   /**
    * Sloučí uživatelské změny s výchozími
    */
   private function mergeUserSettings(){
      $settings = $_GET;
      unset($settings['set']);
      unset($settings['buttons']);
      unset($settings['plugins']);
      unset($settings['tplslist']);
      /*
       * @todo kontrola settings na specifické vlastnosti
       */
      $this->settings = array_merge($this->settings, $settings);
   }

   /**
    * Metoda vygeneruje řetězec s parametry
    *
    * @param array -- pole parametrů
    * @return string -- řetězec s generovaným souborem
    */
   private function generateJsSettings($params) {
      $content = null;
      foreach ($params as $paramName => $paramValue) {
         if(is_array($paramValue)) {
            $content .= $this->generateJsSettings($paramValue);
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
               $v = str_replace('{CATID}', Category::getSelectedCategory()->getId(),(string)$paramValue);
            } else {
               $v = "\"".$paramValue."\"";
            }
            $content .= $paramName." : ".$v.",\n";
         }
      }
      // odstraní poslední čárku
      $content = substr($content, 0, strlen($content)-2);
      $content .= "\n";
      return $content;
   }
}
?>
