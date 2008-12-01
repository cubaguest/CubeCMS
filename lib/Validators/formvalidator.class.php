<?php
/**
 * Třída pro validaci formuláře
 * Třída implementuje řešení pro validaci formulářových prvku. Umožňuje kontrolu
 * jejich odeslání, správného vyplnění a zadaných dat. Lze pomocí ní také vybrat
 * data z formuláře a rovnou předat modelu pro zápis. Umožňuje také generování
 * podle jazykového nastavení
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id$ VVE3.3.0 $Revision$
 * @author			$Author$ $Date$
 *						$LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro validaci formulářových prvků
 * @todo				Dodělat další validace, implementovat ostatní prvky formulářů
 */
class FormValidator extends Validator {
	/**
	 * Názvy parametrů formuláře
	 */
	const INPUT_NAME			= 'name';
	const INPUT_VALUE			= 'value';
	const INPUT_OBLIGATION	= 'obligation';
	const INPUT_LANGS			= 'langs';
	const INPUT_CODE			= 'code';
	const INPUT_VALIDATION	= 'validation';

	/**
	 * Názvy prvků ve formuláři
	 */
	const INPUT_SUBMIT	= 'inputsubmit';
	const INPUT_TEXT		= 'inputtext';
	const INPUT_TEXTAREA	= 'textarea';

	/**
	 * Způsob kódování přenesených dat
	 * bez kódování html zanků
	 * @var int
	 */
	const CODE_NONE = 0;

	/**
	 * Způsob kódování přenesených dat
	 * zakódování html zanků
	 * @var int
	 */
	const CODE_HTMLENCODE = 1;

	/**
	 * Způsob kódování přenesených dat
	 * dekódování html zanků
	 * @var int
	 */
	const CODE_HTMLDECODE = 2;

	/**
	 * Parametr pro žádnou validací
	 * @var int
	 */
	const VALIDATION_NONE = 0;

	/**
	 * Parametr pro validaci emailu
	 * @var int
	 */
	const VALIDATION_EMAIL = 1;

	/**
	 * Proměná pro zapnutí validace data
	 * @var int
	 */
	const VALIDATE_DATE = 2;

	/**
	 * Proměná pro validaci času
	 * @var int
	 */
	const VALIDATE_TIME = 3;

	/**
	 * Pole se strukturou formuláře
	 * @var array
	 */
	private $formStructure = array(self::INPUT_SUBMIT => null,
											 self::INPUT_TEXT => array(),
											 self::INPUT_TEXTAREA => array());

	/**
	 * Prefix formulářových prvků
	 * @var string
	 */
	private $formPrefix = null;

	/**
	 * Pole s hodnotami formuláře s prefixy
	 * @var array
	 */
	private $formValuesWithPrefix = array();
	/**
	 *
	 * Pole s hodnotami formuláře bez prefixů
	 * @var array
	 */
	private $formValues = array();

	/**
	 * Proměná obsahuje true pokud chybí nějáká povinná proměnná
	 * @var boolean
	 */
	private $missingValues = false;

	/**
	 * Proměná obsahuje true při neplatném zadání emailu
	 * @var boolean
	 */
	private $missingEmail = false;

	/**
	 * Proměná obsahuje jestli byla při kontrole formuláře někde chyba
	 * @var boolean
	 */
	private $someError = false;

	/**
	 * Metoda nastavuje prefix použitý ve formuláři
	 * @param string $prefix -- prefix formulářových prvků
	 */
	public function setPrefix($prefix) {
		$this->formPrefix = $prefix;
	}

	/**
	 * Metoda zařadí název potvrzovacího prvku (input submit)
	 *
	 * @param string $name -- název potvrzovacího submit inputu
	 * @return FormValidator -- vrací sama sebe
	 */
	public function inputSubmit($name) {
		$this->formStructure[self::INPUT_SUBMIT] = $name;
		return $this;
	}

