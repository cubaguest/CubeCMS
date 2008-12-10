<?php
/**
 * Ttřída lokalizačního Controll Helperu.
 * Třída poskytuje metody pro zjednodušení práce s lokalizačními řetězci. Slouží
 * také pro generování a parsování lokalizačních polí. 
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: localectrlhelper.class.php 3.0.55 27.9.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída pro práci s lokalizačními prvky v kontroleru - helper
 */

class LocaleCtrlHelper extends CtrlHelper {
	/**
	 * Separátor mezi elementem a jazykem
	 * @var string
	 */
	const ELEMENT_LANG_SEPARATOR = '_';
	
	/**
	 * SpecialCharsEncode
	 * @var string
	 */
	const SP_CHARS_ENCODE = 'encode'; 
	
	/**
	 * SpecialCharsDecode
	 * @var string
	 */
	const SP_CHARS_DECODE = 'decode'; 

	/**
	 * SpecialCharsNone
	 * @var string
	 */
	const SP_CHARS_NONE = 'none'; 
	
	/**
	 * Konstruktor třídy
	 * Konstruktor třídy
	 *
	 */
	function __construct() {
	}
	
	/**
	 * Metoda vygeneruje pole pro zadávání ve více jazycích (počet jazyků je brán z celého systému)
	 *
	 * @param array -- Pole s elementy (např. label, text)
	 * @param array -- Pole s hodnotymi elemntů 
	 * @param array -- Pole s alternativními hodnotami, pokud hodnota není ve values
	 * @deprecated -- lepší použít přímo validítor formuláře
	 */
	public function generateArray($arrayElements, $values = null, $alternativeValues = null) {
		$returnArray = array();
		
//		Test jestli je pole
		if(!is_array($arrayElements)){
			$arrayElements = array($arrayElements);
		}
		
//		Oddělání posledního znaku určeného pro oddělení elementu a jazyku
		$elements = array();
		foreach ($arrayElements as $el) {
			if($el[strlen($el)-1] == self::ELEMENT_LANG_SEPARATOR){
				array_push($elements, substr($el, 0, strlen($el)-1));
			} else {
				array_push($elements, $el);
			}
		}
		
		foreach (Locale::getAppLangs() as $lang) {
			$returnArray[$lang] = array();
			foreach ($elements as $element) {
				$returnArray[$lang][$element] = null;
				if(isset($values[$element.self::ELEMENT_LANG_SEPARATOR.$lang]) 
//					AND $values[$element.self::ELEMENT_LANG_SEPARATOR.$lang] != null
					){
					$returnArray[$lang][$element] = $values[$element.self::ELEMENT_LANG_SEPARATOR.$lang];
				} else if (isset($alternativeValues[$element.self::ELEMENT_LANG_SEPARATOR.$lang])
//					AND $alternativeValues[$element.self::ELEMENT_LANG_SEPARATOR.$lang] != null
					) {
					$returnArray[$lang][$element] = $alternativeValues[$element.self::ELEMENT_LANG_SEPARATOR.$lang];
				} else {
					$returnArray[$lang][$element] = null;
				}
			}
		}
		return $returnArray;
	}
	
	/**
	 * Metoda převede posty na jazykové pole
	 *
	 * @param array -- pole s názvy postů
	 * @param string -- prefix v názvu postu
	 * @param boolean -- true pokud se mají znaky zakódovat
	 * @param boolean -- true pokud se mají zny dekódovat
	 * @param boolean -- jestli se mají klíče výstupního polu ukládat s prefixem
	 * @return array -- pole upravených postů
	 * @deprecated -- lepší použít přímo validítor formuláře
	 */
	public function postsToArray($sendPostsArray, $postPrefix = null, $specialChars = self::SP_CHARS_ENCODE, $withPrefix = false){
		$returnArray = array();

		if(is_array($specialChars)){
			$specialCharsArray = true;
		} else {
			$specialCharsArray = false;
		}
		
//		Uprava postu o doplnění znaku pro oddělení postu a jazyku
		$postsArray = array();

		if(!is_array($sendPostsArray)){
			$sendPArray[0]=$sendPostsArray;
		} else {
			$sendPArray = $sendPostsArray;
		}

		if(!empty ($sendPArray)){
			foreach ($sendPArray as $post) {
				if($post[strlen($post)-1] == self::ELEMENT_LANG_SEPARATOR){
					array_push($postsArray, $post);
				} else {
					array_push($postsArray, $post.self::ELEMENT_LANG_SEPARATOR);
				}
			}
		}
		
		
		foreach (Locale::getAppLangs() as $lang) {
			foreach ($postsArray as $postKey => $post) {
				if ($_POST[$postPrefix.$post.$lang] != null){
					$sendPost = $_POST[$postPrefix.$post.$lang];

					if($specialCharsArray){
						if($specialChars[$postKey] == self::SP_CHARS_ENCODE){
							$sendPost = htmlspecialchars($sendPost);
						} else if($specialChars[$postKey] == self::SP_CHARS_DECODE){
							$sendPost = htmlspecialchars_decode($sendPost);
						}
					} else {
						if($specialChars == self::SP_CHARS_ENCODE){
							$sendPost = htmlspecialchars($sendPost);
						} else if($specialChars == self::SP_CHARS_DECODE){
							$sendPost = htmlspecialchars_decode($sendPost);
						}
					}
				}else {
					$sendPost = null;
				}
				
				if(!$withPrefix){
					$returnArray[$post.$lang] = $sendPost;
					$value = &$returnArray[$post.$lang];
				} else {
					$returnArray[$postPrefix.$post.$lang] = $sendPost;
					$value = &$returnArray[$postPrefix.$post.$lang];
				}
			}
		}
		return $returnArray;
	}
	
	
}
?>