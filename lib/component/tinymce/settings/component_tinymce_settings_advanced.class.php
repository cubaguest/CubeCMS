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
      'external_image_list_url' => null,
      'external_media_list_url' => null,
      'remove_script_host' => true,
      'content_css' => null,
      'PHPSESSID' => null,
      'cid' => null,
      'extended_valid_elements' => 'td[*],div[*],code[class],iframe[src|width|height|align|frameborder|scrolling]', // tady se musí upravit, protože tohle je nepřípustné kvůli atributům a XSS
      'forced_root_block' => 'p',
      'theme_advanced_toolbar_location' => 'top',
      'theme_advanced_toolbar_align' => 'left',
      'theme_advanced_statusbar_location' => 'bottom',
      'theme_advanced_resizing' => 'true',
      'entity_encoding' => 'raw',
      'theme_advanced_blockformats' => "p,h2,h3,h4,h5,h6,address,blockquote,code",// not h1,div,dt,dd,samp
      'valid_styles' => "{
         '*' : 'color,font-size,font-weight,font-style,text-decoration,background-color,text-align, margin-left,margin-right,margin-top,margin-bottom,float,border-left,border-right,border-top,border-bottom', 
         'table' : 'margin-left,margin-right',
         'ol' : 'list-style-type', 'ul' : 'list-style-type'}",
      'tab_focus' => ':prev,:next',
      'width' => 520,

   );

   protected $defaultPlugins = array('autolink','pagebreak','layer','safari','lists','style','table','save','advhr','cubeadvimage', 'cubeadvlink',
      'advlist','emotions','iespell','tabfocus','noneditable','nondeletable',
      'inlinepopups','insertdatetime','preview','media','searchreplace','print','contextmenu','paste',//'directionality','autoresize',
      'fullscreen','visualchars','nonbreaking','xhtmlxtras','template','imgmap','autolink','lists', 'autolink',
      'imgalign', 'imgpreview', 'cubephotogalery', 'cubehelpers' // Cube-CMS plugins
      );

   protected $defaultButtons = array(
      'theme_advanced_buttons1' => array('bold','italic','underline','strikethrough','|','justifyleft','justifycenter',
         'justifyright','justifyfull','formatselect','styleselect','undo','redo','|','preview','fullscreen'/*,'fontsizeselect'*/),
      'theme_advanced_buttons2' => array('cut','copy','paste','pastetext','pasteword','|','search','replace','|','bullist',
         'numlist','|','outdent','indent','blockquote','|','link','unlink','anchor','cleanup','code','|','forecolor','backcolor'),
      'theme_advanced_buttons3' => array('tablecontrols','|','hr','removeformat','visualaid','|','sub','sup','|','charmap','|', 'styleprops'),
      'theme_advanced_buttons4' => array('image','cubephotogalery','emotions','media','template','|','imgmap', 'imgpreview','|','imgal','imgar','|','insparbegin', 'insparend') // Cube-CMS buttons
   );

   private $fileBrowserFunction = 'function vveTinyMCEFileBrowser (field_name, url, type, win) {
   var cmsURL = location.toString();    // script URL - use an absolute path!
   tinyMCE.activeEditor.windowManager.open({
      file : "./component/tinymce_browser/{CATID}/browser.php?t="+type,
      title : "Cube File Browser", width : 750, height : 500, resizable : "yes", inline : "yes",  close_previous : "no"
   }, { window : win,input : field_name,listType : type,cat : tinyMCE.activeEditor.getParam(\'category_id\'), url:url });
   return false;
   }';
   
   private $setupFunction = null;


   public function  __construct() {
      $this->addStyleFormats();
      parent::__construct();
      $this->settingName = 'advanced';
      // fileBrowser
      if(in_array('advimage', $this->plugins) || in_array('advlink', $this->plugins)
         || in_array('cubeadvimage', $this->plugins) || in_array('cubeadvlink', $this->plugins)){
         $this->advSettings['file_browser_callback'] = $this->fileBrowserFunction;
      }
      // css content
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
      $this->advSettings['content_css'] = $contentCss;
      $this->advSettings['editorid'] = session_id();
      $this->advSettings['cid'] = Category::getSelectedCategory()->getId();
      $this->createSetupFunction();
      $this->advSettings['setup'] = $this->setupFunction;
      $this->settings = array_merge($this->settings, $this->advSettings);
   }

   private function addStyleFormats()
   {
      if(is_file(Template::faceDir().'jscripts'.DIRECTORY_SEPARATOR.'tinymce_styles_'.Locales::getLang().'.js')){
         $this->advSettings['style_formats'] = file_get_contents(Template::faceDir().'jscripts'.DIRECTORY_SEPARATOR.'tinymce_styles_'.Locales::getLang().'.js');
      } else if(is_file(Template::faceDir().'jscripts'.DIRECTORY_SEPARATOR.'tinymce_styles.js')){
         $this->advSettings['style_formats'] = file_get_contents(Template::faceDir().'jscripts'.DIRECTORY_SEPARATOR.'tinymce_styles.js');
      } else if(VVE_SUB_SITE_DIR != null AND is_file(str_replace(VVE_SUB_SITE_DIR.DIRECTORY_SEPARATOR, null, Template::faceDir()).'jscripts'.DIRECTORY_SEPARATOR.'tinymce_styles.js')){

         $this->advSettings['style_formats'] = file_get_contents(str_replace(VVE_SUB_SITE_DIR.DIRECTORY_SEPARATOR, null, Template::faceDir()).'jscripts'.DIRECTORY_SEPARATOR.'tinymce_styles.js');
      } else if(VVE_SUB_SITE_DIR != null AND is_file(str_replace(VVE_SUB_SITE_DIR.DIRECTORY_SEPARATOR, null, Template::faceDir()).'jscripts'.DIRECTORY_SEPARATOR.'tinymce_styles_'.Locales::getLang().'.js')){

         $this->advSettings['style_formats'] = file_get_contents(str_replace(VVE_SUB_SITE_DIR.DIRECTORY_SEPARATOR, null, Template::faceDir()).'jscripts'.DIRECTORY_SEPARATOR.'tinymce_styles_'.Locales::getLang().'.js');
      }
   }
   
   private function createSetupFunction()
   {
      $setupFunc = 'function(ed) {';
      
      
//      $setupFunc .= 
//      'ed.onBeforeSetContent.add(function(ed, o) {
//          if(o.content == ""){
//            o.content = "<p>'.$this->tr('Zde vytvořte obsah stránky').'.</p>";
//          }
//      });';

      
      $setupFunc .= '}';
      $this->setupFunction = $setupFunc;
   }
}
?>