	/**
	 * Metoda přidá prvek typu text
	 * @param string $name -- název prvku
	 * @param boolean $obligation(false) -- povinnost zadání prvku
	 * @param boolean $langs(false) -- jestli se má generovat pro všechny jazykové mutace
	 * @param int $specialValidation(self::VALIDATION_NONE) -- pokud májí být ověřvány ještě další vlastnosti
	 * @param int $code(self::CODE_HTMLENCODE) -- jak se má prvek překódovávat
	 * prvku (email, www, atd). Zadává se přes konstanty třídy a může jich být více v poli
	 *
	 * @return FormValidator -- vrací sama sebe
	 */
	public function inputText($name, $obligation = false, $langs = false, $specialValidation = self::VALIDATION_NONE, $code = self::CODE_HTMLENCODE){
		$inputArray = array ();

		$inputArray[self::INPUT_NAME] = $name;
		$inputArray[self::INPUT_OBLIGATION] = $obligation;
		$inputArray[self::INPUT_LANGS] = $langs;
		$inputArray[self::INPUT_CODE] = $code;
		$inputArray[self::INPUT_VALIDATION] = $specialValidation;

		array_push($this->formStructure[self::INPUT_TEXT], $inputArray);
		return $this;
	}

	/**
	 * Metoda vytvoří prvek typu textarea
	 * @param string $name -- název prvku
	 * @param boolean $obligation(false) -- povinnost zadání prvku
	 * @param boolean $langs(false) -- jestli se má generovat pro všechny jazykové mutace
	 * @param string $code(null) -- jestli se má prvek překódovávat
	 * @return FormValidator -- vrací sama sebe
	 */
	public function textarea($name, $obligation = false, $langs = false, $code = self::CODE_HTMLENCODE) {
		$inputArray = array ();

		$inputArray[self::INPUT_NAME] = $name;
		$inputArray[self::INPUT_OBLIGATION] = $obligation;
		$inputArray[self::INPUT_LANGS] = $langs;
		$inputArray[self::INPUT_CODE] = $code;
		array_push($this->formStructure[self::INPUT_TEXTAREA], $inputArray);
		return $this;
	}

	/**
	 * Metoda kontroluje, jesli byl zadaný formulář odeslán
	 * @return boolean -- true pokud byl formulář odeslán
	 */
	public function checkForm() {
		if(isset ($_POST[$this->formPrefix.$this->formStructure[self::INPUT_SUBMIT]])){
			$this->fillinFormValues();
			return !$this->someError;
		} else {
			return false;
		}
	}

	/**
	 * Metoda postupně spouští funkce pro naplňování a validaci hodnot
	 * v odeslaném formuláři
	 */
	private function fillinFormValues() {
		foreach ($this->formStructure as $itemKey => $item) {
			//			Spuštění funkce pro validaci prvku
			$metodName = 'fillin'.ucfirst($itemKey).'Values';
			if(method_exists($this, $metodName)){
					$this->{$metodName}();
			} else {
				new CoreException(_('Požadovaná funkce ').$metodName._(' pro parsování formuláře neexistuje. Zřejmně nebyla implementována'),1);
			}
		}
//		return $allItemsOk;
//		print_r($this->formValues);
//		echo("<pre>");
//		print_r($_POST);
//		echo("</pre>");
	}

