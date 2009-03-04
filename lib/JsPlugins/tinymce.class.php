<?php
/**
 * Třída JsPluginu TinyMce.
 * Třída slouží pro vytvoření JsPluginu TinyMce, což je wysiwing textový edtor,
 * který se navazuje na textarea v šabloně. Třída umožňuje jednoduché nastavení 
 * parametrů editor (volba vzhledu, jazyka, atd).
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: tinymce.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída JsPluginu TinyMce
 */

class TinyMce extends JsPlugin {
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
	 * výchozí parametry tinyMCE
	 */
	private $defaultParams = array(
			'mode' => 'textareas',
			self::PARAM_THEME => 'advanced',
			'language' => 'cs',
			'theme_advanced_toolbar_location' => 'top',
			'theme_advanced_toolbar_align' => 'left',
			'theme_advanced_statusbar_location' => 'bottom',
			'theme_advanced_resizing' => 'true',
			'entity_encoding' => 'raw',
			'encoding' => 'xml',
			'document_base_url' => null,
			'external_image_list_url' => null,
			'remove_script_host' => 'false',
			'content_css' => null
//			'relative_urls' => 'false'
			);

   private $advancedParams = array(
         self::PLUGINS_ARRAY_NAME => array('safari', 'style', 'table', 'save', 'advhr', self::PLUGIN_IMAGES, 'advlink', 'emotions', 'iespell', 'inlinepopups',
					'insertdatetime', 'preview', self::PLUGIN_MEDIA, 'searchreplace', 'print', 'contextmenu', 'paste', 'directionality', 'fullscreen',
					'noneditable', 'visualchars', 'nonbreaking', 'xhtmlxtras'),
			'theme_advanced_buttons1' => array('bold', 'italic', 'underline', 'strikethrough', '|', 'justifyleft', 'justifycenter', 'justifyright',
					'justifyfull', '|', 'formatselect', 'fontselect', 'fontsizeselect', '|', 'preview', 'fullscreen'),
			'theme_advanced_buttons2' => array('cut', 'copy', 'paste', 'pastetext', '|', 'search,replace', '|', 'bullist,numlist', '|', 'outdent',
					'indent,blockquote', '|', 'undo', 'redo', '|', 'link', 'unlink', 'anchor', 'cleanup', 'code', '|', 'inserttime', '|',
					'forecolor', 'backcolor'),
			'theme_advanced_buttons3' => array('tablecontrols', '|', 'hr', 'removeformat', 'visualaid', '|', 'sub', 'sup', '|', 'charmap',
					'emotions', self::ICON_IMAGES , self::ICON_MEDIA, '|', 'ltr', 'rtl'));

   private $advancedSimpleParams = array(
         self::PLUGINS_ARRAY_NAME => array('safari', 'inlinepopups', 'searchreplace', 'contextmenu', 'paste'),
			'theme_advanced_buttons1' => array('bold', 'italic', 'underline', 'strikethrough', '|', 'justifyleft', 'justifycenter', 'justifyright',
					'justifyfull', '|', 'bullist,numlist', '|','search', '|', 'link', 'unlink','|', 'undo', 'redo','code'),
			'theme_advanced_buttons2' => array(),
			'theme_advanced_buttons3' => array());

	protected function initJsPlugin() {
//		Název pluginu
		$this->setJsPluginName("TinyMCE");
		
		$this->defaultParams['document_base_url'] = Links::getMainWebDir();
	}
	
	protected function initFiles() {
//		Výchozí js soubor pluginu
      $jsFile = new JsFile("tiny_mce_params.js", true);
      $face = AppCore::getTepmlateFaceDir(false,false);
      $face = substr($face, 0, strlen($face)-1);
      $jsFile->setParam(self::PARAM_FACE, $face);
      $jsFile->setParam('language', Locale::getLang());
		$this->setSettingJsFile($jsFile);
		
//		Přidání js soubrů pluginu
		$this->addJsFile(new JsFile("tiny_mce.js"));
	}
	
	/**
	 * Metda vytvoří výchozí konfigurační soubor
	 */
	protected function generateFile(JsFile $file) {
      if($file->getName() == 'tiny_mce_params.js'){
			$file->getParam(self::PARAM_THEME) == null ? $theme = null :
            $theme = rawurldecode($file->getParam(self::PARAM_THEME));

//         doplnění obsahu s css
         if($file->getParam(self::PARAM_FACE) != null){
            $cssurl = Links::getMainWebDir().AppCore::getTepmlateFaceDir(false);
            $cssurl = substr($cssurl, 0, strlen($cssurl)-1).$file->getParam(self::PARAM_FACE).URL_SEPARATOR;
            $this->defaultParams['content_css'] = $cssurl.AppCore::TEMPLATES_STYLESHEETS_DIR.'/style_tiny_mce.css';
         }

         if($theme != self::TINY_THEME_SIMPLE){
            //         Doplnění parametru (images, media atd.)
            if($file->getParam(self::PARAM_IMAGES)){
               array_push($this->advancedSimpleParams[self::ICONS_ROWS_NAME.$file->getParam(self::PARAM_IMAGES)], self::ICON_IMAGES);
               array_push($this->advancedSimpleParams[self::PLUGINS_ARRAY_NAME], self::PLUGIN_IMAGES);
            }

            if($file->getParam(self::PARAM_MEDIA)){
               array_push($this->advancedSimpleParams[self::ICONS_ROWS_NAME.$file->getParam(self::PARAM_MEDIA)], self::ICON_MEDIA);
               array_push($this->advancedSimpleParams[self::PLUGINS_ARRAY_NAME], self::PLUGIN_MEDIA);
            }
         }

//			Který režim je zobrazen		
			switch ($theme) {
				case self::TINY_THEME_SIMPLE:
               $this->generateSimpleCfgFile($file->getParams());
					break;
				case self::TINY_THEME_ADVANCED_SIMPLE:
               $this->generateAdvSimpleCfgFile($file->getParams());
               break;
				case self::TINY_THEME_ADVANCED:
				default:
					$this->generateAdvCfgFile($file->getParams());
					break;
			}
		}
	}
	
