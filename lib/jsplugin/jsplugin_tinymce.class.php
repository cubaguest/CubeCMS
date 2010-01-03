<?php
/**
 * Třída JsPluginu TinyMce.
 * Třída slouží pro vytvoření JsPluginu TinyMce, což je wysiwing textový edtor,
 * který se navazuje na textarea v šabloně. Třída umožňuje jednoduché nastavení
 * parametrů editor (volba vzhledu, jazyka, atd).
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída JsPluginu TinyMce
 */

class JsPlugin_TinyMce extends JsPlugin {
/**
 * Konstanta s názvem adresáře s pluginem
 * @var string
 */
   const TINY_MCE_MAIN_DIR = 'tinymce';

   /**
    * $GET s theme tinymce
    * @var string
    */
   const PARAM_TINY_THEME = 'theme';

   /**
    * $GET s modem tinymce
    * @var string
    */
   const PARAM_TINY_MODE = 'mode';

   /**
    * $GET s odkazem na list obrázků
    * @var string
    */
   const PARAM_TINY_IMAGES_LIST = 'img';

   /**
    * $GET s odkazem na list odkazů
    * @var string
    */
   const PARAM_TINY_LINKS_LIST = 'link';

   /**
    * Druhy theme pro tiny mce
    * @var string
    */
   const TINY_THEME_SIMPLE = 'simple';
   const TINY_THEME_ADVANCED = 'advanced';
   const TINY_THEME_ADVANCED_SIMPLE = 'advsimple';
   const TINY_THEME_FULL = 'full';

   /**
    * Parametry pro typ theme
    */
   const THEME_ADVANCED = 'advanced';
   const THEME_SIMPLE = 'simple';

   /**
    * některé parametry v konfigu
    */
   const PARAM_THEME = 'theme';

   /**
    * Parametr obrázků
    */
   const PARAM_IMAGES = 'images';

   /**
    * Parametr obrázků
    */
   const PARAM_MEDIA = 'media';

   /**
    * Parametr s adresářem faces
    */
   const PARAM_FACE = 'facedir';

   /**
    * Parametr s název kategorie
    */
   const PARAM_CATEGORY = 'cat';

   /**
    * Parametr s název sekce pro kategorii
    */
   const PARAM_SECTION = 'sec';

   /**
    * Název pole s iconami (bez čísla!!!)
    */
   const ICONS_ROWS_NAME = 'theme_advanced_buttons';

   /**
    * Parametr přidá ikonu pro přidání obrázků
    */
   const ICON_IMAGES = 'image';

   /**
    * Parametr přidá ikonu pro přidání medií (flash)
    */
   const ICON_MEDIA = 'media';

   /**
    * Název parametru s pluginy v konfigu
    */
   const PLUGINS_ARRAY_NAME = 'plugins';

   /**
    * Název pluginu pro obrázky
    */
   const PLUGIN_IMAGES = 'advimage';

   /**
    * Název pluginu pro média
    */
   const PLUGIN_MEDIA = 'media';

   /**
    * Mody tinymce
    * @var atring
    */
   const TINY_MODE_TEXTAREAS = 'textareas';

   /**
    * Pole s konfigurací pluginu
    * @var array
    */
   protected $config = array(
   'theme' => 'simple',
   'skin' => "o2k7",
   'skin_variant' => "black",
   'specific_textareas' => false,
	'editor_selector' => 'mceEditor',
   'textarea_trigger' => "convert_this",
   'entity_encoding' => 'raw',
   'encoding' => 'xml',
   'external_image_list_url' => null,
   'external_link_list_url' => null,
   'template_external_list_url' => null,
   'template_replace_values' => array()
   );


