<?php
/**
 * Třída pro vytvoření požadavku pro naplnění jqgrid
 */
class Component_TinyMCE_Settings_Full extends Component_TinyMCE_Settings_Advanced {
   protected $defaultPlugins = array('safari','spellchecker','pagebreak','style','layer','table','save','advhr','advimage','advlink','emotions','iespell','inlinepopups','insertdatetime','preview','media','searchreplace','print','contextmenu','paste','directionality','fullscreen','noneditable','visualchars','nonbreaking','xhtmlxtras','template');

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
        "newdocument","|","preview","print","fullscreen","code")
   );

   public function  __construct() {
      parent::__construct();
      $this->settingName = 'full';
   }
}
?>