	/**
	 * Metoda parsuje a kontroluje prvky typu input-text
	 */
	private function fillinInputtextValues() {
		//		Procházení jednotlivých prvků formuláře a parsování do pole
		foreach ($this->formStructure[self::INPUT_TEXT] as $item) {
			if(isset ($_POST[$this->formPrefix.$item[self::INPUT_NAME]])){
				$post = $_POST[$this->formPrefix.$item[self::INPUT_NAME]];

				//	pokud je předána pouze jedna hodnota
				if(!is_array($post)){
//					pokud je prvek povinný
					if($item[self::INPUT_OBLIGATION] AND empty ($post)){
						$this->addMissingError();
						$this->addFormValue($item[self::INPUT_NAME], null);
						continue;
					} else {
						$value = $this->codeStrings($post, $item[self::INPUT_CODE]);
						$this->validateItem($value, $item[self::INPUT_VALIDATION]);
						$this->addFormValue($item[self::INPUT_NAME], $value);
					}
				}
				//	pokud je předáno pole
				else {
//					Pokud je zadáno jazykové pole
					if(isset ($post[Locale::getDefaultLang()])){
						if($item[self::INPUT_OBLIGATION] AND empty($post[Locale::getDefaultLang()])){
							$this->addMissingError();
							$this->addFormValue($item[self::INPUT_NAME], $this->codeStrings($post, $item[self::INPUT_CODE]));
							continue;
						} else {
							$value = $this->codeStrings($post, $item[self::INPUT_CODE]);
							$this->validateItem($value, $item[self::INPUT_VALIDATION]);
							$this->addFormValue($item[self::INPUT_NAME], $value);
						}
					}
//					Jakékoliv jiné pole
					else {
						if($item[self::INPUT_OBLIGATION]){
							foreach ($post as $key => $var) {
								if(empty($post[$key])){
									$this->addMissingError();
									break(1);
								}
							}
						}
						$value = $this->codeStrings($post, $item[self::INPUT_CODE]);
						$this->validateItem($value, $item[self::INPUT_VALIDATION]);
						$this->addFormValue($item[self::INPUT_NAME], $value);
					}
				}
			} else {
				$this->addFormValue($item[self::INPUT_NAME], null);
			}
		}
	}

	/**
	 * Metoda parsuje a kontroluje prvek typu textarea
	 */
	private function fillinTextareaValues() {
		//		Procházení jednotlivých prvků formuláře
		foreach ($this->formStructure[self::INPUT_TEXTAREA] as $item) {
			if(isset ($_POST[$this->formPrefix.$item[self::INPUT_NAME]])){
				$post = $_POST[$this->formPrefix.$item[self::INPUT_NAME]];
				//	pokud je předána pouze jedna hodnota
				if(!is_array($post)){
//					pokud je prvek povinný
					if($item[self::INPUT_OBLIGATION] AND empty($post)){
						$this->addMissingError();
						$this->addFormValue($item[self::INPUT_NAME], null);
						continue;
					} else {
						$this->addFormValue($item[self::INPUT_NAME], $post);
					}
				}
				//	pokud je předáno pole
				else {
//					Pokud je zadáno jazykové pole
					if(isset ($post[Locale::getDefaultLang()])){
						if($item[self::INPUT_OBLIGATION] AND empty($post[Locale::getDefaultLang()])){
							$this->addMissingError();
							$this->addFormValue($item[self::INPUT_NAME], null);
							continue;
						} 
						$this->addFormValue($item[self::INPUT_NAME], $post);
					}
//					Jakékoliv jiné pole
					else {
						if($item[self::INPUT_OBLIGATION]){
							foreach ($post as $key => $var) {
								if(empty($post[$key])){
									$this->addMissingError();
									break(2);
								}
							}
						}
						$this->addFormValue($item[self::INPUT_NAME], $post);
					}
				}
			} else {
				$this->addFormValue($item[self::INPUT_NAME], null);
//				$return = false;
			}
		}
	}

	/**
	 * Metoda pracuje s prvkem input typu submit. Měla by ústat prázdná, protože
	 * se jedná o tláčítko pro odeslání formuláře
	 */
	private function fillinInputsubmitValues() {}

	/**
	 * Metoda vrací objekt modulu
	 * @return Module -- objekt modulu
	 */
	private function getModule() {
		return $this->module;
	}

	/**
	 * Metoda přidá prvek do pole hodnot
	 * @param mixed -- prvke
	 */
	private function addFormValue($name, $value) {
		$this->formValues[$name] = $value;
		$this->formValuesWithPrefix[$this->formPrefix.$name] = $value;
	}

	/**
	 * Metoda zakóduje požadovanou hodnotu podle zvoleného kódování
	 * (umí zpracovávat i pole)
	 * @param mixed $value -- řetězec nepo pole které se má kódovat
	 * @return mixed -- překódovaná hodnota
	 */
	private function codeStrings(&$value, $code) {
		if(is_array($value)){
			foreach ($value as $key => $val) {
				if(is_array($val)){
					$this->codeStrings($value[$key], $code);
				} else {
					$value[$key] = $this->codeString($val, $code);
				}
			}
		} else {
			$value = $this->codeString($value, $code);
		}
		return $value;
	}