   /**
    * výchozí parametry tinyMCE
    */
   private $defaultParams = array(
   'theme' => 'advanced',
   'mode' => "textareas",
   'language' => 'cs',
   'category_id' => null,
   'force_br_newlines' => true,
   'document_base_url' => null,
   'remove_script_host' => false,
   'content_css' => null,
   'extended_valid_elements' => 'td[*],div[*]',
   'forced_root_block' => false,
   'theme_advanced_toolbar_location' => 'top',
   'theme_advanced_toolbar_align' => 'left',
   'theme_advanced_statusbar_location' => 'bottom',
   'theme_advanced_resizing' => 'true');

   private $advParams = array(
   'external_image_list_url' => null,
   'external_link_list_url' => null,
   'template_external_list_url' => null,
   'template_replace_values' => array(),
   'file_browser_callback' => 'vveTinyMCEFileBrowser'

   //   ,'theme_advanced_toolbar_location' => 'external'
   );

   /**
    * Parametry pro Advanced THEME
    * @var array
    */
   private $advanced1Params = array(
   'plugins' => array('safari', 'style', 'table', 'save', 'advhr', 'advimage', 'advlink', 'emotions', 'iespell', 'inlinepopups',
   'insertdatetime', 'preview', 'media', 'searchreplace', 'print', 'contextmenu', 'paste', 'directionality', 'fullscreen',
   'noneditable', 'visualchars', 'nonbreaking', 'xhtmlxtras', 'template'),
   'theme_advanced_buttons1' => array('bold', 'italic', 'underline', 'strikethrough', '|', 'justifyleft', 'justifycenter', 'justifyright',
   'justifyfull', '|', 'formatselect', 'fontselect', 'fontsizeselect', '|', 'preview', 'fullscreen', 'template'),
   'theme_advanced_buttons2' => array('cut', 'copy', 'paste', 'pastetext', '|', 'search,replace', '|', 'bullist,numlist', '|', 'outdent',
   'indent,blockquote', '|', 'undo', 'redo', '|', 'link', 'unlink', 'anchor', 'cleanup', 'code', '|', 'inserttime', '|',
   'forecolor', 'backcolor'),
   'theme_advanced_buttons3' => array('tablecontrols', '|', 'hr', 'removeformat', 'visualaid', '|', 'sub', 'sup', '|', 'charmap',
   'emotions', 'image' , 'media', '|', 'ltr', 'rtl'));

   /**
    * Parametry pro ořezané advanced THEME
    * @var array
    */
   private $advancedSimpleParams = array(
   'plugins' => 'safari,inlinepopups,searchreplace,contextmenu,paste',
   'theme_advanced_buttons1' => 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,search,|,link,unlink,|,undo,redo,code',
   'theme_advanced_buttons2' => null,
   'theme_advanced_buttons3' => null);

   /**
    * Parametry pro ořezané advanced THEME
    * @var array
    */
   private $advancedFullParams = array(
   'plugins' => "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
//   'theme_advanced_buttons1' => "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
   'theme_advanced_buttons1' => "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
//   'theme_advanced_buttons2' => "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
   'theme_advanced_buttons2' => "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,forecolor,backcolor",
//   'theme_advanced_buttons3' => "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
   'theme_advanced_buttons3' => "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
//   'theme_advanced_buttons4' => "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage"
   'theme_advanced_buttons4' => "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertdate,inserttime,preview"
   );

   protected function initJsPlugin() {
      $this->defaultParams['document_base_url'] = Url_Request::getBaseWebDir();
   }

