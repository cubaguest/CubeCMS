<?php
/**
 * Třída pro vytvoření nastavení tinymce editoru pro maily
 */
class Component_TinyMCE_Settings_Mail extends Component_TinyMCE_Settings_Advanced {
   
   protected $defaultButtons = array(
      'theme_advanced_buttons1' => array("bold","italic","underline","strikethrough","|","justifyleft","justifycenter",
         "justifyright","justifyfull","formatselect","styleselect","fontselect","fontsizeselect"),
      'theme_advanced_buttons2' => array("cut","copy","paste","pastetext","pasteword","|","search","replace","|",
         "bullist","numlist","|","outdent","indent","blockquote","|","undo","redo","|","link","unlink","anchor","|",
         "forecolor","backcolor","|","removeformat","cleanup","visualaid"),
      'theme_advanced_buttons3' => array("tablecontrols","|","insertdate","inserttime","|","hr","charmap","sub","sup","|",
         "image","emotions","iespell","media","advhr","|","ltr","rtl"),
      'theme_advanced_buttons4' => array("insertlayer","moveforward","movebackward","absolute","|","styleprops","|",
         "cite","abbr","acronym","del","ins","attribs","|","visualchars","nonbreaking","template","blockquote","pagebreak","|",
         "newdocument","|","preview","print","fullscreen","code",'|','imgal','imgar')
   );
   
   public function  __construct() {
      parent::__construct();
      $this->settingName = 'mail';
      
   }
   
   public function prepareSettingsFromUrl()
   {
      parent::prepareSettingsFromUrl();
      $this->settings['relative_urls'] = false;
      $this->settings['remove_script_host'] = false;
      $this->settings['height'] = 600;
      $this->settings['forced_root_block'] = 'div';
      $this->settings['theme_advanced_blockformats'] = "p,div,h1,h2,h3,h4,h5,h6,blockquote,code,samp,address";
      $this->settings['theme_advanced_font_sizes'] = "10px,12px,13px,14px,16px,18px,20px,30px,40px";
      $this->settings['style_formats'] = null;
      unset($this->settings['valid_styles']);
   }
   
   protected function getContentCssFile()
   {
      return null;
   }
}
?>