	/**
	 * Metoda překóduje zadaný řetězec podle nastaveného kódování
	 * @param string $string -- řetězec, který se má překódovat
	 * @param int $code -- kód překódování
	 * @return string -- překódovaný řetězec
	 */
	private function codeString(&$string, $code){
		switch ($code) {
			case self::CODE_HTMLDECODE:
					$string = htmlspecialchars_decode($string, ENT_QUOTES);
				break;
			case self::CODE_HTMLENCODE:
					$string = htmlspecialchars($string, ENT_QUOTES);
				break;
			default://do nothing
				break;
		}
		return $string;
	}

	/**
	 * Metoda pro vytvoření chbové hlášky v případě nevyplnění jednoho
	 * z povinných údajů
	 */
	private function addMissingError() {
		if(!$this->missingValues){
			$this->errMsg()->addMessage(_('Nebyly zadány všechny povinné údaje'));
			$this->missingValues = true;
			$this->someError = true;
		}
	}

	/**
	 * Metoda zavolá funkci pro danou validaci
	 * @param mixed $itemValue -- hodnota prvku
	 * @param int $validationType -- typ validace s konstanty VALIDATE_...
	 */
	private function validateItem($itemValue, $validationType) {
		switch ($validationType) {
			case self::VALIDATION_EMAIL:
				if(!$this->validateItemMail($itemValue)){
					$this->someError = true;
				}
				break;
			case self::VALIDATE_DATE:
			case self::VALIDATE_TIME:
			case self::VALIDATION_NONE:
			default:
				return true;
				break;
		};
	}

	/**
	 * Metoda provede validaci emailu
	 * @param string $value -- email
	 */
	private function validateItemMail($email) {
		$urlVal = new UrlValidator();

		if($urlVal->checkMail($email)){
			return true;
		} else {
			if(!$this->missingEmail){
				$this->errMsg()->addMessage(_('Nebyly zadána korektní emailová adresa'));
				$this->missingEmail = true;
			}
			return false;
		}
	}

	/**
	 * Metoda vrací hodnoty formuláře ja pole hodnot
	 *
	 * @param boolean $oneArray(option) -- true pokud má být vráceno pole s jednou hloubkou,
	 * všechny indexy podpolí budo sloučeny s hlavními indexy pomocí operátoru
	 * @param boolean $withPrefix(option) -- jestli do indexů bude přidán také prefix formuláře
	 * @param string $operator(option) -- oddělovací operátor mezi indexy při slučování
	 */
	public function getFormValues($oneArray = false, $withPrefix = false, $operator = '_') {
		if($withPrefix){
			$valuesArray = $this->formValuesWithPrefix;
		} else {
			$valuesArray = $this->formValues;
		}
		if(!$oneArray){
			return $valuesArray;
		} else {
			return $this->createValuesArray($valuesArray, $operator);
		}
	}

	/**
	 * Metoda vygeneruje pole s jednou vrstvou, Vložení pole se prochází
	 * rekurzivně a klíče se spojují
	 * @param array $value -- hodnoty, které se mají zapsat
	 * @param string $separator -- separátor mezi klíči
	 * @param string $previousKey(internal) -- předchozí část klíče
	 * @param array $valuesArray(internal) -- ukazatel na vnitřní pole
	 * @return array -- jednovrstvé pole s hodnotami
	 * @todo hodila by se optimalizace, kvůli předávání pole ukazatelem
	 */
	private function createValuesArray($value, $separator = '_', $previousKey = null, &$valuesArray = null) {
		if(empty ($valuesArray)){
			$valuesArray = array ();
		}

		foreach ($value as $key => $var) {
			if(is_array($var)){
				if(!empty ($previousKey)){
					$nextKey = $previousKey.$separator.$key;
				} else {
					$nextKey = $key;
				}
				$this->createValuesArray($var, $separator, $nextKey, $valuesArray);
			} else {
				if(empty ($previousKey)){
					$valuesArray[$key] = $var;
				} else {
					$valuesArray[$previousKey.$separator.$key] = $var;
				}
			}
		}
		return $valuesArray;
	}
}
?>
