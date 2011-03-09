<?php
/**
 * Třída pro vytvoření požadavku pro naplnění jqgrid
 */
class Component_TinyMCE_Settings_Advanced extends Component_TinyMCE_Settings {
   protected $advSettings = array(
      'theme' => 'advanced',
      'plugins' => array(),
      'external_image_list_url' => null,
      'external_link_list_url' => null,
      'template_replace_values' => array(),
      'template_external_list_url' => null,
      'remove_script_host' => false,
      'content_css' => null,
      'extended_valid_elements' => 'td[*],div[*],code[class],iframe[src|width|height|name|align|frameborder|scrolling]', // tady se musí upravit, protože tohle je nepřípustné kvůli atributům a XSS
      'forced_root_block' => 'p',
      'theme_advanced_toolbar_location' => 'top',
      'theme_advanced_toolbar_align' => 'left',
      'theme_advanced_statusbar_location' => 'bottom',
      'theme_advanced_resizing' => 'true',
      'entity_encoding' => 'raw',
      'theme_advanced_blockformats' => "p,h2,h3,h4,h5,h6,address,blockquote,code",// not h1,div,dt,dd,samp
      'valid_styles' => "{'*' : 'color,font-size,font-weight,font-style,text-decoration,background-color,text-align'}",
      'tab_focus' => ':prev,:next',
      'width' => 520,

   );

   protected $defaultPlugins = array('safari','style','table','save','advhr','advimage','advlink','emotions','iespell','tabfocus',
      'inlinepopups','insertdatetime','preview','media','searchreplace','print','contextmenu','paste',//'directionality',
      'fullscreen','noneditable','visualchars','nonbreaking','xhtmlxtras','template','imgmap', 
      'imgalign', 'imgpreview' // Cube-CMS plugins
      );

   protected $defaultButtons = array(
      'theme_advanced_buttons1' => array('bold','italic','underline','strikethrough','|','justifyleft','justifycenter',
         'justifyright','justifyfull','formatselect','styleselect','undo','redo','|','preview','fullscreen'/*,'fontsizeselect'*/),
      'theme_advanced_buttons2' => array('cut','copy','paste','pastetext','pasteword','|','search','replace','|','bullist',
         'numlist','|','outdent','indent','blockquote','|','link','unlink','anchor','cleanup','code','|','forecolor','backcolor'),
      'theme_advanced_buttons3' => array('tablecontrols','|','hr','removeformat','visualaid','|','sub','sup','|','charmap'),
      'theme_advanced_buttons4' => array('image','imgmap','|','imgal','imgar', '|', 'imgpreview', '|', 'emotions','media','template') // Cube-CMS buttons
   );

   private $fileBrowserFunction = 'function vveTinyMCEFileBrowser (field_name, url, type, win) {
   var cmsURL = location.toString();    // script URL - use an absolute path!
   tinyMCE.activeEditor.windowManager.open({
      file : "./component/tinymce_browser/{CATID}/browser.php",
      title : "Cube File Browser", width : 750, height : 500, resizable : "yes", inline : "yes",  close_previous : "no"
   }, { window : win,input : field_name,listType : type,cat : tinyMCE.activeEditor.getParam(\'category_id\'), url:url });
   return false;
   }';


   public function  __construct() {
      $this->addStyleFormats();
      parent::__construct();
      $this->settingName = 'advanced';
      // fileBrowser
      if(in_array('advimage', $this->plugins) || in_array('advlink', $this->plugins)){
         $this->advSettings['file_browser_callback'] = $this->fileBrowserFunction;
      }
      // css
      if(file_exists(AppCore::getAppWebDir().Template::FACES_DIR.DIRECTORY_SEPARATOR.
      Template::face().DIRECTORY_SEPARATOR.Template::STYLESHEETS_DIR.DIRECTORY_SEPARATOR.'style-content.css')) {
         $this->advSettings['content_css'] = Template::face(false).Template::STYLESHEETS_DIR.URL_SEPARATOR.'style-content.css';
      } else {
         $this->advSettings['content_css'] = Url_Request::getBaseWebDir().Template::STYLESHEETS_DIR.DIRECTORY_SEPARATOR.'style-content.css';
      }
      
      $this->settings = array_merge($this->settings, $this->advSettings);
   }

   private function addStyleFormats()
   {
      if(file_exists(Template::faceDir().'jscripts'.DIRECTORY_SEPARATOR.'tinymce_styles_'.Locales::getLang().'.js')){
         $this->advSettings['style_formats'] = file_get_contents(Template::faceDir().'jscripts'.DIRECTORY_SEPARATOR.'tinymce_styles_'.Locales::getLang().'.js');
      } else if(Template::faceDir().'jscripts'.DIRECTORY_SEPARATOR.'tinymce_styles.js'){
         $this->advSettings['style_formats'] = file_get_contents(Template::faceDir().'jscripts'.DIRECTORY_SEPARATOR.'tinymce_styles.js');
      }
   }
}
?>