   protected function setFiles() {
      $this->addFile(new JsPlugin_JsFile("tiny_mce.js"));
      switch ($this->getCfgParam('theme')) {
         case 'simple':
            $cfgFile = new JsPlugin_JsFile("settingssimple.js", true);
            break;
         case 'advancedsimple':
            $cfgFile = new JsPlugin_JsFile("settingsadvancedsimple.js", true);
            break;
         case 'full':
            $editorFile = new JsPlugin_JsFile("tiny_mce_browser.js");
            $editorFile->setParam('cat', Category::getSelectedCategory()->getId());
            $this->addFile($editorFile);
            $cfgFile = new JsPlugin_JsFile("settingsfull.js", true);
            break;
         case 'advanced1':
         case 'advanced':
         default:
            $editorFile = new JsPlugin_JsFile("tiny_mce_browser.js");
            $editorFile->setParam('cat', Category::getSelectedCategory()->getId());
            $this->addFile($editorFile);
            $cfgFile = new JsPlugin_JsFile("settingsadvanced1.js", true);
            break;
      }
      $cfgFile->setParam('editor_selector', $this->config['editor_selector']);
      $this->addFile($cfgFile);
   }

   /**
    * Metoda nastaví některé dynmické proměnné, které jsou ve všech módech
    * @param <type> $params 
    */
   private function setBasicOptions(&$params) {
      $params['document_base_url'] = Url_Request::getBaseWebDir();
      $params['language'] = Locale::getLang();
      $params['category_id'] = AppCore::getCategory()->getId();
      if(isset ($_GET['editor_selector'])){
         $params['editor_selector'] = rawurldecode($_GET['editor_selector']);
      } else {
         $params['editor_selector'] = $this->getCfgParam('editor_selector');
      }
      if(file_exists(AppCore::getAppWebDir().Template::FACES_DIR.DIRECTORY_SEPARATOR.
            Template::face().DIRECTORY_SEPARATOR.Template::STYLESHEETS_DIR.DIRECTORY_SEPARATOR.'style-content.css')){
         $params['content_css'] = Template::face(false).Template::STYLESHEETS_DIR.URL_SEPARATOR.'style-content.css';
      }
      $params['skin'] = $this->getCfgParam('skin');
      $params['skin_variant'] = $this->getCfgParam('skin_variant');
//'skin' => "o2k7",
//   'skin_variant' => "black",
      return $params;
   }

   /**
    * Metodda pro generování simple theme
    */
   public function settingsSimpleView() {
      $params = $this->defaultParams;
      $params = $this->setBasicOptions($params);
      $params['theme'] = 'simple';
      $content = $this->cfgFileHeader();
      $content .= $this->generateParamsForFile($params);
      $content .= $this->cfgFileFooter();

      print ($content);
   }

   /**
    * Metodda pro generování advanced 1 theme
    */
   public function settingsAdvanced1View() {
      $params = array_merge($this->defaultParams, $this->advParams, $this->advanced1Params);
      $params = $this->setBasicOptions($params);
      $content = $this->cfgFileHeader();
      $content .= $this->generateParamsForFile($params);
      $content .= $this->cfgFileFooter();

      print ($content);
   }

   /**
    * Metodda pro generování advanced 1 theme
    */
   public function settingsAdvancedSimpleView() {
      $params = array_merge($this->defaultParams, $this->advParams, $this->advancedSimpleParams);
      $params = $this->setBasicOptions($params);
      $content = $this->cfgFileHeader();
      $content .= $this->generateParamsForFile($params);
      $content .= $this->cfgFileFooter();

      print ($content);
   }

   /**
    * Metodda pro generování advanced 1 theme
    */
   public function settingsFullView() {
      $params = array_merge($this->defaultParams, $this->advParams, $this->advancedFullParams);
      $params = array_merge($params, $this->advancedFullParams);
      $params = $this->setBasicOptions($params);

      $content = $this->cfgFileHeader();
      $content .= $this->generateParamsForFile($params);
      $content .= $this->cfgFileFooter();

      print ($content);
   }


   // metody pro nastavení
   /**
    * Metoda nastaví typ zobrazení pluginu
    */
   public function setTheme($theme) {
      $this->getSettingsJsFile()->setParam(self::PARAM_TINY_THEME, $theme);
   }

   /*
    * privátní metody
    */

