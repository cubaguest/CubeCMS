<?php
/**
 * Třída nasatvení TinyMCE Editoru
 *
 * Pluginy:
 *    safari, emoticons
 *
 * Tlačítka:
 *    'bold','italic','underline','strikethrough','|','bullist','numlist','|','link','unlink','|','undo','redo','|', 'emotions'
 */
class Component_TinyMCE_Settings_AdvSimple2 extends Component_TinyMCE_Settings_Advanced {
   protected $defaultPlugins = array('safari','emotions','inlinepopups');

   protected $defaultButtons = array(
      'theme_advanced_buttons1' => array('bold','italic','underline','strikethrough','|',
         'bullist','numlist','|','link','unlink','|','undo','redo','|', 'emotions'),
      'theme_advanced_buttons2' => array(),
      'theme_advanced_buttons3' => array(),
      'theme_advanced_buttons4' => array()
   );

   public function  __construct() {
      parent::__construct();
      $this->settings = array_merge($this->settings, array('extended_valid_elements' => null, 'remove_script_host' => true));
      $this->settings['valid_styles'] = "{
         '*' : 'font-weight,font-style,text-decoration,text-align',
         'ol' : 'list-style-type', 'ul' : 'list-style-type'}";
      unset($this->settings['style_formats']);
      unset($this->settings['external_image_list_url']);
      unset($this->settings['external_link_list_url']);
      unset($this->settings['template_external_list_url']);
      unset($this->settings['external_media_list_url']);
      $this->settingName = 'advsimple2';
   }

   protected function getContentCssFile()
   {
      return Component_TinyMCE_Settings::getContentCssFile();
   }
}
