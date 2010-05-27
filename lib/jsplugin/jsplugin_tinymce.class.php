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
   const TINY_THEME_SIMPLE_2 = 'advancedsimple2';
   const TINY_THEME_ADVANCED = 'advanced';
   const TINY_THEME_ADVANCED_SIMPLE = 'advancedsimple';
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
    * Název souboru se seznamem editovatelných adresářů
    */
   const W_DIRS_FILE = 'dirs.xml';

   /**
    * Název souboru s externími šablonami
    */
   const EXTERNAL_TEMPLATES_FILE = 'tinymce_templates.js';

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
           'external_image_list_url' => null,
           'external_link_list_url' => null,
//           'template_external_list_url' => null,
           'template_replace_values' => array(),
           'root_element' => true
   );


   /**
    * výchozí parametry tinyMCE
    */
   private $defaultParams = array(
           'theme' => 'advanced',
           'mode' => "textareas",
           'language' => 'cs',
           'entity_encoding' => 'raw',
           //'encoding' => 'xml',
           'category_id' => null,
//           'force_br_newlines' => true, // dělá bordel, nejde vkládat další prvky
           'document_base_url' => null,
           'remove_script_host' => false,
           'content_css' => null,
           'extended_valid_elements' => 'td[*],div[*],code[class],iframe[src|width|height|name|align|frameborder|scrolling]',
           'forced_root_block' => 'p',
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
           'plugins' => 'safari,style,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template',
//      'theme_advanced_buttons1' => "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,styleselect,fontselect,fontsizeselect",
           'theme_advanced_buttons1' => 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect,|,preview,fullscreen,template',
           'theme_advanced_buttons2' => 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,cleanup,code,|,forecolor, backcolor',
//           'theme_advanced_buttons2' => "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,|,forecolor,backcolor,|,removeformat,cleanup,visualaid",
           'theme_advanced_buttons3' => 'tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,image,media,|,ltr,rtl'
//           'theme_advanced_buttons3' => "tablecontrols,|,insertdate,inserttime,|,hr,charmap,sub,sup,|,image,emotions,iespell,media,advhr,|,ltr,rtl"
   );
   /**
    * Parametry pro ořezané advanced THEME
    * @var array
    */
   private $advancedSimpleParams = array(
           'plugins' => 'safari,inlinepopups,searchreplace,contextmenu,paste,advimage,advlink,media',
           'theme_advanced_buttons1' => 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,pastetext,pasteword,|,bullist,numlist,|,search,|,link,unlink,|,undo,redo,code,|,image,media',
           'theme_advanced_buttons2' => null,
           'theme_advanced_buttons3' => null);

   /**
    * Parametry pro ořezané advanced THEME
    * @var array
    */
   private $advancedSimpleParams2 = array(
           'plugins' => 'safari,emotions',
           'theme_advanced_buttons1' => 'bold,italic,underline,strikethrough,|,link,unlink,|,undo,redo,|,emotions',
           'theme_advanced_buttons2' => null,
           'theme_advanced_buttons3' => null);

   /**
    * Parametry pro ořezané advanced THEME
    * @var array
    */
   private $advancedFullParams = array(
           'plugins' => "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
           'theme_advanced_buttons1' => "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,styleselect,fontselect,fontsizeselect",
           'theme_advanced_buttons2' => "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,|,forecolor,backcolor,|,removeformat,cleanup,visualaid",
           'theme_advanced_buttons3' => "tablecontrols,|,insertdate,inserttime,|,hr,charmap,sub,sup,|,image,emotions,iespell,media,advhr,|,ltr,rtl",
           'theme_advanced_buttons4' => "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,newdocument,|,preview,print,fullscreen,code"
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
         case 'advancedsimple2':
            $cfgFile = new JsPlugin_JsFile("settingsadvancedsimple2.js", true);
            break;
         case 'advancedsimple':
            $cfgFile = new JsPlugin_JsFile("settingsadvancedsimple.js", true);
            $editorFile = new JsPlugin_JsFile("tiny_mce_browser.js");
            $editorFile->setParam('cat', Category::getSelectedCategory()->getId());
            $this->addFile($editorFile);
            break;
         case 'full':
            $cfgFile = new JsPlugin_JsFile("settingsfull.js", true);
            $editorFile = new JsPlugin_JsFile("tiny_mce_browser.js");
            $editorFile->setParam('cat', Category::getSelectedCategory()->getId());
            $this->addFile($editorFile);
            break;
         case 'advanced1':
         case 'advanced':
         default:
            $cfgFile = new JsPlugin_JsFile("settingsadvanced1.js", true);
            $editorFile = new JsPlugin_JsFile("tiny_mce_browser.js");
            $editorFile->setParam('cat', Category::getSelectedCategory()->getId());
            $this->addFile($editorFile);
            break;
      }
      $cfgFile->setParam('editor_selector', $this->config['editor_selector']);
      if($this->config['root_element'] == false){
         $cfgFile->setParam('root_element', 'false');
      }
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
      $params['sessionid'] = Sessions::getSessionId();
      if(isset ($_GET['editor_selector'])) {
         $params['editor_selector'] = rawurldecode($_GET['editor_selector']);
      } else {
         $params['editor_selector'] = $this->getCfgParam('editor_selector');
      }
      if(isset ($_GET['root_element']) AND $_GET['root_element'] == 'false') {
         $params['forced_root_block'] = false;
      }
      if(file_exists(AppCore::getAppWebDir().Template::FACES_DIR.DIRECTORY_SEPARATOR.
      Template::face().DIRECTORY_SEPARATOR.Template::STYLESHEETS_DIR.DIRECTORY_SEPARATOR.'style-content.css')) {
         $params['content_css'] = Template::face(false).Template::STYLESHEETS_DIR.URL_SEPARATOR.'style-content.css';
      } else {
         $params['content_css'] = Url_Request::getBaseWebDir().Template::STYLESHEETS_DIR.DIRECTORY_SEPARATOR.'style-content.css';
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
    * Metodda pro generování simple theme
    */
   public function settingsAdvancedSimple2View() {
      $params = array_merge($this->defaultParams, $this->advancedSimpleParams2);
      $params = $this->setBasicOptions($params);
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
      $this->addTemplatesFile($params);
      
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
      $this->addTemplatesFile($params);

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

   /**
    * Metoda přidá do parametrů soubor se šablonami
    * @param array $params -- parametry
    */
   private function addTemplatesFile(&$params) {
      $link = new Url_Link_JsPlugin($this);
      $params['template_external_list_url'] = $link->action('templates', 'js');
   }

   /*
    * Metody pro generování obsahu filebrowseru
   */

   public function filebrowserView() {
   }

   /**
    * Metoda vrátí adresáře ve formátu JSON
    */
   public function getDirsView() {
      if(!$this->category()->getRights()->isWritable()) {
         header('HTTP/1.1 403 Forbidden');
         print(_('Byl jste odhlášen.').' '._('Adresáře se nepodařilo načíst'));
         flush();
         exit();
      }

      $tpl = new Template_JsPlugin(new Url_Link(), AppCore::getCategory(), $this);
      $tpl->addTplFile('browser/dirs.phtml');

      $tpl->dirs = $this->loadDir(AppCore::getAppWebDir().'data/');
      $tpl->wrdirs = $this->loadWritableDirs();
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
         if($itFile->isDir() AND !$itFile->isDot() AND preg_match("/^\./", $itFile->getFileName()) != 1) {
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
      if(!$this->category()->getRights()->isWritable()) {
         header('HTTP/1.1 403 Forbidden');
         print(_('Byl jste odhlášen.').' '._('Soubory se nepodařilo načíst'));
         flush();
         exit();
      }

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
                     if($finfo !== false) {
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
            if(function_exists('finfo_open')) {
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
                  if($finfo !== false) {
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
      $dirs = $this->loadWritableDirs();
      if(in_array($dir, $dirs)) {
         $tpl->editable = true;
      } else {
         $tpl->editable = false;
      }
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

      if(!$this->category()->getRights()->isWritable()) {
         $message = _('Byl jste odhlášen.').sprintf(' '._('Adresář "%s" se nepodařilo vytvořit'), $path.$newDir);
         $code = false;
      } else {

         if($path == 'null' OR $path == null) {
            $path = 'data';
         }
         if(substr($path, strlen($path)-1, 1) != '/') {
            $path .= DIRECTORY_SEPARATOR;
         }
//      print (AppCore::getAppWebDir().$path.$newDir);
         if(file_exists(AppCore::getAppWebDir().$path.$newDir)
                 AND is_dir(AppCore::getAppWebDir().$path.$newDir)) {
            $message = sprintf(_('Adresář "%s" již existuje'), AppCore::getAppWebDir().$path.$newDir);
            $code = false;
         } else if(@mkdir(AppCore::getAppWebDir().$path.$newDir, 0777, true)) {
            $dirs = $this->loadWritableDirs();
            $this->addNewWrDir($path.$newDir, $dirs);
            $this->saveWritableDirs($dirs);
            $message = sprintf(_('Adresář "%s" byl vytvořen'), $path.$newDir);
            $code = true;
         } else {
            $message = sprintf(_('Adresář "%s" se nepodařilo vytvořit'), $path.$newDir);
            $code = false;
         }
      }

      $this->sendJsonData(array('code' => $code, 'message' => $message, 'data' => $newDir,
              'path' => $path.$newDir));
   }

   /**
    * Metoda pro mazání adresáře
    */
   public function removedirView() {
      $dir = $_POST['dir'];

      if(!$this->category()->getRights()->isWritable()) {
         $message = _('Byl jste odhlášen.').sprintf(' '._('Adresář "%s" se nepodařilo smazat'), $dir);
         $code = false;
      } else {
         function deleteDirectory($dirname) {
            if (!file_exists($dirname)) {
               return false;
            } // Sanity check
            if (is_file($dirname) || is_link($dirname)) {
               return unlink($dirname);
            }
            $dir = dir($dirname);
            while (false !== $entry = $dir->read()) {
               if ($entry == '.' || $entry == '..') {
                  continue;
               }
               if(!deleteDirectory($dirname . DIRECTORY_SEPARATOR . $entry)) {
                  return false;
               }
            }
            $dir->close();
            return rmdir($dirname);
         }
         $wDirs = $this->loadWritableDirs();

         if(in_array($dir, $wDirs)) {
            if(deleteDirectory(AppCore::getAppWebDir().$dir.DIRECTORY_SEPARATOR)) {
               $dirs = $this->loadWritableDirs();
               $this->delWrDir($dir, $dirs);
               $this->saveWritableDirs($dirs);
               $message = sprintf(_('Adresář "%s" byl smazán'), $dir);
               $code = true;
            } else {
               $message = sprintf(_('Adresář "%s" se nepodařilo smazat'), $dir);
               $code = false;
            }
         } else {
            $message = sprintf(_('Adresář "%s" nelze smazat. Systémový?'), $dir);
            $code = false;
         }
      }

      $this->sendJsonData(array('code' => $code, 'message' => $message));
   }

   private function loadWritableDirs() {
      $arr = array();
      if(file_exists(AppCore::getAppWebDir().VVE_DATA_DIR.DIRECTORY_SEPARATOR.self::W_DIRS_FILE)) {
         $dirs = simplexml_load_file(AppCore::getAppWebDir().VVE_DATA_DIR.DIRECTORY_SEPARATOR.self::W_DIRS_FILE);
         // TODO tohle optimalizovat !!!!!!!!!!!!
         foreach ($dirs->name as $dir) {
            if(empty ($dir)) continue;
            array_push($arr, $dir);
         }
      }
      return $arr;
   }

   private function saveWritableDirs($dirs) {
      $xml = new XMLWriter();
      $xml->openMemory();
      $xml->setIndent(4);
      $xml->startDocument('1.0', 'UTF-8');
      $xml->startElement('dirs');
      foreach ($dirs as $dir) {
         $xml->writeElement('name', $dir);
      }
      $xml->endElement();
//      if(file_exists(AppCore::getAppWebDir().VVE_DATA_DIR.DIRECTORY_SEPARATOR.self::W_DIRS_FILE)){
//         unlink(AppCore::getAppWebDir().VVE_DATA_DIR.DIRECTORY_SEPARATOR.self::W_DIRS_FILE);
//      }
      file_put_contents(AppCore::getAppWebDir().VVE_DATA_DIR.DIRECTORY_SEPARATOR.self::W_DIRS_FILE, $xml->outputMemory(), LOCK_EX);
   }

   private function addNewWrDir($dirName, &$dirs) {
      $dirs[] = $dirName;
      return $dirs;
   }

   private function delWrDir($dirName, &$dirs) {
      foreach ($dirs as $key => $dir) {
         if(preg_match("/^".str_replace('/', '\/', $dirName)."(\/|$)/", $dir) == 1) {
            unset ($dirs[$key]);
         }
      }
   }

   private function renameWrDir($oldName, $newName, &$dirs) {
      foreach ($dirs as &$dir) {
         $dir = preg_replace("/^".str_replace('/', '\/', $oldName)."(\/|$)/", $newName."\\1", $dir);
      }
   }

   public function renamedirView() {
      $oldDirName = $_POST['oldname'];
      $newDirName = vve_cr_safe_file_name($_POST['newname']);
      $path = $_POST['path'];

      if(!$this->category()->getRights()->isWritable()) {
         $message = _('Byl jste odhlášen.').sprintf(' '._('Adresář "%s" se nepodařilo přejmenovat'), $oldDirName);
         $code = false;
      } else {

         if(file_exists(AppCore::getAppWebDir().$path.DIRECTORY_SEPARATOR.$newDirName)
                 AND is_dir(AppCore::getAppWebDir().$path.DIRECTORY_SEPARATOR.$newDirName)) {
            $message = sprintf(_('Adresář "%s" již existuje'), $path.DIRECTORY_SEPARATOR.$newDirName);
            $code = false;
         } else if(@rename(AppCore::getAppWebDir().$oldDirName,
         AppCore::getAppWebDir().$path.DIRECTORY_SEPARATOR.$newDirName)) {
            $dirs = $this->loadWritableDirs();
            $this->renameWrDir($oldDirName, $path.DIRECTORY_SEPARATOR.$newDirName, $dirs);
            $this->saveWritableDirs($dirs);
            $message = sprintf(_('Adresář "%s" byl přejmenován na "%s"'), $oldDirName,$path.DIRECTORY_SEPARATOR.$newDirName);
            $code = true;
         } else {
            $message = sprintf(_('Adresář "%s" se nepodařilo přejmenovat'), $oldDirName);
            $code = false;
         }
      }
      $this->sendJsonData(array('code' => $code, 'message' => $message, 'data' => $newDirName));
   }

   /**
    * Metoda pro přesun adresářů
    */
   public function movedirView() {
      $oldPath = $_POST['oldpath'];
      $newPath = $_POST['newpath'];
      $dir = substr($oldPath, strrpos($oldPath,URL_SEPARATOR)+1);

      if(!$this->category()->getRights()->isWritable()) {
         $message = _('Byl jste odhlášen.').sprintf(' '._('Adresář "%s" se nepodařilo přesunout'), $oldPath);
         $code = false;
      } else {
         $dirs = $this->loadWritableDirs();
         $this->renameWrDir($oldPath, $newPath.URL_SEPARATOR.$dir, $dirs);
         $this->saveWritableDirs($dirs);
         if(file_exists(AppCore::getAppWebDir().$newPath.DIRECTORY_SEPARATOR.$dir)
                 AND is_dir(AppCore::getAppWebDir().$newPath.DIRECTORY_SEPARATOR.$dir)) {
            $message = sprintf(_('Adresář "%s" již existuje'), $newPath.DIRECTORY_SEPARATOR.$dir);
            $code = false;
         } else if(@rename(AppCore::getAppWebDir().$oldPath,
         AppCore::getAppWebDir().$newPath.DIRECTORY_SEPARATOR.$dir)) {
            $message = sprintf(_('Adresář "%s" byl přesunut do "%s"'), $oldPath, $newPath.URL_SEPARATOR.$dir);
            $code = true;
         } else {
            $message = sprintf(_('Adresář "%s" se nepodařilo přesunout'), $oldPath);
            $code = false;
         }
      }
      $this->sendJsonData(array('code' => $code, 'message' => $message,
              'data' => $newPath.URL_SEPARATOR.$dir,
              'dataold' => str_replace('/', '\/', $oldPath)));
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

      if(!$this->category()->getRights()->isWritable()) {
         print ('<script language="javascript" type="text/javascript">
         alert("'._('Byl jste odhlášen.').' '._('Soubor nebyl nahrán.').'");
         </script> ');
      } else {

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
   }

   public function removeFileView() {
      $file = $_POST['file'];
      $dir = $_POST['dir'];

      if(!$this->category()->getRights()->isWritable()) {
         $message = _('Byl jste odhlášen.').sprintf(' '._('Soubor "%s" se nepodařilo smazat'), $file);
         $code = false;
      } else {
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
      }
      $this->sendJsonData(array('code' => $code, 'message' => $message));
   }

   public function removeFilesView() {
      $files = $_POST['files'];
      $dir = $_POST['dir'];

      if(!$this->category()->getRights()->isWritable()) {
         $message = _('Byl jste odhlášen.').sprintf(' '._('Soubory "%s" se nepodařilo smazat'), $files);
         $code = false;
      } else {
         $files = explode(';', $files);
         $code = true;
         $badFiles = null;
         foreach ($files as $file) {
            if(file_exists(AppCore::getAppWebDir().$dir.DIRECTORY_SEPARATOR.$file)
                    OR unlink(AppCore::getAppWebDir().$dir.DIRECTORY_SEPARATOR.$file)) {
            } else {
               $code = false;
               $badFiles .= $file.', ';
            }
         }
         if($code) {
            $message = _('Soubory byly smazány');
         } else {
            $badFiles = sprintf(_('Soubory "%s" se napodařilo smazat'), substr($badFiles, 0, strlen($badFiles)-2));
         }
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

      if(!$this->category()->getRights()->isWritable()) {
         $message = _('Byl jste odhlášen.').sprintf(' '._('Soubor "%s" se nepodařilo přejmenovat'), $oldName);
         $code = false;
      } else {
         if(file_exists(AppCore::getAppWebDir().$path.DIRECTORY_SEPARATOR.$oldName)) {
            if(@rename(AppCore::getAppWebDir().$path.DIRECTORY_SEPARATOR.$oldName,
            AppCore::getAppWebDir().$path.DIRECTORY_SEPARATOR.$newName)) {
               $message = sprintf(_('Soubor "%s" byl přejmenován na "%s"'), $oldName, $newName);
               $code = true;
            } else {
               $message = sprintf(_('Soubor "%s" se nepodařilo přejmenovat'), $oldName);
               $code = false;
            }
         } else {
            $message = sprintf(_('Soubor "%s" nebyl nalezen'), $oldName);
            $code = false;
         }
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

      $files = explode(';', $file);

      if(!$this->category()->getRights()->isWritable()) {
         $message = _('Byl jste odhlášen.').sprintf(' '._('Soubor "%s" se nepodařilo přesunot'), $file);
         $code = false;
      } else if($files[0] == '') {
         $message = _('Nebyl vybrán žádný soubor pro přesun');
         $code = false;
      } else {
         foreach ($files as $f) {
            if(file_exists(AppCore::getAppWebDir().$dir.DIRECTORY_SEPARATOR.$f)) {
               if(@rename(AppCore::getAppWebDir().$dir.DIRECTORY_SEPARATOR.$f,
               AppCore::getAppWebDir().$newDir.DIRECTORY_SEPARATOR.$f)) {
                  $message = sprintf(_('Soubor "%s "byl přesunut'), $file);
                  $code = true;
               } else {
                  $message = sprintf(_('Soubor "%s" se nepodařilo přesunut'), $file);
                  $code = false;
               }
            } else {
               $message = sprintf(_('Soubor "%s" v adresáři neexistuje'), $file);
               $code = false;
            }
         }
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
      $path = $_POST['path'];
      $file = $_POST['file'];
      $width = $_POST['size_w'];
      $heigh = $_POST['size_h'];

      if(!$this->category()->getRights()->isWritable()) {
         $message = _('Byl jste odhlášen.').sprintf(' '._('Soubor "%s" se nepodařilo upravit'), $image->getName());
         $code = false;
      } else {
         $badValues = false;
         if(!is_numeric($width) OR !is_numeric($heigh)) {
            $badValues = true;
         }

         $path = str_replace(Url_Request::getBaseWebDir(), AppCore::getAppWebDir(), $path);

         $image = new Filesystem_File_Image($file, $path, false);
//      $image->setDimensions();
         $image->resampleImage($width, $heigh);
         $image->save();
         if(!$image->isError() AND $badValues !== true) {
            $code = true;
            $message = sprintf(_('Velikost obrázku "%s" byla upravenna'),$image->getName());
         } else {
            $code = false;
            $message = sprintf(_('Velikost obrázku "%s" se nepodařilo upravit'),$image->getName());
         }
      }

      $this->sendJsonData(array('code' => $code, 'message' => $message));
   }

   /**
    * Metoda pro změnu velikosti obrázku
    */
   public function cropimageView() {
      $path = $_POST['path'];
      $file = $_POST['file'];
      $x1 = $_POST['x1'];
      $y1 = $_POST['y1'];
      $x2 = $_POST['x2'];
      $y2 = $_POST['y2'];

      if(!$this->category()->getRights()->isWritable()) {
         $message = _('Byl jste odhlášen.').sprintf(' '._('Soubor "%s" se nepodařilo upravit'), $image->getName());
         $code = false;
      } else {

         $badValues = false;
         if(!is_numeric($x1) OR !is_numeric($y1) OR !is_numeric($x2) OR !is_numeric($y2)) {
            $badValues = true;
         }

         $path = str_replace(Url_Request::getBaseWebDir(), AppCore::getAppWebDir(), $path);

         $image = new Filesystem_File_Image($file, $path, false);
//      $image->setDimensions();
         $image->crop($x1, $y1, $x2, $y2);
         $image->save();
         if(!$image->isError() AND $badValues !== true) {
            $code = true;
            $message = sprintf(_('Obrázek "%s" byl ořezán'),$image->getName());
         } else {
            $code = false;
            $message = sprintf(_('Obrázek "%s" se nepodařilo ořezat'),$image->getName());
         }
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

      if(!$this->category()->getRights()->isWritable()) {
         $message = _('Byl jste odhlášen.').sprintf(' '._('Soubor "%s" se nepodařilo upravit'), $file);
         $code = false;
      } else {

         $bgColor = 0;
         $regexp = '/bg=([[:digit:]]+)/i';
         $matches = array();
         if(preg_match($regexp, $angle, $matches) != 0) {
            $bgColor = $matches[1];
         }

         $image = new Filesystem_File_Image($file, $path, false);
         $image->rotateImage($angle, $bgColor);
         $image->save();
         if(!$image->isError()) {
            $code = true;
            $message = sprintf(_('Rotace obrázku "%s" byla upravenna'),$image->getName());
         } else {
            $code = false;
            $message = sprintf(_('Rotaci obrázku "%s" se nepodařilo upravit'),$image->getName());
         }
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

      if(!$this->category()->getRights()->isWritable()) {
         $message = _('Byl jste odhlášen.').sprintf(' '._('Soubor "%s" se nepodařilo upravit'), $file);
         $code = false;
      } else {
         $image = new Filesystem_File_Image($file, $path, false);

         $regexp = '/([xy]{1})/i';
         $matches = array();
         if(preg_match($regexp, $axis, $matches) != 0) {
            $image->flip($matches[1]);
         }
         $image->save();
         if(!$image->isError()) {
            $code = true;
            $message = sprintf(_('Zrcadlení obrázku "%s" bylo provedeno'),$image->getName());
         } else {
            $code = false;
            $message = sprintf(_('Zrcadlení obrázku "%s" se nepodařilo provést'),$image->getName());
         }
      }
      $this->sendJsonData(array('code' => $code, 'message' => $message));
   }

   /**
    * Metoda pro vrácení seznamu šablon
    *
    * var tinyMCETemplateList = [
    * // Name, URL, Description
    * ["Simple snippet", "templates/snippet1.htm", "Simple HTML snippet."],
    * ["Layout", "templates/layout1.htm", "HTML Layout."]
    * ];
    * @see http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/template#Example_of_an_external_list
    */
   public function templatesView(){
      // header
      print('var tinyMCETemplateList = ['."\n");
      // načtení externích
      if(file_exists(Template::faceDir().Template::TEMPLATES_DIR.DIRECTORY_SEPARATOR.self::EXTERNAL_TEMPLATES_FILE)){
         $externalTpls = file_get_contents(Template::faceDir().Template::TEMPLATES_DIR.URL_SEPARATOR.self::EXTERNAL_TEMPLATES_FILE);
         $matches = array();
         preg_match_all('/\[[^][]+\]/', $externalTpls, $matches);
         foreach ($matches[0] as $extFile) {
            print ($extFile.",\n");
         }
      }
      // šablony z modulu
      $modelTpl = new Templates_Model();
      $tpllist = $modelTpl->getTemplates(Templates_Model::TEMPLATE_TYPE_TEXT);
      if(!empty ($tpllist)){
         // link
         $link = new Url_Link_ModuleStatic();
         $link->module('templates')->action('template', 'html');
         $tplstr = null;
         foreach ($tpllist as $tpl) {
            $tplstr .= '["'.$tpl->{Templates_Model::COLUMN_NAME}.'", "'
            .$link->param('id', $tpl->{Templates_Model::COLUMN_ID}).'", "'.$tpl->{Templates_Model::COLUMN_DESC}."\"],\n";
         }
         print (substr($tplstr, 0, strlen($tplstr)-2)."\n");
      }
      //end
      print('];'."\n");
   }
}
?>