   /**
    * Metoda vygeneruje řetězec s parametry
    *
    * @param array -- pole parametrů
    * @return string -- řetězec s generovaným souborem
    */
   private function generateParamsForFile($params) {
      $content = null;
      foreach ($params as $paramName => $paramValue) {
         if(is_array($paramValue)) {
            $content .= $this->generateParamsForFile($paramValue);
         } else {
            if(is_bool($paramValue)) {
               if($paramValue) {
                  $v = "true";
               } else {
                  $v = "false";
               }
            } else if(is_int($paramValue)) {
                  $v = (string)$paramValue;
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

   /**
    * Metoda generuje hlavičku konfogiračního souboru
    *
    * @return string -- hlavička souboru
    */
   private function cfgFileHeader() {
      $header = "tinyMCE.init({\n";
      return $header;
   }

   /**
    * Metoda generuje patičku konfiguračního souboru
    *
    * @return string -- patička souboru
    */
   private function cfgFileFooter() {
      $footer = "});\n";
      return $footer;
   }

   /*
    * Metody pro generování obsahu filebrowseru
    */

   public function filebrowserView() {
      

//      $template = new Template(new Url_Link());
//      $template->addTplFile('tinymce/filebrowser.phtml');
//
//      $template->renderTemplate();
   }

   /**
    * Metoda vrátí adresáře ve formátu JSON
    */
   public function getDirsView() {
      $tpl = new Template_JsPlugin(new Url_Link(), AppCore::getCategory(), $this);
      $tpl->addTplFile('browser/dirs.phtml');

      $tpl->dirs = $this->loadDir(AppCore::getAppWebDir().'data/');
      $tpl->dataPath = AppCore::getAppWebDir().'data/';

      $tpl->renderTemplate();
   }

   /**
    * Metoda načte strukturu adresáře
    */
   private function loadDir($path) {
      $array = array();
      $it = new DirectoryIterator($path);
      foreach ($it as $itFile) {
         if($itFile->isDir() AND !$itFile->isDot() AND !ereg("^\.", $itFile->getFileName())) {
            $arr = $this->loadDir($itFile->getPath().DIRECTORY_SEPARATOR.$itFile->getFileName());
            $array[$itFile->getFileName()]['childs'] = $arr;
            $array[$itFile->getFileName()]['path'] = str_replace(AppCore::getAppWebDir(),
                '', $itFile->getPath()).URL_SEPARATOR.$itFile->getFileName();
         }
      }
      return $array;
   }

   /**
    * Metoda vrátí adresáře ve formátu JSON
    */
   public function getFilesView() {
      $tpl = new Template_JsPlugin(new Url_Link(), AppCore::getCategory(), $this);
      if(isset ($_GET['dir'])) {
         $dir = urldecode($_GET['dir']);
      } else {
         $dir = "data";
      }
      $files = array();
      switch ($_GET['type']) {
         case 'image':
            $it = new DirectoryIterator(AppCore::getAppWebDir().$dir);
            foreach ($it as $itFile) {
               if(!$itFile->isDir() AND !$itFile->isDot()) {
                  if(($size = @getimagesize($itFile->getPath().DIRECTORY_SEPARATOR.$itFile->getFileName())) !== false
                     AND $size[2] != IMAGETYPE_SWC AND $size != IMAGETYPE_SWF) {
                     $file = array(
                         'type' => 'image',
                         'name' => $itFile->getFileName(),
                         'width' => $size[0],
                         'height' => $size[1],
                         'mime' => $size['mime'],
                         'size' => filesize(AppCore::getAppWebDir().$dir.DIRECTORY_SEPARATOR.$itFile->getFileName()),
                         'path' => Url_Request::getBaseWebDir().$dir.URL_SEPARATOR
                     );
                     array_push($files, $file);
                  }
               }
            }
            break;
         case 'media':
            $it = new DirectoryIterator(AppCore::getAppWebDir().$dir);
            foreach ($it as $itFile) {
               if(!$itFile->isDir() AND !$itFile->isDot()) {
                  $finfo = false;
                  if(function_exists('finfo_open')) {
                     $finfo = finfo_open(FILEINFO_MIME);
                  }

                  if(preg_match("/\.(swf|wmv|rm|mov)$/i",$itFile->getFileName())) {
                     $file = array(
                         'type' => 'video-x-generic',
                         'name' => $itFile->getFileName(),
                         'mime' => null,
                         'size' => filesize(AppCore::getAppWebDir().$dir.DIRECTORY_SEPARATOR.$itFile->getFileName()),
                         'path' => Url_Request::getBaseWebDir().$dir.URL_SEPARATOR
                     );
                     if($finfo !== false){
                        $file['mime'] = finfo_file($finfo, $itFile->getPath().DIRECTORY_SEPARATOR.$itFile->getFileName());
                        $file['mime'] = preg_replace(array('/^([^\ ]+)/', '/^(\w+)\//'), array('\1', '\1-'), $file['mime']);
                     }
                     array_push($files, $file);
                  }
               }
            }
            break;
         default:
            $it = new DirectoryIterator(AppCore::getAppWebDir().$dir);
            $finfo = false;
            if(function_exists('finfo_open')){
               $finfo = finfo_open(FILEINFO_MIME);
            }
            foreach ($it as $itFile) {
               if(!$itFile->isDir() AND !$itFile->isDot()) {
                  $file = array();
                  $file['size'] = filesize(AppCore::getAppWebDir().$dir.DIRECTORY_SEPARATOR.$itFile->getFileName());
                  $file['path'] = Url_Request::getBaseWebDir().$dir.URL_SEPARATOR;
                  $file['name'] = $itFile->getFileName();
                  $file['type'] = 'file';
                  $file['mime'] = null;
                  if($finfo !== false){
                     $file['mime'] = finfo_file($finfo, $itFile->getPath().DIRECTORY_SEPARATOR.$itFile->getFileName());
                     $file['mime'] = preg_replace(array('/^([^\ ]+)/', '/^(\w+)\//'), array('\1', '\1-'), $file['mime']);
                     $file['mime'] = preg_replace('/^([^ ]+) (.*)$/', '\1', $file['mime']);
                  }
                  $matches = array();
                  // obr
                  if(($size = getimagesize($itFile->getPath().DIRECTORY_SEPARATOR.$itFile->getFileName())) !== false
                     AND $size[2] != IMAGETYPE_SWC AND $size[2] != IMAGETYPE_SWF) {
                     $file['type'] = 'image';
                     $file['width'] = $size[0];
                     $file['height'] = $size[1];
                     $file['mime'] = $size['mime'];
                     
                  }
                  array_push($files, $file);
               }
            }
            break;
      }
      $tpl->addTplFile('browser/files.phtml');
      //      print(new Url_Link());
      $tpl->files = $files;
      //      $tpl->dataPath = AppCore::getAppWebDir().'data/';

      $tpl->renderTemplate();
   }

   private function sendJsonData($data) {
      print (json_encode($data));
   }

   /**
    * Metoda pro vytvoření adresáře
    */
   public function createdirView() {
      $newDir = vve_cr_safe_file_name($_POST['dirname']);
      $path = $_POST['path'];

      if(!Auth::isLoginStatic()){
         $message = sprintf(_('Byl jste odhlášen. Adresář "%s" se nepodařilo vytvořit'), $path.$newDir);
         $code = false;
      }

      if($path == 'null' OR $path == null) {
         $path = 'data';
      }
      if(substr($path, strlen($path)-1, 1) != '/') {
         $path .= DIRECTORY_SEPARATOR;
      }
//      print (AppCore::getAppWebDir().$path.$newDir);
      if(@mkdir(AppCore::getAppWebDir().$path.$newDir, 0777, true)) {
         $message = sprintf(_('Adresář "%s" byl vytvořen'), $path.$newDir);
         $code = true;
      } else {
         $message = sprintf(_('Adresář "%s" se nepodařilo vytvořit'), $path.$newDir);
         $code = false;
      }

      $this->sendJsonData(array('code' => $code, 'message' => $message, 'data' => $newDir,
          'path' => $path.$newDir));
   }

   /**
    * Metoda pro mazání adresáře
    */
   public function removedirView() {
      function deleteDirectory($dirname) {
               if (!file_exists($dirname)) {return false;} // Sanity check
               if (is_file($dirname) || is_link($dirname)) {return unlink($dirname);}
               $dir = dir($dirname);
               while (false !== $entry = $dir->read()) {
                  if ($entry == '.' || $entry == '..') {continue;}
                  if(!deleteDirectory($dirname . DIRECTORY_SEPARATOR . $entry)){
                     return false;
                  }
               }
               $dir->close();
               return rmdir($dirname);
      }
      $dir = $_POST['dir'];
      //      var_dump(deleteDirectory(AppCore::getAppWebDir().$dir.DIRECTORY_SEPARATOR));
      if(deleteDirectory(AppCore::getAppWebDir().$dir.DIRECTORY_SEPARATOR)) {
      //      if(@rmdir(AppCore::getAppWebDir().$dir.DIRECTORY_SEPARATOR.'*')) {
         $message = sprintf(_('Adresář "%s" byl smazán'), $dir);
         $code = true;
      } else {
         $message = sprintf(_('Adresář "%s" se nepodařilo smazat'), $dir);
         $code = false;
      }
      $this->sendJsonData(array('code' => $code, 'message' => $message));
   }

   public function renamedirView() {
      $oldDirName = vve_cr_safe_file_name($_POST['oldname']);
      $newDirName = vve_cr_safe_file_name($_POST['newname']);
      $path = $_POST['path'];
      $path = str_ireplace($_POST['newname'], '', $path);
      if(@rename(AppCore::getAppWebDir().$path.$oldDirName,
         AppCore::getAppWebDir().$path.$newDirName)) {
         $message = sprintf(_('Adresář "%s" byl přejmenován'), $path.$oldDirName);
         $code = true;
      } else {
         $message = sprintf(_('Adresář "%s" se nepodařilo přejmenovat'), $path.$oldDirName);
         $code = false;
      }
      $this->sendJsonData(array('code' => $code, 'message' => $message, 'data' => $newDirName));
   }
   
   /**
    * Metoda pro přesun adresářů
    */
   public function movedirView() {
      $oldPath = $_POST['oldpath'];
      $newPath = $_POST['newpath'];

      if(@rename(AppCore::getAppWebDir().$oldPath,
         AppCore::getAppWebDir().$newPath)) {
         $message = sprintf(_('Adresář "%s" byl přesunut'), $oldPath);
         $code = true;
      } else {
         $message = sprintf(_('Adresář "%s" se nepodařilo přesunout'), $oldPath);
         $code = false;
      }
      $this->sendJsonData(array('code' => $code, 'message' => $message));
   }

   /**
    * Metoda pro upload souborů
    */
   public function uploadFileView() {
      $listType = $_POST['newf_ListType'];
      $dir = $_POST['newf_Dir'];
      if($dir == null) $dir = 'data';
      if(substr($dir, strlen($dir)-1, 1) != '/') {
         $dir .= DIRECTORY_SEPARATOR;
      }

      $form = new Form('newf_');

      $file = new Form_Element_File('File');
      $validNoEmpty = new Form_Validator_NotEmpty();
      $file->addValidation($validNoEmpty);

      if($listType == 'image') {
         $validOnlyImage = new Form_Validator_FileExtension(array('jpg', 'jpeg', 'png', 'gif'));
         $file->addValidation($validOnlyImage);
      } else if($listType == 'media') {
            $validOnlyImage = new Form_Validator_FileExtension(array('swf', 'qt', 'wmv', 'rm'));
            $file->addValidation($validOnlyImage);
         }

      $file->setUploadDir(AppCore::getAppWebDir().$dir);
      $form->addElement($file);

      $submit = new Form_Element_Submit('Upload');
      $form->addElement($submit);

      $result = null;
      if($form->isSend()) {

         if($form->isValid()) {
            $result = _('Soubor byl uložen');
         }
         if(!$validNoEmpty->isValid()) {
            $result = _('Soubor nebyl vybrán');
         }
         // pouze obrázky
         if(isset ($validOnlyImage) AND !$validOnlyImage->isValid()) {
            $result = _('Soubor není obrázek');
         }
      }
      sleep(1);
      print ('<script language="javascript" type="text/javascript">
         parent.FileBrowserFilesFunctions.stopUpload("'.$result.'");
         </script> ');
   }

   public function removeFileView() {
      $file = $_POST['file'];
      $dir = $_POST['dir'];
      $code = false;
      if(file_exists(AppCore::getAppWebDir().$dir.DIRECTORY_SEPARATOR.$file)) {
         if(unlink(AppCore::getAppWebDir().$dir.DIRECTORY_SEPARATOR.$file)) {
            $message = sprintf(_('Soubor "%s" byl smazán'), $file);
            $code = true;
         } else {
            $message = sprintf(_('Soubor "%s" se napodařilo smazat'), $file);
         }
      } else {
         $message = sprintf(_('Soubor "%s" v adresáři "%s" neexistuje'), $file, $dir);
      }
      $this->sendJsonData(array('code' => $code, 'message' => $message));
   }

   /**
    * Metoda pro přejmenování souboru
    */
   public function renamefileView() {
      $oldName = $_POST['oldname'];
      $newName = vve_cr_safe_file_name($_POST['newname']);
      $path = $_POST['path'];

      if(file_exists(AppCore::getAppWebDir().$path.DIRECTORY_SEPARATOR.$oldName)) {
         if(@rename(AppCore::getAppWebDir().$path.DIRECTORY_SEPARATOR.$oldName,
         AppCore::getAppWebDir().$path.DIRECTORY_SEPARATOR.$newName)) {
            $message = _('Soubor byl přejmenován');
            $code = true;
         } else {
            $message = _('Soubor se nepodařilo přejmenovat');
            $code = false;
         }
      } else {
         $message = _('Soubor nebyl nalezen');
         $code = false;
      }
      $this->sendJsonData(array('code' => $code, 'message' => $message));
   }

   /**
    * Metoda pro přesun souboru
    */
   public function movefileView() {
      $file = $_POST['file'];
      $dir = $_POST['dir'];
      $newDir = $_POST['newdir'];

      if(file_exists(AppCore::getAppWebDir().$dir.DIRECTORY_SEPARATOR.$file)) {
         if(@rename(AppCore::getAppWebDir().$dir.DIRECTORY_SEPARATOR.$file,
         AppCore::getAppWebDir().$newDir.DIRECTORY_SEPARATOR.$file)) {
            $message = _('Soubor byl přesunut');
            $code = true;
         } else {
            $message = _('Soubor se nepodařilo přesunut');
            $code = false;
         }
      } else {
         $message = _('Soubor v adresáři neexistuje');
         $code = false;
      }
      $this->sendJsonData(array('code' => $code, 'message' => $message));
   }


   /*
    * POMOCNÉ funkce pro úpravu obrázků
    */
   /**
    * Metoda pro změnu velikosti obrázku
    */
   public function resizeimageView() {
      if(Auth::isLoginStatic()){

      $path = $_POST['path'];
      $file = $_POST['file'];
      $width = $_POST['size_w'];
      $heigh = $_POST['size_h'];

      $badValues = false;
      if(!is_numeric($width) OR !is_numeric($heigh)){
         $badValues = true;
      }

      $path = str_replace(Url_Request::getBaseWebDir(), AppCore::getAppWebDir(), $path);

      $image = new Filesystem_File_Image($file, $path, false);
//      $image->setDimensions();
      $image->resampleImage($width, $heigh);
      $image->save();
      if(!$image->isError() AND $badValues !== true){
         $code = true;
         $message = sprintf(_('Velikost obrázku "%s" byla upravenna'),$image->getName());
      } else {
         $code = false;
         $message = sprintf(_('Velikost obrázku "%s" se nepodařilo upravit'),$image->getName());
      }
      } else {
         $code = false;
         $message = sprintf(_('Nemáte dostatečná práva nebo jste byl odhlášen'),$image->getName());
      }

      $this->sendJsonData(array('code' => $code, 'message' => $message));
   }

   /**
    * Metoda pro změnu velikosti obrázku
    */
   public function cropimageView() {
      if(Auth::isLoginStatic()){

      $path = $_POST['path'];
      $file = $_POST['file'];
      $x1 = $_POST['x1'];
      $y1 = $_POST['y1'];
      $x2 = $_POST['x2'];
      $y2 = $_POST['y2'];

      $badValues = false;
      if(!is_numeric($x1) OR !is_numeric($y1) OR !is_numeric($x2) OR !is_numeric($y2)){
         $badValues = true;
      }

      $path = str_replace(Url_Request::getBaseWebDir(), AppCore::getAppWebDir(), $path);

      $image = new Filesystem_File_Image($file, $path, false);
//      $image->setDimensions();
      $image->crop($x1, $y1, $x2, $y2);
      $image->save();
      if(!$image->isError() AND $badValues !== true){
         $code = true;
         $message = sprintf(_('Obrázek "%s" byl ořezán'),$image->getName());
      } else {
         $code = false;
         $message = sprintf(_('Obrázek "%s" se nepodařilo ořezat'),$image->getName());
      }
      } else {
         $code = false;
         $message = sprintf(_('Nemáte dostatečná práva nebo jste byl odhlášen'),$image->getName());
      }

      $this->sendJsonData(array('code' => $code, 'message' => $message));
   }

   /**
    * Metoda pro změnu rotace obrázku
    */
   public function rotateimageView() {
      $path = $_POST['path'];
      $file = $_POST['file'];
      $angle = $_POST['angle'];

      $bgColor = 0;
      $regexp = '/bg=([[:digit:]]+)/i';
      $matches = array();
      if(preg_match($regexp, $angle, $matches) != 0){
         $bgColor = $matches[1];
      }

      $image = new Filesystem_File_Image($file, $path, false);
      $image->rotateImage($angle, $bgColor);
      $image->save();
      if(!$image->isError()){
         $code = true;
         $message = sprintf(_('Rotace obrázku "%s" byla upravenna'),$image->getName());
      } else {
         $code = false;
         $message = sprintf(_('Rotaci obrázku "%s" se nepodařilo upravit'),$image->getName());
      }

      $this->sendJsonData(array('code' => $code, 'message' => $message));
   }

   /**
    * Metoda pro změnu rotace obrázku
    */
   public function flipimageView() {
      $path = $_POST['path'];
      $file = $_POST['file'];
      $axis = $_POST['axis'];

      $image = new Filesystem_File_Image($file, $path, false);

      $regexp = '/([xy]{1})/i';
      $matches = array();
      if(preg_match($regexp, $axis, $matches) != 0){
         $image->flip($matches[1]);
      }
      $image->save();
      if(!$image->isError()){
         $code = true;
         $message = sprintf(_('Zrcadlení obrázku "%s" bylo provedeno'),$image->getName());
      } else {
         $code = false;
         $message = sprintf(_('Zrcadlení obrázku "%s" se nepodařilo provést'),$image->getName());
      }

      $this->sendJsonData(array('code' => $code, 'message' => $message));
   }
}
?>