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
   'mode' => 'textareas',
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
   'mode' => 'textareas',
   'theme' => 'advanced',
   'language' => 'cs',
   'force_br_newlines' => true,
   'document_base_url' => null,
   'remove_script_host' => false,
   'content_css' => null,
   'extended_valid_elements' => 'td[*],div[*]',
   'theme_advanced_toolbar_location' => 'top',
   'theme_advanced_toolbar_align' => 'left',
   'theme_advanced_statusbar_location' => 'bottom',
   'theme_advanced_resizing' => 'true');

   private $advParams = array(
   'external_image_list_url' => null,
   'external_link_list_url' => null,
   'template_external_list_url' => null,
   'template_replace_values' => array(),
   'theme_advanced_toolbar_location' => 'external');

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
   'plugins' => array('safari', 'inlinepopups', 'searchreplace', 'contextmenu', 'paste'),
   'theme_advanced_buttons1' => array('bold', 'italic', 'underline', 'strikethrough', '|', 'justifyleft', 'justifycenter', 'justifyright',
   'justifyfull', '|', 'bullist,numlist', '|','search', '|', 'link', 'unlink','|', 'undo', 'redo','code'),
   'theme_advanced_buttons2' => array(),
   'theme_advanced_buttons3' => array());

   /**
    * Parametry pro ořezané advanced THEME
    * @var array
    */
   private $advancedFullParams = array(
   'plugins' => "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager",
   'theme_advanced_buttons1' => "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
   'theme_advanced_buttons2' => "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
   'theme_advanced_buttons3' => "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
   'theme_advanced_buttons4' => "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage"
);

   protected function initJsPlugin() {
   //		Název pluginu
      $this->defaultParams['document_base_url'] = Url_Request::getBaseWebDir();
   }

   protected function setFiles() {
      $this->addJsFile(new JsPlugin_JsFile("tiny_mce.js"));

      switch ($this->getCfgParam('theme')) {
         case 'simple':
            $cfgFile = new JsPlugin_JsFile("settingssimple.js", true);
            break;
         case 'advanced2':
            $cfgFile = new JsPlugin_JsFile("settingsadvanced2.js", true);
            break;
         case 'full':
            $cfgFile = new JsPlugin_JsFile("settingsfull.js", true);
            break;
         case 'advanced1':
         default:
            $cfgFile = new JsPlugin_JsFile("settingsadvanced1.js", true);
            break;
      }


      $this->addJsFile($cfgFile);
   }

   /**
    * Metodda pro generování simple theme
    */
   public function settingsSimpleView() {
      $params = $this->defaultParams;
      $params['document_base_url'] = Url_Request::getBaseWebDir();
      $params['language'] = Locale::getLang();
      $params['theme'] = 'simple';

      $content = $this->cfgFileHeader();
      $content .= $this->generateParamsForFile($params);
      $content .= $this->cfgFileFooter();

      print ($content);
   }

   /**
    * Metodda pro generování advanced 1 theme
    */
   public function settingsSimpleView() {
      $params = $this->defaultParams;
      $params['document_base_url'] = Url_Request::getBaseWebDir();
      $params['language'] = Locale::getLang();
      $params['theme'] = 'simple';

      $content = $this->cfgFileHeader();
      $content .= $this->generateParamsForFile($params);
      $content .= $this->cfgFileFooter();

      print ($content);
   }

   /**
    * Metodda pro generování advanced 1 theme
    */
   public function settingsFullView() {
      $params = $this->defaultParams;
      $params['document_base_url'] = Url_Request::getBaseWebDir();
      $params['language'] = Locale::getLang();

      $params = array_merge($params, $this->advancedFullParams);

      $content = $this->cfgFileHeader();
      $content .= $this->generateParamsForFile($params);
      $content .= $this->cfgFileFooter();

      print ($content);
   }



   // ========================= OLD ===========================



   /**
    * Metda vytvoří výchozí konfigurační soubor
    */
   protected function generateFile(JsPlugin_JsFile $file) {
   //      if($file->getName() == 'tiny_mce_params.js') {
   //         $file->getParam(self::PARAM_THEME) == null ? $theme = null :
   //             $theme = rawurldecode($file->getParam(self::PARAM_THEME));
   //         //         doplnění obsahu s css
   //         if($file->getParam(self::PARAM_FACE) != null) {
   //            $faceUrl = Links::getMainWebDir().Template::FACES_DIR.URL_SEPARATOR.Template::face().URL_SEPARATOR;
   //            $this->defaultParams['content_css'] = $faceUrl.Template::STYLESHEETS_DIR.'/style-tinymce.css';
   //            $this->defaultParams['template_external_list_url'] = $faceUrl.Template::TEMPLATES_DIR.'/tinymce/templates.js';
   //         }
   //         if($file->getParam(self::PARAM_CATEGORY) != null) {
   //            $this->defaultParams['template_replace_values']['categoryName'] = rawurldecode($file->getParam(self::PARAM_CATEGORY));
   //         }
   //         if($file->getParam(self::PARAM_SECTION) != null) {
   //            $this->defaultParams['template_replace_values']['sectionName'] = rawurldecode($file->getParam(self::PARAM_SECTION));
   //         }
   //
   //
   //         if($theme != self::TINY_THEME_SIMPLE) {
   //         //         Doplnění parametru (images, media atd.)
   //            if($file->getParam(self::PARAM_IMAGES)) {
   //               array_push($this->advancedSimpleParams[self::ICONS_ROWS_NAME.$file->getParam(self::PARAM_IMAGES)], self::ICON_IMAGES);
   //               array_push($this->advancedSimpleParams[self::PLUGINS_ARRAY_NAME], self::PLUGIN_IMAGES);
   //            }
   //            if($file->getParam(self::PARAM_MEDIA)) {
   //               array_push($this->advancedSimpleParams[self::ICONS_ROWS_NAME.$file->getParam(self::PARAM_MEDIA)], self::ICON_MEDIA);
   //               array_push($this->advancedSimpleParams[self::PLUGINS_ARRAY_NAME], self::PLUGIN_MEDIA);
   //            }
   //         }
   //
   //         //			Který režim je zobrazen
   //         switch ($theme) {
   //            case self::TINY_THEME_SIMPLE:
   //               $this->generateSimpleCfgFile($file->getParams());
   //               break;
   //            case self::TINY_THEME_ADVANCED_SIMPLE:
   //               $this->generateAdvSimpleCfgFile($file->getParams());
   //               break;
   //            case self::TINY_THEME_ADVANCED:
   //            default:
   //               $this->generateAdvCfgFile($file->getParams());
   //               break;
   //         }
   //      }
   }

   /**
    * Metoda nastaví typ zobrazení pluginu
    */
   public function setMode($mode) {
      $this->getSettingsJsFile()->setParam(self::PARAM_TINY_MODE, $mode);
   }

   /**
    * Metoda nastaví typ zobrazení pluginu
    */
   public function setTheme($theme) {
      $this->getSettingsJsFile()->setParam(self::PARAM_TINY_THEME, $theme);
   }

   /**
    * Metoda nastavuje jestli se mají načíst obrázky a odkud
    * @param string $fileLink -- odkaz
    */
   public function setImagesList($fileLink) {
      $this->getSettingsJsFile()->setParam(self::PARAM_TINY_IMAGES_LIST, $fileLink);
   }

   /**
    * Metoda nastavuje jestli se mají načíst odkazy a odkud
    * @param string $fileLink -- odkaz
    */
   public function setLinksList($fileLink) {
      $this->getSettingsJsFile()->setParam(self::PARAM_TINY_LINKS_LIST, $fileLink);
   }

   /**
    * Metoda přidá iconu pro přidávání obrázků
    * @param int $row = na který řádek v ikonách se má přidat
    */
   public function addImagesIcon($row = 1) {
      $this->getSettingsJsFile()->setParam(self::PARAM_IMAGES, $row);
   }

   /**
    * Metoda přidá iconu pro přidávání medií
    * @param int $row = na který řádek v ikonách se má přidat
    */
   public function addMediaIcon($row = 1) {
      $this->getSettingsJsFile()->setParam(self::PARAM_MEDIA, $row);
   }

   /**
    * Metoda vygeneruje hlavičku souboru
    *
    */
   private function generateCfgFile($fileParams) {
   //      $content = $this->cfgFileHeader();
   //      $params = array_merge($this->defaultParams, $this->advancedParams);
   //      //		Nahrazení parametrů za přenesené
   //      foreach ($fileParams as $param => $value) {
   //         $params[$param] = $value;
   //      }
   //      $params[self::PARAM_THEME] = self::THEME_ADVANCED;
   //      $this->checkImagesList($params);
   //      $this->checkLinksList($params);
   //      $this->removeOtherParams($params);
   //      $content .= $this->generateParamsForFile($params);
   //      $content .= $this->cfgFileFooter();
   //      //		Odeslání souboru
   //      $this->sendFileContent($content);
   }

   /**
    * Metoda vygeneruje hlavičku souboru pro simple theme
    *
    */
   private function generateSimpleCfgFile($fileParams) {
   //      $content = $this->cfgFileHeader();
   //      $params = $this->defaultParams;
   //      //		Nahrazení parametrů za přenesené
   //      foreach ($fileParams as $param => $value) {
   //         $params[$param] = $value;
   //      }
   //      $params[self::PARAM_THEME] = self::THEME_SIMPLE;
   //      $content .= $this->generateParamsForFile($params);
   //      $content .= $this->cfgFileFooter();
   //      //		Odeslání souboru
   //      $this->sendFileContent($content);
   }

   /**
    * Metoda vygeneruje hlavičku souboru pro simple theme
    */
   private function generateAdvSimpleCfgFile($fileParams) {
   //      $content = $this->cfgFileHeader();
   //      $params = array_merge($this->defaultParams, $this->advancedSimpleParams);
   //      //		Nahrazení parametrů za přenesené
   //      foreach ($fileParams as $param => $value) {
   //         $params[$param] = $value;
   //      }
   //      $params[self::PARAM_THEME] = self::THEME_ADVANCED;
   //      $this->checkImagesList($params);
   //      $this->checkLinksList($params);
   //      $this->removeOtherParams($params);
   //      $content .= $this->generateParamsForFile($params);
   //      $content .= $this->cfgFileFooter();
   //      //		Odeslání souboru
   //      $this->sendFileContent($content);
   }

   /**
    * Metoda zkontroluje, jestli nebyl předán i odkaz na list obrázků
    *
    * @param array -- pole, kde se má popřípadě parametr nastavit
    */
   private function checkImagesList(&$params) {
      if(isset ($params[self::PARAM_TINY_IMAGES_LIST]) AND $params[self::PARAM_TINY_IMAGES_LIST] != null) {
         $params['external_image_list_url'] = $params[self::PARAM_TINY_IMAGES_LIST];
         unset($params[self::PARAM_TINY_IMAGES_LIST]);
      }
   }

   /**
    * Metoda zkontroluje, jestli nebyl předán i odkaz na list odkazů
    *
    * @param array -- pole, kde se má popřípadě parametr nastavit
    */
   private function checkLinksList(&$params) {
      if(isset ($params[self::PARAM_TINY_LINKS_LIST]) AND $params[self::PARAM_TINY_LINKS_LIST] != null) {
         $params['external_link_list_url'] = $params[self::PARAM_TINY_LINKS_LIST];
         unset($params[self::PARAM_TINY_LINKS_LIST]);
      }
   }

   /**
    * Metoda odtraní přenesené parametry, které namájí být ve výsledném souboru
    * @param array $params -- pole s parametry
    */
   private function removeOtherParams(&$params) {
      $array = array(self::PARAM_FACE, self::PARAM_IMAGES, self::PARAM_MEDIA,
          self::PARAM_CATEGORY, self::PARAM_SECTION);

      foreach ($array as $param) {
         if(isset ($params[$param])) {
            unset ($params[$param]);
         }
      }
   }

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
            if(is_bool($paramValue)){
               if($paramValue){
                  $v = "true";
               } else {
                  $v = "false";
               }
            } else if(is_int($paramValue)){
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




   //      $string = null;
   //      foreach ($params as $param => $value) {
   //         if(is_array($value)) {
   //            $str = null;
   //            if(empty ($value) OR is_numeric(key($value))) {
   //               $str = null;
   //               foreach ($value as $val) {
   //                  $str .= $val.',';
   //               }
   //               $str = '"'.substr($str, 0, strlen($str)-1).'"';
   //            } else {
   //               $str = null;
   //               foreach ($value as $key => $val) {
   //                  $str .= $key.':"'.$val.'",';
   //               }
   //               $str = "{".substr($str, 0, strlen($str)-1)."}";
   //            }
   //            $string .= "\t".$param.' : '.(string)$str.",\n";
   //         } else if($value != null) {
   //               $string .= "\t".$param.' : "'.(string)$value."\",\n";
   //            } else {
   //               $string .= "\t".$param." : \"\",\n";
   //            }
   //      }
   //      $string = substr($string, 0, strlen($string)-2)."\n";
   //      return $string;
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
    * Metoda vygeneruje z pole string pro list obrázků v TinyMCE
    *
    * @param unknown_type $imagesArray
    */
   public static function sendListImages($imagesArray) {
      $string = "var tinyMCEImageList = new Array(\n";
      foreach ($imagesArray as $name => $path) {
         $string .= "[\"".$name."\", \"".$path."\"],\n";
      }
      if(!empty($imagesArray)) {
         $string = substr($string, 0, strlen($string)-2)."\n";
      }
      $string .= ");\n";
      header("Content-Length: " . strlen($string));
      header("Content-type: application/x-javascript");
      echo $string;
      exit();
   }

   /**
    * Metoda vygeneruje z pole string pro list linků v TinyMCE
    *
    * @param unknown_type $imagesArray
    */
   public static function sendListLinks($imagesArray) {
      $string = "var tinyMCELinkList = new Array(\n";
      foreach ($imagesArray as $name => $path) {
         $string .= "[\"".$name."\", \"".$path."\"],\n";
      }
      if(!empty($imagesArray)) {
         $string = substr($string, 0, strlen($string)-2)."\n";
      }
      $string .= ");\n";
      header("Content-Length: " . strlen($string));
      header("Content-type: application/x-javascript");
      echo $string;
      exit();
   }
}
?>