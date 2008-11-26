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
	const GET_TINY_THEME = 'theme';
	
	/**
	 * $GET s modem tinymce
	 * @var string
	 */
	const GET_TINY_MODE = 'mode';
	
	/**
	 * $GET s odkazem na list obrázků
	 * @var string
	 */
	const GET_TINY_IMAGES_LIST = 'img';
	
	/**
	 * Druhy theme pro tiny mce
	 * @var string
	 */
	const TINY_THEME_SIMPLE = 'simple';
	const TINY_THEME_ADVANCED = 'advanced';
	const TINY_THEME_FULL = 'full';
	
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
			'theme' => 'advanced',
			'language' => 'cs',
			'plugins' => array('safari', 'style', 'table', 'save', 'advhr', 'advimage', 'advlink', 'emotions', 'iespell', 'inlinepopups',
					'insertdatetime', 'preview', 'media', 'searchreplace', 'print', 'contextmenu', 'paste', 'directionality', 'fullscreen',
					'noneditable', 'visualchars', 'nonbreaking', 'xhtmlxtras'),
			'theme_advanced_buttons1' => array('bold', 'italic', 'underline', 'strikethrough', '|', 'justifyleft', 'justifycenter', 'justifyright',
					'justifyfull', '|', 'formatselect', 'fontselect', 'fontsizeselect', '|', 'preview', 'fullscreen'),
			'theme_advanced_buttons2' => array('cut', 'copy', 'paste', 'pastetext', '|', 'search,replace', '|', 'bullist,numlist', '|', 'outdent',
					'indent,blockquote', '|', 'undo', 'redo', '|', 'link', 'unlink', 'anchor', 'cleanup', 'code', '|', 'inserttime', '|',
					'forecolor', 'backcolor'),
			'theme_advanced_buttons3' => array('tablecontrols', '|', 'hr', 'removeformat', 'visualaid', '|', 'sub', 'sup', '|', 'charmap',
					'emotions', 'image', 'media', '|', 'ltr', 'rtl'),
			'theme_advanced_toolbar_location' => 'top',
			'theme_advanced_toolbar_align' => 'left',
			'theme_advanced_statusbar_location' => 'bottom',
			'theme_advanced_resizing' => 'true',
			'entity_encoding' => 'raw',
			'encoding' => 'xml',
			'document_base_url' => null,
			'external_image_list_url' => null,
			'remove_script_host' => 'false',
//			'relative_urls' => 'false'
			);
	
	/**
	 * Enter description here...
	 * @var string
	 */
	private $fileContent = null;
	
	protected function initJsPlugin() {
//		Název pluginu
		$this->setJsPluginName("TinyMCE");
		
//		nastavení jazyka TinyMCE
		$this->setParam('language', Locale::getLang());
		$this->defaultParams['document_base_url'] = Links::getMainWebDir();
	}
	
	protected function initFiles() {
//		Výchozí js soubor pluginu
		$this->setSettingJsFile(new JsPluginJsFile("tiny_mce_default.js"));
		
//		Přidání js soubrů pluginu
		$this->addJsFile(new JsPluginJsFile("tiny_mce.js"));
	}
	
	/**
	 * Metda vytvoří výchozí konfigurační soubor
	 */
	protected function generateFile() {
		if($this->getFileName() == 'tiny_mce_default.js'){
			isset($_GET[self::GET_TINY_THEME]) == false ? $theme = null : $theme = rawurldecode($_GET[self::GET_TINY_THEME]);
//			Který režim je zobrazen		
			switch ($theme) {
				case self::TINY_THEME_SIMPLE:
					break;
				
				case self::TINY_THEME_ADVANCED:
				default:
					$this->generateAdvCfgFile();
					break;
			}
		}
	}
	
	/**
	 * Metoda nastaví typ zobrazení pluginu
	 *
	 */
	public function setMode($mode){
		$this->setParam(self::GET_TINY_MODE, $mode);
	}

	/**
	 * Metoda nastaví typ zobrazení pluginu
	 *
	 */
	public function setTheme($theme){
		$this->setParam(self::GET_TINY_THEME, $theme);
	}
	
	/**
	 * Metoda nastavuje jestli se načíst obrázky a odkud
	 * @param integer -- id itemu
	 * @param integer -- id článku
	 */
	public function setImagesList($fileLink) {
		$this->setParam(self::GET_TINY_IMAGES_LIST, $fileLink);
	}
	
	/**
	 * Metoda vygeneruje hlavičku souboru
	 *
	 */
	private function generateAdvCfgFile() {
		$content = $this->cfgFileHeader();
		
		$params = $this->defaultParams;
		
//		Nahrazení parametrů za přenesené
		foreach ($this->getFileParams() as $param => $value) {
			$params[$param] = $value;
		}
		
		$this->checkImagesList($params);
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
		if(isset($_GET[self::GET_TINY_IMAGES_LIST])){
			$params['external_image_list_url'] = $this->getFileParam(self::GET_TINY_IMAGES_LIST);
			unset($params[self::GET_TINY_IMAGES_LIST]);
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
