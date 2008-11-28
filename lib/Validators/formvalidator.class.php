<?php
/**
 * Třída pro validaci formuláře
 * Třída implementuje řešení pro validaci formulářových prvku. Umožňuje kontrolu
 * jejich odeslání, správného vyplnění a zadaných dat. Lze pomocí ní také vybrat
 * data z formuláře a rovnou předat modelu pro zapis. Umožňuje také generování
 * podle jazykového nastavení
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id$ formvalidator.class.php VVE3.3.0 $Revision$
 * @author			$Author$ $Date$
 *						$LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro validaci formulářových prvků
 */
class FormValidator extends Validator {

	const INPUT_NAME			= 'name';
	const INPUT_VALUE			= 'value';
	const INPUT_OBLIGATION	= 'obligation';
	const INPUT_LANGS			= 'langs';
	const INPUT_CODE			= 'code';
	const INPUT_CONTROLL		= 'controll';


	const INPUT_SUBMIT	= 'inputsubmit';
	const INPUT_TEXT		= 'inputtext';
	const INPUT_TEXTAREA	= 'textarea';

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
	 * Objekt modulu
	 * @var Module
	 */
	private $module = null;

	/**
	 * Konstruktor nastaví základní parametry Validátoru
	 */
	function  __construct() {
		$this->module = AppCore::getSelectedModule();
	}

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
	 * @param string $code(null) -- jestli se má prvek překódovávat
	 * @param mixed $specialVerification(null) -- pokud májí být ověřvány ještě další vlastnosti
	 * prvku (email, www, atd). Zadává se přes konstanty třídy a může jich být více v poli
	 *
	 * @return FormValidator -- vrací sama sebe
	 */
	public function inputText($name, $obligation = false, $langs = false, $code = null, $specialVerification = null){
		$inputArray = array ();

		$inputArray[self::INPUT_NAME] = $name;
		$inputArray[self::INPUT_OBLIGATION] = $obligation;
		$inputArray[self::INPUT_LANGS] = $langs;
		$inputArray[self::INPUT_CODE] = $code;
		$inputArray[self::INPUT_CONTROLL] = $specialVerification;

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
	public function textarea($name, $obligation = false, $langs = false, $code = null) {
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

			return true;
		} else {
			return false;
		}
	}

	/**
	 * Metoda postupně spouští funkce pro naplňování a validaci hodnot
	 * v odeslaném formuláři
	 */
	private function fillinFormValues() {
		$allItemsOk = true;
		foreach ($this->formStructure as $itemKey => $item) {
			//			Spuštění funkce pro validaci prvku
			$metodName = 'fillin'.ucfirst($itemKey).'Values';
			if(method_exists($this, $metodName)){
				$allItemsOk = $this->{$metodName}();
			} else {
				new CoreException(_('Požadovaná funkce ').$metodName._(' pro parsování formuláře neexistuje. Zřejmně nebyla implementována'),1);
			}
		}
	}

	/**
	 * Metoda parsuje a kontroluje prvky typu input-text
	 */
	private function fillinInputtextValues() {
		
	}

	/**
	 * Metoda parsuje a kontroluje prvek typu textarea
	 */
	private function fillinTextareaValues() {
		;
	}

	/**
	 * Metoda pracuje s prvkem input typu submit. Měla by ústat prázdná, protože
	 * se jedná o tláčítko pro odeslání formuláře
	 */
	private function fillinInputsubmitValues() {}
}
?>
