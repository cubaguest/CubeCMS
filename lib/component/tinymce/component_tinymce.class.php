<?php
/**
 * Třída pro komnponenty Wysiwing editoru TinyMCE
 */
class Component_TinyMCE extends Component {
   const TPL_LIST_SYSTEM = 'system';
   const TPL_LIST_FILE = 'file';
   const TPL_LIST_SYSTEM_MAIL = 'systemmail';

   private $jsPlugin = null;

   private $templateList = self::TPL_LIST_SYSTEM;

   protected function  init() {
      $this->jsPlugin = new Component_TinyMCE_JsPlugin();
      parent::init();
   }
   
   /**
    * Metoda vrací objekt s nasatvením editoru
    * @return Component_TinyMCE_Settings
    */
   public function setEditorSettings(Component_TinyMCE_Settings $settings) {
      $this->jsPlugin->setSettings($settings);
   }

   /**
    * Metoda vrací aktuální objekt nasatvení editoru
    * @return Component_TinyMCE_Settings
    */
   public function getEditorSettings() {
      return $this->jsPlugin->getSettings();
   }

   /**
    * Metoda nastaví list šablony pro vložení 
    * @param string $type -- typ listu nebo url adresa k seznamu 
    */
   public function setTplsList($type) {
      $this->templateList = $type;
   }


   /**
    * Metoda nastaví id šablony pro výpis
    * @param integer -- id šablony (jakékoliv)
    */
   public function mainView() {
      if($this->jsPlugin->getSettings() instanceof Component_TinyMCE_Settings_Advanced){
         // která tpl list se používá
         switch ($this->templateList) {
            case self::TPL_LIST_SYSTEM:
               $linkJsPlugin = new Url_Link_JsPlugin('Component_TinyMCE_JsPlugin');
               $link = (string)$linkJsPlugin->action('tplsSystem', 'js');
               break;
            case self::TPL_LIST_SYSTEM_MAIL:
               $linkJsPlugin = new Url_Link_JsPlugin('Component_TinyMCE_JsPlugin');
               $link = (string)$linkJsPlugin->action('tplsSystemMail', 'js');
               break;
            case self::TPL_LIST_FILE:
            default:
               $link = $this->templateList;
               break;
         }
         $this->jsPlugin->getSettingFile()->setParam(Component_TinyMCE_Settings::SETTING_EXTERNAL_TPL_LIST, (string)$link);
      }
      $this->template()->addJsPlugin($this->jsPlugin);
   }
}
?>