	/**
	 * Metoda nastaví typ zobrazení pluginu
	 *
	 */
	public function setMode($mode){
      $this->getSettingsJsFile()->setParam(self::PARAM_TINY_MODE, $mode);
	}

	/**
	 * Metoda nastaví typ zobrazení pluginu
	 *
	 */
	public function setTheme($theme){
      $this->getSettingsJsFile()->setParam(self::PARAM_TINY_THEME, $theme);
	}
	
	/**
	 * Metoda nastavuje jestli se načíst obrázky a odkud
	 * @param integer -- id itemu
	 * @param integer -- id článku
	 */
	public function setImagesList($fileLink) {
      $this->getSettingsJsFile()->setParam(self::PARAM_TINY_IMAGES_LIST, $fileLink);
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
	private function generateAdvCfgFile($fileParams) {
		$content = $this->cfgFileHeader();
      $params = array_merge($this->defaultParams, $this->advancedParams);

//		Nahrazení parametrů za přenesené
		foreach ($fileParams as $param => $value) {
			$params[$param] = $value;
		}

      $params[self::PARAM_THEME] = self::THEME_ADVANCED;
		
		$this->checkImagesList($params);
      $this->removeOtherParams($params);
		$content .= $this->generateParamsForFile($params);
		$content .= $this->cfgFileFooter();
//		Odeslání souboru
		$this->sendFileContent($content);
	}

	/**
	 * Metoda vygeneruje hlavičku souboru pro simple theme
	 *
	 */
	private function generateSimpleCfgFile($fileParams) {
		$content = $this->cfgFileHeader();

		$params = $this->defaultParams;

//		Nahrazení parametrů za přenesené
		foreach ($fileParams as $param => $value) {
			$params[$param] = $value;
		}

      $params[self::PARAM_THEME] = self::THEME_SIMPLE;

		$content .= $this->generateParamsForFile($params);
		$content .= $this->cfgFileFooter();
//		Odeslání souboru
		$this->sendFileContent($content);
	}

	/**
	 * Metoda vygeneruje hlavičku souboru pro simple theme
	 *
	 */
	private function generateAdvSimpleCfgFile($fileParams) {
		$content = $this->cfgFileHeader();

		$params = array_merge($this->defaultParams, $this->advancedSimpleParams);

//		Nahrazení parametrů za přenesené
		foreach ($fileParams as $param => $value) {
			$params[$param] = $value;
		}

      $params[self::PARAM_THEME] = self::THEME_ADVANCED;

      $this->checkImagesList($params);
      $this->removeOtherParams($params);

		$content .= $this->generateParamsForFile($params);
		$content .= $this->cfgFileFooter();
//		Odeslání souboru
		$this->sendFileContent($content);
	}
	
	/**
	 * Metoda zkontroluje, jestli nebyl předán i odkaz na list obrázků
	 *
	 * @param array -- pole, kde se má popřípadě parametr nastavit
	 */
	private function checkImagesList(&$params) {
      if(isset ($params[self::PARAM_TINY_IMAGES_LIST]) AND $params[self::PARAM_TINY_IMAGES_LIST] != null){
			$params['external_image_list_url'] = $params[self::PARAM_TINY_IMAGES_LIST];
			unset($params[self::PARAM_TINY_IMAGES_LIST]);
		}
	}

   /**
    * Metoda odtraní přenesené parametry, které namájí být ve výsledném souboru
    * @param array $params -- pole s parametry
    */
   private function removeOtherParams(&$params) {
      if(isset ($params[self::PARAM_FACE])){
         unset ($params[self::PARAM_FACE]);
      }
      if(isset ($params[self::PARAM_IMAGES])){
         unset ($params[self::PARAM_IMAGES]);
      }
      if(isset ($params[self::PARAM_MEDIA])){
         unset ($params[self::PARAM_MEDIA]);
      }
   }
	
	/**
	 * Metoda vygeneruje řetězec s parametry
	 *
	 * @param array -- pole parametrů
	 * @return string -- řetězec s generovaným souborem
	 */
	private function generateParamsForFile($params) {
		$string = null;
		
		foreach ($params as $param => $value) {
			if(is_array($value)){
				$str = null;
				foreach ($value as $val) {
					$str .= $val.',';
				}
				$value = substr($str, 0, strlen($str)-1);
			}
			
			if($value == 'false' OR $value == 'true'){
				$string .= "\t".$param.' : '.$value.",\n";
			} else if($value != null){
				$string .= "\t".$param.' : "'.$value."\",\n";
			} else {
            $string .= "\t".$param." : \"\",\n";
         }
		}
		$string = substr($string, 0, strlen($string)-2)."\n";
		return $string;
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
	public static function generateListImages($imagesArray) {
		$string = "var tinyMCEImageList = new Array(\n";
		foreach ($imagesArray as $name => $path) {
			$string .= "[\"".$name."\", \"".$path."\"],\n";
		}
		if(!empty($imagesArray)){
			$string = substr($string, 0, strlen($string)-2)."\n";
		}
		$string .= ");\n";
		return $string;
	}
}

?>
