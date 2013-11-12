<?php
/**
 * Třída pro vytvoření požadavku pro naplnění jqgrid
 */
class Component_TinyMCE_Settings_Advanced extends Component_TinyMCE_Settings {
   const TPL_LIST_SYSTEM = 1;
   const TPL_LIST_SYSTEM_MAIL = 2;
   
   protected $defaultSettings = array(
      'theme' => 'advanced',
      'language' => 'en',
      'plugins' => array(),
      'editor_selector' => 'mceEditor',
      'mode' => "specific_textareas",
      'external_image_list_url' => null,
      'external_link_list_url' => null,
      'template_replace_values' => array(),
      'template_external_list_url' => null,
      'external_media_list_url' => null,
      'external_variables_list_url' => null,
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
         'td' : 'width,height',
         'th' : 'width,height',
         'ol' : 'list-style-type', 'ul' : 'list-style-type'}",
      'tab_focus' => ':prev,:next',
      'width' => "100%",
      'height' => "400",
      'alloweddirs' => array(),
      'forcedir' => null,
      'document_base_url' => null
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

   public function  __construct() {
      parent::__construct();
      $this->settingName = 'advanced';
   }

   /**
    * Metoda nastaví adresáře do kterých je povolen zápis navíc
    * @param array $dirs
    */
   public function setAllowedDirs($dirs) 
   {
      $this->settings['alloweddirs'] = $dirs;
      return $this;
   }
   
   public function getAllowedDirs() 
   {
      return $this->settings['alloweddirs'];
   }
   
   /**
    * Metoda vynutí vybraný adresář zobrazený v prohlížeči
    * @param string $dir
    */
   public function setForceDir($dir) 
   {
      $this->settings['forcedir'] = $dir;
      return $this;
   }
   
   public function getForceDir() 
   {
      return $this->settings['forcedir'];
   }
   
   public function setVariablesURL($url) 
   {
      $this->settings['external_variables_list_url'] = (string)$url;
      return $this;
   } 
   
   public function setTemplatesList($urlOrType) 
   {
       // která tpl list se používá
      switch ($urlOrType) {
         case self::TPL_LIST_SYSTEM:
            $linkJsPlugin = new Url_Link_JsPlugin('Component_TinyMCE_JsPlugin');
            $link = $linkJsPlugin->action('tplsSystem', 'js');
            break;
         case self::TPL_LIST_SYSTEM_MAIL:
            $linkJsPlugin = new Url_Link_JsPlugin('Component_TinyMCE_JsPlugin');
            $link = $linkJsPlugin->action('tplsSystemMail', 'js');
            break;
         default:
            $link = $urlOrType;
            break;
      }
      $this->setSetting('template_external_list_url', (string)$link);
   } 
   
   private function addStyleFormats()
   {
      if(is_file(Template::faceDir().'jscripts'.DIRECTORY_SEPARATOR.'tinymce_styles_'.Locales::getLang().'.js')){
         $this->settings['style_formats'] = file_get_contents(Template::faceDir().'jscripts'.DIRECTORY_SEPARATOR.'tinymce_styles_'.Locales::getLang().'.js');
      } else if(is_file(Template::faceDir().'jscripts'.DIRECTORY_SEPARATOR.'tinymce_styles.js')){
         $this->settings['style_formats'] = file_get_contents(Template::faceDir().'jscripts'.DIRECTORY_SEPARATOR.'tinymce_styles.js');
      } else if(VVE_SUB_SITE_DIR != null AND is_file(str_replace(VVE_SUB_SITE_DIR.DIRECTORY_SEPARATOR, null, Template::faceDir()).'jscripts'.DIRECTORY_SEPARATOR.'tinymce_styles.js')){

         $this->settings['style_formats'] = file_get_contents(str_replace(VVE_SUB_SITE_DIR.DIRECTORY_SEPARATOR, null, Template::faceDir()).'jscripts'.DIRECTORY_SEPARATOR.'tinymce_styles.js');
      } else if(VVE_SUB_SITE_DIR != null AND is_file(str_replace(VVE_SUB_SITE_DIR.DIRECTORY_SEPARATOR, null, Template::faceDir()).'jscripts'.DIRECTORY_SEPARATOR.'tinymce_styles_'.Locales::getLang().'.js')){

         $this->settings['style_formats'] = file_get_contents(str_replace(VVE_SUB_SITE_DIR.DIRECTORY_SEPARATOR, null, Template::faceDir()).'jscripts'.DIRECTORY_SEPARATOR.'tinymce_styles_'.Locales::getLang().'.js');
      }
   }
   
   private function createSetupFunction()
   {
      $setupFunc = 'function(ed) {';
      $setupFunc .= '}';
      return $setupFunc;
   }
   
   public function settingsAsString()
   {
      $str = null;
      // fileBrowser
      if(in_array('advimage', $this->plugins) || in_array('advlink', $this->plugins)
         || in_array('cubeadvimage', $this->plugins) || in_array('cubeadvlink', $this->plugins)){
         $this->settings['file_browser_callback'] 
            = Component_TinyMCE_Browser::getUploaderFunction($this);
      }
      
      $this->settings['editorid'] = session_id();
      $this->settings['cid'] = Category::getSelectedCategory()->getId();
      if($this->settings['cid'] == 0){
         $this->settings['cid'] = (int)AppCore::getUrlRequest()->getCategory();
      }
      
      $this->settings['setup'] = $this->createSetupFunction();
      $str .= $this->createDynamicVariablesPlugin();
      $str .= parent::settingsAsString();
      
      return $str;
   }
   
   protected function getContentCssFile()
   {
      $this->addStyleFormats();
      return parent::getContentCssFile();
   }
   
   protected function createDynamicVariablesPlugin()
   {
      if( $this->settings['external_variables_list_url'] != null ){
         $this->buttons['theme_advanced_buttons1'][] = 'variablesList';
         $this->plugins[] = '-variables';
         
         $cnt = 'tinymce.create("tinymce.plugins.Variables",{
            createControl : function(n, cm) {
               if(n == "variablesList"){
                  var mlb = cm.createListBox("variablesList", {
                     title : "'.$this->tr('Proměnné').'",
                     onselect : function(v) {
                        tinyMCE.activeEditor.execCommand(\'mceInsertContent\', false, v);
                     }
                  });
                  tinymce.util.XHR.send({
                     url : \''.$this->settings['external_variables_list_url'].'\',
                     success : function(data) {
                        var l = tinymce.util.JSON.parse(data);
                        if(l.variables){
                           tinymce.each(l.variables, function(v, i) { mlb.add(v, i); });
                        }
                     }
                  });
                  return mlb;
               }
               return null;
            }
         });
         tinymce.PluginManager.add("variables", tinymce.plugins.Variables);'."\n\n";
         return $cnt;
      }
      return null;
   }
}
?>
