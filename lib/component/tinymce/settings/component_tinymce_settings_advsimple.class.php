<?php
/**
 * Třída pro vytvoření požadavku pro naplnění jqgrid
 */
class Component_TinyMCE_Settings_AdvSimple extends Component_TinyMCE_Settings_Advanced {
   protected $defaultPlugins = array('safari','inlinepopups','searchreplace','contextmenu','paste','advimage','advlink','media','template', 'fullscreen');

   protected $defaultButtons = array(
      'theme_advanced_buttons1' => array('bold','italic','underline','strikethrough','|','justifyleft','justifycenter',
         'justifyright','justifyfull','|','pastetext','pasteword','|','image','link','unlink','template','removeformat','formatselect','styleselect'),
      'theme_advanced_buttons2' => array(),
      'theme_advanced_buttons3' => array(),
      'theme_advanced_buttons4' => array()
   );

   public function  __construct() {
      parent::__construct();
      $this->settingName = 'advsimple';
      $this->settings['editor_selector'] = 'mceEditorSimple';
      $this->settings['height'] = '200';
   }
}
?>
