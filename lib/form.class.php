<?php
/**
 * Třída pro obsluhu formuláře
 * Třída implementuje řešení pro obsluhu formulářových prvku. Umožňuje kontrolu
 * jejich odeslání, správného vyplnění zadaných dat, jejich načtení a upráva.
 * Lze pomocí ní také vybrat data z formuláře a rovnou předat modelu pro zápis.
 * Umožňuje také generování podle jazykového nastavení
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: form.class.php 7 2009-01-21 21:32:52Z jakub $ VVE3.5.0 $Revision: 7 $
 * @author        $Author: jakub $ $Date: 2009-01-21 21:32:52 +0000 (St, 21 led 2009) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2009-01-21 21:32:52 +0000 (St, 21 led 2009) $
 * @abstract      Třída pro obsluhu formulářových prvků
 * @todo          Dodělat další validace, implementovat ostatní prvky formulářů
 */
class Form {
 /**
  * Názvy parametrů formuláře
  */
   const ITEM_NAME			= 'name';
   const ITEM_VALUE			= 'value';
   const ITEM_OBLIGATION	= 'obligation';
   const ITEM_LANGS			= 'langs';
   const ITEM_CODE			= 'code';
   const ITEM_VALIDATION	= 'validation';
   const ITEM_MAX_LENGHT	= 'maxlenght';
   const ITEM_MIN_LENGHT	= 'minlenght';

  /**
   * Názvy prvků ve formuláři
   */
   const INPUT_SUBMIT	= 'inputsubmit';
   const INPUT_TEXT		= 'inputtext';
   const INPUT_HIDDEN	= 'inputhidden';
   const INPUT_PASSWORD	= 'inputpasswd';
   const INPUT_CHECKBOX	= 'inputcheckbox';
   const INPUT_FILE     = 'inputfile';
   const INPUT_DATE     = 'inputdate';
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
   * Proměná pro zapnutí validace data a datum není omezené
   * @var int
   */
   const VALIDATE_DATE_ISEVERYTIME = 'E';


  /**
   * Proměná pro zapnutí validace data a datum musí být v budoucnosti
   * @var int
   */
   const VALIDATE_DATE_ISFUTURE = 'F';

  /**
   * Proměná pro zapnutí validace data a datum musí být v minulosti
   * @var int
   */
   const VALIDATE_DATE_ISPAST = 'P';


  /**
   * Index pro chybějící zprávy
   */
   const ERROR_MISSING = 'missing';

   /**
	 * Název proměné s originálním názvem souboru
	 * @var string
	 */
	const POST_FILES_ERROR 			= 'error';
	const POST_FILES_ORIGINAL_NAME	= 'name';
	const POST_FILES_SIZE 			= 'size';
	const POST_FILES_TYPE 			= 'type';
	const POST_FILES_TMP_NAME		= 'tmp_name';

  /**
   * Proměná obsahuje, jestli bylo v zadání formuláře chyba
   * @var boolean
   */
   private $isError = false;

  /**
   * Poles chbějícími prvky
   * @deprecated
   */
//   private $errorMessages = array(self::ERROR_MISSING => false);

  /**
   * Název prvků, ve kterých byla chyba
   * @var array
   */
   private $errorItems = array();

  /**
  * Obejt pro informační hlášky
  * @var Messages
  */
   private $infomsg = null;

 /**
  * Obejt pro chybové hlášky hlášky
  * @var Messages
  */
   private $errmsg = null;

 /**
  * Objekt modulu
  * @var Module
  */
   private $module = null;

  /**
   * Prefix pro formulářové prvky první úrovně
   * @var string
   */
   private $formPrefix = null;

  /**
   * Struktury a hodnoty prvků
   */

  /**
   * pole se strukturou formuláře
   * @var array
   */
   private $formStructure = array();


  /**
   * Pole s hodnotami formuláře
   * @var array
   */
   private $formValues = array();


 /**
  * Konstruktor nastaví základní parametry
   * @param string $formPrefix -- prefix formulářových prvků první úrovně
  */
   final public function  __construct($formPrefix = null) {
      if(AppCore::getSelectedModule() instanceof Module){
         $this->module = AppCore::getSelectedModule();
      }

      if(AppCore::getModuleMessages() instanceof Messages){
         $this->infomsg = AppCore::getModuleMessages();
      }

      if(AppCore::getModuleErrors() instanceof Messages){
         $this->errmsg = AppCore::getModuleErrors();
      }

      $this->formPrefix = $formPrefix;
   }

 /**
  * Metoda vrací objekt s informačními zprávami
  * @return Messages -- objekt zpráv
  */
   final private function infoMsg() {
      return $this->infomsg;
   }

 /**
  * Metoda vrací objekt s chybovými zprávami
  * @return Messages -- objekt zpráv
  */
   final private function errMsg() {
      return $this->errmsg;
   }

 /**
  * Metoda nastavuje prefix použitý ve formuláři
  * @param string $prefix -- prefix formulářových prvků
  * @return Form
  */
   public function setPrefix($prefix) {
      $this->formPrefix = $prefix;
      return $this;
   }

 /*
  * Meotdy pro vytváření prvků formuláře
  */

  /**
   * Metody vytváří prvek typu INPUT - SUBMIT, tedy prvke pro potvrzení formuláře
   * @param string $name -- název tlačítka
   *
   * @return Form
   */
   public function crSubmit($name) {
      $this->formStructure[self::INPUT_SUBMIT] = $name;
      $this->formValues[$name] = false;
      return $this;
   }

  /**
   * Metody vytváří prvek typu INPUT - TEXT
   * @param string $name -- Název prvku
   * @param boolean $obligation -- (option) jestli se jedná o povinný prvek
   * @param boolean $langs -- (option) jestli má prvek možnost jazykové mutace
   * @param mixed $specialValidation -- (option) typ vylidace prvku (výchozí je
   * žádná, odvíjí se od konstat třídy pro validaci) nebo funkci (is_string,...)
   * @param int $code -- (option) typ kódování (výchozí je dekódování všech prvků
   * na html entity, odvíjí se od konstatn třídy po kódovaní)
   * @param int $maxChars -- maximální počet znaků
   * @param int $minChars -- minimální počet znaků
   *
   * @return Form
   */
   public function crInputText($name, $obligation = false, $langs = false,
      $specialValidation = self::VALIDATION_NONE, $code = self::CODE_HTMLENCODE,
      $maxChars = null, $minChars = null) {

      $inputArray = array ();
      $inputArray[self::ITEM_NAME] = $name;
      $inputArray[self::ITEM_OBLIGATION] = $obligation;
      $inputArray[self::ITEM_LANGS] = $langs;
      $inputArray[self::ITEM_CODE] = $code;
      $inputArray[self::ITEM_VALIDATION] = $specialValidation;
      $inputArray[self::ITEM_MAX_LENGHT] = $maxChars;
      $inputArray[self::ITEM_MIN_LENGHT] = $minChars;

      $this->formStructure[self::INPUT_TEXT][$name] = $inputArray;

      if($langs){
         $this->formValues[$name] = $this->createLangArray();
      } else {
         $this->formValues[$name] = null;
      }
      return $this;
   }

  /**
   * Metody vytváří prvek typu INPUT - TEXT
   * @param string $name -- Název prvku
   * @param boolean $obligation -- (option) jestli se jedná o povinný prvek
   * @param mixed $specialValidation -- (option) typ vylidace prvku (výchozí je žádná,
   * odvíjí se od konstat třídy pro validaci) nebo lze volat funkci (is_numeric,...)
   * @param int $code -- (option) typ kódování (výchozí je dekódování všech prvků
   * na html entity, odvíjí se od konstatn třídy po kódovaní)
   *
   * @return Form
   */
   public function crInputHidden($name, $obligation = false,
      $specialValidation = self::VALIDATION_NONE, $code = self::CODE_HTMLENCODE) {

      $inputArray = array ();
      $inputArray[self::ITEM_NAME] = $name;
      $inputArray[self::ITEM_OBLIGATION] = $obligation;
      $inputArray[self::ITEM_CODE] = $code;
      $inputArray[self::ITEM_VALIDATION] = $specialValidation;

      $this->formStructure[self::INPUT_HIDDEN][$name] = $inputArray;

      $this->formValues[$name] = null;

      return $this;
   }

  /**
   * Metody vytváří prvek typu INPUT - CHECKBOX
   * @param string $name -- Název prvku
   *
   * @return Form
   */
   public function crInputCheckboxn($name) {
      $this->formStructure[self::INPUT_CHECKBOX][$name][self::ITEM_NAME] = $name;
      $this->formValues[$name] = false;

      return $this;
   }

  /**
   * Metody vytváří prvek typu INPUT - FILE
   * @param string $name -- Název prvku
   * @param boolean $obligation -- jestli je zadaný prvek povinný
   *
   * @return Form
   */
   public function crInputFile($name, $obligation = false) {
      $this->formStructure[self::INPUT_FILE][$name][self::ITEM_NAME] = $name;
      $this->formStructure[self::INPUT_FILE][$name][self::ITEM_OBLIGATION] = $obligation;
      $this->formValues[$name] = null;

      return $this;
   }

  /**
   * Metody vytváří prvek typu INPUT - TEXT
   * @param string $name -- Název prvku
   * @param boolean $obligation -- (option) jestli se jedná o povinný prvek
   * @param boolean $langs -- (option) jestli má prvek možnost jazykové mutace
   * @param mixed $specialValidation -- (option) typ vylidace prvku (výchozí je
   * žádná, odvíjí se od konstat třídy pro validaci) nebo funkci (is_string,...)
   * @param int $code -- (option) typ kódování (výchozí je dekódování všech prvků
   * na html entity, odvíjí se od konstatn třídy po kódovaní)
   * @param int $maxChars -- maximální počet znaků
   * @param int $minChars -- minimální počet znaků
   *
   * @return Form
   */
   public function crInputPassword($name, $obligation = false,
      $specialValidation = self::VALIDATION_NONE, $code = self::CODE_HTMLENCODE,
      $maxChars = null, $minChars = null) {

      $inputArray = array ();
      $inputArray[self::ITEM_NAME] = $name;
      $inputArray[self::ITEM_OBLIGATION] = $obligation;
      $inputArray[self::ITEM_CODE] = $code;
      $inputArray[self::ITEM_VALIDATION] = $specialValidation;
      $inputArray[self::ITEM_MAX_LENGHT] = $maxChars;
      $inputArray[self::ITEM_MIN_LENGHT] = $minChars;
      $this->formStructure[self::INPUT_PASSWORD][$name] = $inputArray;
      return $this;
   }

   /**
   * Metody vytváří prvek typu INPUT - DATE - ze smarty šablony
   * @param string $name -- Název prvku
   * @param int/timestamp $validateTime -- (option) jestli má být datum časově omezeno
    * Zadává se konstanta VALIDATE_DATE_XXX nebo časové razítko
   * @param bool $down -- (option) pokud je true datum musí být menší než zadané
   * @return Form
   */
   public function crInputDate($name, $validateTime = self::VALIDATE_DATE_ISEVERYTIME, $down = false) {

      $inputArray = array ();
      $inputArray[self::ITEM_NAME] = $name;
      $inputArray[self::ITEM_VALIDATION] = $validateTime;
      $inputArray[self::ITEM_CODE] = $down;

      $this->formStructure[self::INPUT_DATE][$name] = $inputArray;

      $this->formValues[$name] = null;

      return $this;
   }

  /**
   * Metody vytváří prvek typu TEXTAREA
   * @param string $name -- Název prvku
   * @param boolean $obligation -- (option) jestli se jedná o povinný prvek
   * @param boolean $langs -- (option) jestli má prvek možnost jazykové mutace
   * @param int $code -- (option) typ kódování (výchozí je dekódování všech prvků
   * na html entity, odvíjí se od konstatn třídy po kódovaní)
   * @param int $maxChars -- maximální počet znaků
   * @param int $minChars -- minimální počet znaků
   *
   * @return Form
   */
   public function crTextArea($name, $obligation = false, $langs = false,
      $code = self::CODE_HTMLENCODE, $maxChars = null, $minChars = null) {

      $inputArray = array ();

      $inputArray[self::ITEM_NAME] = $name;
      $inputArray[self::ITEM_OBLIGATION] = $obligation;
      $inputArray[self::ITEM_LANGS] = $langs;
      $inputArray[self::ITEM_CODE] = $code;
      $inputArray[self::ITEM_MAX_LENGHT] = $maxChars;
      $inputArray[self::ITEM_MIN_LENGHT] = $minChars;

      $this->formStructure[self::INPUT_TEXTAREA][$name] = $inputArray;

      if($langs){
         $this->formValues[$name] = $this->createLangArray();
      } else {
         $this->formValues[$name] = null;
      }
      return $this;
   }

  /*
   * Metody pro kontroly odeslání
   */

  /**
   * Metoda zkontroluje, jestli byl formulář odeslán a překontroluje všechny prvky
   *
   * @return boolean
   */
   public function checkForm() {
      //    Pokud byl formulář odeslán
//      echo "<pre>";
//      print_r($_POST);
//      echo "</pre>";
      if(isset ($_POST[$this->formPrefix.$this->formStructure[self::INPUT_SUBMIT]]) OR
         isset ($_POST[$this->formPrefix.$this->formStructure[self::INPUT_SUBMIT].'_x'])){
         
         $this->fillinForm();

         return !$this->isError;
      }
      return false;
   }

  /*
   * Metody pro nastavování a vracení hodnot prvků
   */

   /**
    * Metoda vrací pole s chybně zadanými prvky
    * @return array -- pole s názvy chybně zadaných prvků
    */
   public function getErrorItems() {
      return $this->errorItems;
   }

    /**
   * Metoda vrací hodnoty formuláře jako pole hodnot
   *
   * @param boolean $oneArray(option) -- true pokud má být vráceno pole s jednou hloubkou,
   * všechny indexy podpolí budo sloučeny s hlavními indexy pomocí operátoru
   * @param boolean $withPrefix(option) -- jestli do indexů bude přidán také prefix formuláře
   * @param string $operator(option) -- oddělovací operátor mezi indexy při slučování
   */
   public function getValues($oneArray = false, $withPrefix = false, $operator = '_') {
      $returnaArray = array();
      //    Pokud má být prefix tak se doplní
      if($withPrefix){
         foreach ($this->formValues as $key => $val) {
            $returnaArray[$this->formPrefix.$key] = $val;
         }
      } else {
         $returnaArray = $this->formValues;
      }

      if($oneArray){
         $returnaArray = $this->createOneArrayByKeys($returnaArray, null, $operator);
      }
      return $returnaArray;
   }

  /**
   * Metoda vrací hordnodu prvku ve formuláři
   * @param string $itemName -- název formulářového prvku
   * @param boolean $withPrefix -- jestli se má vracet i prefix formuláře
   * @param boolean $oneArray -- jestli má být vráce pole o jednom rozměru,
   * klíče budou sloučeny za sebe podle separátoru
   * @param string $separator -- oddělovač klíčů v jednorozměrném poli
   */
   public function getValue($itemName, $oneArray = false, $withPrefix = false, $separator = '_') {
      $value = null;
      if(key_exists($itemName, $this->formValues)){
         //    if(isset ($this->formValues[$itemName])){
         if($oneArray AND is_array($this->formValues[$itemName])){
            //        foreach ($this->formValues[$itemName] as $key => $val) {
            //          $value[$this->formPrefix.$key]
            //        }
            if(!$withPrefix){
               $value = $this->createOneArrayByKeys($this->formValues[$itemName]);
            } else {
               $value = $this->createOneArrayByKeys($this->formValues[$itemName], $this->formPrefix);
            }
         } else {
            //        if(!$withPrefix){
            $value = $this->formValues[$itemName];
            //        } else {
            //          $value = $this->formValues[$itemName];
            //        }
         }


      }
      return $value;
   }

  /**
   * Metoda nastavuje hodnotu prvkum formuláře
   * @param string $itemName -- název prvku
   * @param mixed $value -- hodnota prvku
   */
   public function setValue($itemName, $value) {
      $item = $this->findItem($itemName);

      if(!empty($item)){
         //      Jedná lise o jazykovou verzy
         if(isset ($item[self::ITEM_LANGS]) AND $item[self::ITEM_LANGS]){
            $langs = Locale::getAppLangs();
            foreach ($langs as $lang) {
               if(isset ($value[$lang])){
                  $this->formValues[$itemName][$lang] = $value[$lang];
               }
            }
         } else {
            $this->formValues[$itemName] = $value;
         }
         return true;
      } else {
         return false;
      }
   }

   /**
    * Metoda nalezne prvek ve struktůře formuláře a vrátího
    * @param string $name -- název
    * @return array -- pole s informacemi o prvku
    */
   private function findItem($name){
      foreach ($this->formStructure as $vars) {
         if(isset ($vars[$name])){
            return $vars[$name];
         }
      }
   }

  /**
   * Metoda vytvoří pole, se sloučenými klíči
   */
   private function createOneArrayByKeys($valuesArray, $currentPrefix = null, $separator = '_') {
      $values = array();

      if($currentPrefix != null){
         if($currentPrefix[strlen($currentPrefix)-1] != $separator){
            $prefixKey = $currentPrefix.$separator;
         } else {
            $prefixKey = $currentPrefix;
         }
      } else {
         $prefixKey = null;
      }

      foreach ($valuesArray as $key => $val) {
         if(is_array($val)){
            $values = array_merge($values, $this->createOneArrayByKeys($val,$prefixKey.$key, $separator));
         } else {
            $values[$prefixKey.$key] = $val;
         }
      }
      return $values;
   }

  /*
   * Privátní metody pro vyplňování formuláře daty
   */

  /**
   * Metoda vyplní odeslané pole do hodnot formuláře
   */
   private function fillinForm() {
      // tlačítko submit
      if(isset ($this->formStructure[self::INPUT_SUBMIT])){
         $this->fillInSubmit();
      }
      //    vyplnění textových polí
      if(isset ($this->formStructure[self::INPUT_TEXT])){
         $this->fillInInputText();
      }
      //    vyplnění zkrytých polí
      if(isset ($this->formStructure[self::INPUT_HIDDEN])){
         $this->fillInInputHidden();
      }
      //    vyplnění checkbox polí
      if(isset ($this->formStructure[self::INPUT_CHECKBOX])){
         $this->fillInInputCheckbox();
      }
      //    vyplnění heslových polí
      if(isset ($this->formStructure[self::INPUT_PASSWORD])){
         $this->fillInInputPassword();
      }
      //    vyplnění souborových polí
      if(isset ($this->formStructure[self::INPUT_FILE])){
         $this->fillInInputFile();
      }
      //    vyplnění pole s datem
      if(isset ($this->formStructure[self::INPUT_DATE])){
         $this->fillInInputDate();
      }
      // Vyplnění textarea
      if(isset ($this->formStructure[self::INPUT_TEXTAREA])){
         $this->fillInTextArea();
      }
      //    $this->debug();

   }

  /**
   * Metoda vyplní tlačítko submit
   */
   private function fillInSubmit() {
      if(isset ($_POST[$this->formPrefix.$this->formStructure[self::INPUT_SUBMIT]])){
         $this->formValues[$this->formStructure[self::INPUT_SUBMIT]] = true;
      }
   }

  /**
   * Metoda vyplní data z formuláře do pole hodnot s daty
   */
   private function fillInInputText() {
      $inputs = $this->formStructure[self::INPUT_TEXT];
      $oblLang = Locale::getDefaultLang();
      $allLang = Locale::getAppLangs();

      foreach ($inputs as $inputName => $value) {
         //      Jestli byl prvek vůbec odeslán
         if(isset ($_POST[$this->formPrefix.$inputName])){
            //   SOF   Kontrola povinnosti
            if($value[self::ITEM_OBLIGATION]){
               //        pokud je více jazyků, je povinný havní jazyk aplikace
               if($value[self::ITEM_LANGS] AND is_array($_POST[$this->formPrefix.$inputName])){
                  // Pokud bylo předáno pole prvků s jazyky
//                  if(!isset ($_POST[$this->formPrefix.$inputName][$oblLang]) AND
//                     is_array($_POST[$this->formPrefix.$inputName])){
//
//                  }
                  //          Pokud nebyla hodnota vyplněna
//                  else
                  if($_POST[$this->formPrefix.$inputName][$oblLang] == null
                     OR $_POST[$this->formPrefix.$inputName][$oblLang] == ''){
                     $this->addMissingValueError();
                     $this->addErrorItem($inputName, $oblLang);
                  }
               }
               //          Není jazyková verze
               else {
                  if($_POST[$this->formPrefix.$inputName] == null
                     OR $_POST[$this->formPrefix.$inputName] == ''){
                     $this->addMissingValueError();
                     $this->addErrorItem($inputName);
                  }
               }
            }
            //  EOF  Kontrola povinnosti
            //        data nejsou povinná
            else {}
            $postValue = $_POST[$this->formPrefix.$inputName];
            //echo get_magic_quotes_gpc()."<br>";
            //echo set_magic_quotes_runtime(false)."<br>";
            //echo get_magic_quotes_gpc()."<br>";
            //        echo "<pre>";
            //        print_r($postValue);
            //        echo "</pre>";


            // SOF kódování
            if($value[self::ITEM_CODE] != self::CODE_NONE){
               if($value[self::ITEM_CODE] == self::CODE_HTMLENCODE){
                  $postValue = $this->codeHtmlEncode($postValue);
               } else if($value[self::ITEM_CODE] == self::CODE_HTMLDECODE){
                  $postValue = $this->codeHtmlDecode($postValue);
               }
            }
            // EOF kódování

            // SOF Validace
            if($value[self::ITEM_VALIDATION] != self::VALIDATION_NONE){
               $this->validateItem($inputName, $postValue, $value[self::ITEM_VALIDATION]);
            }
            // EOF Validace

            // SOF délka řetězce
            if($value[self::ITEM_MIN_LENGHT] != null OR $value[self::ITEM_MIN_LENGHT] != null){
               $this->checkLenght($inputName, $postValue, $value[self::ITEM_MAX_LENGHT],
                  $value[self::ITEM_MIN_LENGHT]);
            }
            // EOF délka řetězce

            //        doplnění dat
            //        foreach ($allLang as $lang) {
            //          if(isset ($_POST[$this->formPrefix.$inputName][$lang])){
            $this->formValues[$inputName] = $postValue;
            //          }
            //        }

         } else {
            new CoreException(_('Nebyl odeslán formulářový prvek s názvem ')
               .$_POST[$this->formPrefix.$inputName], 1);
         }
      }
   }

  /**
   * Metoda vyplní data z formuláře do pole hodnot s daty
   */
   private function fillInInputHidden() {
      $inputs = $this->formStructure[self::INPUT_HIDDEN];

      foreach ($inputs as $inputName => $value) {
         //      Jestli byl prvek vůbec odeslán
         if(isset ($_POST[$this->formPrefix.$inputName])){
            //   SOF   Kontrola povinnosti
            if($value[self::ITEM_OBLIGATION]){
               if($_POST[$this->formPrefix.$inputName] == null
                  OR $_POST[$this->formPrefix.$inputName] == ''){
                  $this->addMissingValueError();
                  $this->addErrorItem($inputName);
               }
            }
            //  EOF  Kontrola povinnosti
            //        data nejsou povinná
            else {}
            $postValue = $_POST[$this->formPrefix.$inputName];

            // SOF kódování
            if($value[self::ITEM_CODE] != self::CODE_NONE){
               if($value[self::ITEM_CODE] == self::CODE_HTMLENCODE){
                  $postValue = $this->codeHtmlEncode($postValue);
               } else if($value[self::ITEM_CODE] == self::CODE_HTMLDECODE){
                  $postValue = $this->codeHtmlDecode($postValue);
               }
            }
            // EOF kódování

            // SOF Validace
            if($value[self::ITEM_VALIDATION] != self::VALIDATION_NONE){
               $this->validateItem($inputName, $postValue, $value[self::ITEM_VALIDATION]);
            }
            // EOF Validace

            $this->formValues[$inputName] = $postValue;

         } else {
            new CoreException(_('Nebyl odeslán formulářový prvek s názvem ')
               .$this->formPrefix.$inputName, 2);
         }
      }
   }

  /**
   * Metoda vyplní data z formuláře do pole hodnot s daty
   */
   private function fillInInputCheckbox() {
      $inputs = $this->formStructure[self::INPUT_CHECKBOX];

      foreach ($inputs as $inputName => $value) {
         if(isset ($_POST[$this->formPrefix.$inputName])){
//            Je odeslán bez hodnoty
            if($_POST[$this->formPrefix.$inputName] == 'on'){
               $this->formValues[$inputName] = true;
            }
//            Je odeslán s hodnotou
            else {
               $this->formValues[$inputName] = $_POST[$this->formPrefix.$inputName];
            }
         } else {
            $this->formValues[$inputName] = false;
         }
      }
   }

  /**
   * Metoda vyplní data z formuláře do pole hodnot s daty
   * @todo -- impllementovat procházení polí souborů
   */
   private function fillInInputFile() {
      $inputs = $this->formStructure[self::INPUT_FILE];
      foreach ($inputs as $inputName => $value) {
         // Jestli byl soubor vůbec odeslán
         if(isset ($_FILES[$this->formPrefix.$inputName])){
            // pokud byl odesláno pole souborů
            if(is_array($_FILES[$this->formPrefix.$inputName][self::POST_FILES_TMP_NAME])){
               $someFile = false;
               $filesArr = $_FILES[$this->formPrefix.$inputName][self::POST_FILES_TMP_NAME];
               // inicializace pole
               $this->formValues[$inputName] = array();
               // Procházení pole souborů
               foreach ($filesArr as $key => $fileTmpName) {
                  // pokud byl soubor špatně odeslán
                  if($_FILES[$this->formPrefix.$inputName][self::POST_FILES_ERROR][$key] != 0 AND
                     $_FILES[$this->formPrefix.$inputName][self::POST_FILES_ERROR][$key] != 4){
                     $this->createUploadFileError($_FILES[$this->formPrefix.$inputName][self::POST_FILES_ERROR][$key],
                        $_FILES[$this->formPrefix.$inputName][self::POST_FILES_ORIGINAL_NAME][$key]);
                  } else if($fileTmpName != null){
                     $file = new File($_FILES[$this->formPrefix.$inputName][self::POST_FILES_ORIGINAL_NAME][$key], null,
                        $_FILES[$this->formPrefix.$inputName][self::POST_FILES_TMP_NAME][$key],
                        $_FILES[$this->formPrefix.$inputName][self::POST_FILES_TYPE][$key],
                        $_FILES[$this->formPrefix.$inputName][self::POST_FILES_SIZE][$key]);
                     // uložení do pole s hodnotami
                     $this->formValues[$inputName][$key] = $file;
                     $someFile = true;
                  }
               }

               // Pokud je prvek povinný, musí být zadán alespoň jeden soubor
               if($value[self::ITEM_OBLIGATION] AND !$someFile){
                  $this->errMsg()->addMessage(_('Nebyl nahrán ani jeden soubor'));
                  $this->addErrorItem($inputName);
               }

            } else {
//               kontrola uploadu
               if (is_uploaded_file($_FILES[$this->formPrefix.$inputName][self::POST_FILES_TMP_NAME])){
                  // vytvoření objektu souboru
                  $file = new File($_FILES[$this->formPrefix.$inputName][self::POST_FILES_ORIGINAL_NAME], null,
                     $_FILES[$this->formPrefix.$inputName][self::POST_FILES_TMP_NAME],
                     $_FILES[$this->formPrefix.$inputName][self::POST_FILES_TYPE],
                     $_FILES[$this->formPrefix.$inputName][self::POST_FILES_SIZE]);
                  $this->formValues[$inputName] = $file;
               } else {
                  if(!$value[self::ITEM_OBLIGATION] AND $_FILES[$this->formPrefix.$inputName][self::POST_FILES_ERROR] == 4){

                  } else {
                     $this->createUploadFileError($_FILES[$this->formPrefix.$inputName][self::POST_FILES_ERROR],
                        $_FILES[$this->formPrefix.$inputName][self::POST_FILES_ORIGINAL_NAME]);
                  }
               }
            }

         } else {
            new CoreException(_('Nebyl odeslán formulářový prvek s názvem ')
               .$this->formPrefix.$inputName._(' nebo nebyl odeslán formulář s parametrem "enctype"'), 6);
         }

         //            if($value[self::ITEM_OBLIGATION]){
         //
         //            }

         //            $this->formValues[$inputName] = $file;
      }

         //      Jestli byl prvek vůbec odeslán
//         if(isset ($_POST[$this->formPrefix.$inputName])){
//            //   SOF   Kontrola povinnosti
//            if($value[self::ITEM_OBLIGATION]){
//
//               if(!is_array($_POST[$this->formPrefix.$inputName])){
//                  if($_POST[$this->formPrefix.$inputName] == null
//                     OR $_POST[$this->formPrefix.$inputName] == ''){
//                     $this->addMissingValueError();
//                     $this->addErrorItem($inputName);
//                  }
//               } else {
//                  $isEmpty = true;
//                  foreach ($_POST[$this->formPrefix.$inputName] as $key => $variable) {
//                     if(empty ($variable)){
//                        unset ($_POST[$this->formPrefix.$inputName][$key]);
//                     } else {
//                        $isEmpty = false;
//                     }
//                  }
//               }
//            }
//            //  EOF  Kontrola povinnosti
//            //        data nejsou povinná
//            $postValue = $_POST[$this->formPrefix.$inputName];
//
//            // SOF Validace
//            if($value[self::ITEM_VALIDATION] != self::VALIDATION_NONE){
//               $this->validateItem($inputName, $postValue, $value[self::ITEM_VALIDATION]);
//            }
//            // EOF Validace
//
//            $this->formValues[$inputName] = $postValue;
//
//         } else {
//            new CoreException(_('Nebyl odeslán formulářový prvek s názvem ')
//               .$inputName, 5);
//         }
//      }
   }

   /**
   * Metoda vyplní data z formuláře do pole hodnot s datumy
    *
    * @todo dodělat validaci
   */
   private function fillInInputDate() {
      $inputs = $this->formStructure[self::INPUT_DATE];
      foreach ($inputs as $inputName => $value) {
         if(isset ($_POST[$this->formPrefix.$inputName])){
            $timestamp = mktime(0, 0, 0, $_POST[$this->formPrefix.$inputName]['Date_Day'],
               $_POST[$this->formPrefix.$inputName]['Date_Month'],
               $_POST[$this->formPrefix.$inputName]['Date_Year']);

            // SOF Validace
//            if($value[self::ITEM_VALIDATION] != self::VALIDATE_DATE_ISEVERYTIME){
//               $this->validateItem($inputName, $timestamp, $value[self::ITEM_VALIDATION]);
//            }
            // EOF Validace
//            Je odeslán bez hodnoty
//            if($_POST[$this->formPrefix.$inputName] == 'on'){
//               $this->formValues[$inputName] = true;
//            }
////            Je odeslán s hodnotou
//            else {
//               $this->formValues[$inputName] = $_POST[$this->formPrefix.$inputName];
//            }
         } else {
            $this->formValues[$inputName] = false;
         }
      }
   }

   /**
    * Metoda vygeneruje chbovou hlášku pro chybně odeslaný soubor
    * @param integer $errNumber -- číslo chyby z $_FILES
    * @param string $fileOriginalName -- původní název souboru
    */
   private function createUploadFileError($errNumber, $fileOriginalName) {
      switch($errNumber){
         case 0: //no error; possible file attack!
            $this->errMsg()->addMessage(_('Problém s nahráním souboru ').$fileOriginalName);
            break;
         case 1: //uploaded file exceeds the upload_max_filesize directive in php.ini
            $this->errMsg()->addMessage(_('Soubor je příliš ').$fileOriginalName._(' velký'));
            break;
         case 2: //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
            $this->errMsg()->addMessage(_('Soubor je příliš ').$fileOriginalName._(' velký'));
            break;
         case 3: //uploaded file was only partially uploaded
            $this->errMsg()->addMessage(_('Soubor ').$fileOriginalName._(' byl nahrán jen částečně'));
            break;
         case 4: //no file was uploaded
            $this->errMsg()->addMessage(_('Soubor nebyl vybrán'));
            break;
         default: //a default error, just in case!  :)
            $this->errMsg()->addMessage(_('Problém s nahráním souboru ').$fileOriginalName);
            break;
      }
   }

  /**
   * Metoda vyplní data z formuláře do pole hodnot s daty
   */
   private function fillInInputPassword() {
      $inputs = $this->formStructure[self::INPUT_PASSWORD];

      foreach ($inputs as $inputName => $value) {
         //      Jestli byl prvek vůbec odeslán
         if(isset ($_POST[$this->formPrefix.$inputName])){
            //   SOF   Kontrola povinnosti
            if($value[self::ITEM_OBLIGATION]){
               if($_POST[$this->formPrefix.$inputName] == null
                  OR $_POST[$this->formPrefix.$inputName] == ''){
                  $this->addMissingValueError();
                  $this->addErrorItem($inputName);
               }
            }
            //  EOF  Kontrola povinnosti
            $postValue = $_POST[$this->formPrefix.$inputName];

            // SOF kódování
            if($value[self::ITEM_CODE] != self::CODE_NONE){
               if($value[self::ITEM_CODE] == self::CODE_HTMLENCODE){
                  $postValue = $this->codeHtmlEncode($postValue);
               } else if($value[self::ITEM_CODE] == self::CODE_HTMLDECODE){
                  $postValue = $this->codeHtmlDecode($postValue);
               }
            }
            // EOF kódování

            // SOF Validace
            if($value[self::ITEM_VALIDATION] != self::VALIDATION_NONE){
               $this->validateItem($inputName, $postValue, $value[self::ITEM_VALIDATION]);
            }
            // EOF Validace

            // SOF délka řetězce
            if($value[self::ITEM_MIN_LENGHT] != null OR $value[self::ITEM_MIN_LENGHT] != null){
               $this->checkLenght($inputName, $postValue, $value[self::ITEM_MAX_LENGHT],
                  $value[self::ITEM_MIN_LENGHT]);
            }
            // EOF délka řetězce

            $this->formValues[$inputName] = $postValue;
         } else {
            new CoreException(_('Nebyl odeslán formulářový prvek s názvem ')
               .$_POST[$this->formPrefix.$inputName], 3);
         }
      }
   }

  /**
   * Metoda vyplní data z formuláře do pole hodnot s daty
   */
   private function fillInTextArea() {
      $inputs = $this->formStructure[self::INPUT_TEXTAREA];
      $oblLang = Locale::getDefaultLang();
      $allLang = Locale::getAppLangs();

      foreach ($inputs as $inputName => $value) {
         //      Jestli byl prvek vůbec odeslán
         if(isset ($_POST[$this->formPrefix.$inputName][$oblLang])){
            //   SOF   Kontrola povinnosti
            if($value[self::ITEM_OBLIGATION]){
               //        pokud je více jazyků, je povinný havní jazyk aplikace
               if($value[self::ITEM_LANGS] AND is_array($_POST[$this->formPrefix.$inputName])){
                  // Pokud nebyla hodnota vyplněna
                  if($_POST[$this->formPrefix.$inputName][$oblLang] == null
                     OR $_POST[$this->formPrefix.$inputName][$oblLang] == ''){
                     $this->addMissingValueError();
                     $this->addErrorItem($inputName, $oblLang);
                  }
               }
               // Není jazyková verze
               else {
                  if($_POST[$this->formPrefix.$inputName] == null
                     OR $_POST[$this->formPrefix.$inputName] == ''){
                     $this->addMissingValueError();
                     $this->addErrorItem($inputName);
                  }
               }
            }
            //  EOF  Kontrola povinnosti
            //        data nejsou povinná
            else {}
            $postValue = $_POST[$this->formPrefix.$inputName];
            //echo get_magic_quotes_gpc()."<br>";
            //echo set_magic_quotes_runtime(false)."<br>";
            //echo get_magic_quotes_gpc()."<br>";
            //        echo "<pre>";
            //        print_r($postValue);
            //        echo "</pre>";


            // SOF kódování
            if($value[self::ITEM_CODE] != self::CODE_NONE){
               if($value[self::ITEM_CODE] == self::CODE_HTMLENCODE){
                  $postValue = $this->codeHtmlEncode($postValue);
               } else if($value[self::ITEM_CODE] == self::CODE_HTMLDECODE){
                  $postValue = $this->codeHtmlDecode($postValue);
               }
            }
            // EOF kódování

            // SOF Validace
            //        if($value[self::ITEM_VALIDATION] != self::VALIDATION_NONE){
            //          $this->validateItem($postValue, $value[self::ITEM_VALIDATION]);
            //        }
            // EOF Validace

            // SOF délka řetězce
            if($value[self::ITEM_MIN_LENGHT] != null OR $value[self::ITEM_MIN_LENGHT] != null){
               $this->checkLenght($inputName, $postValue, $value[self::ITEM_MAX_LENGHT],
                  $value[self::ITEM_MIN_LENGHT]);
            }
            // EOF délka řetězce

            $this->formValues[$inputName] = $postValue;

         } else {
            new CoreException(_('Nebyl odeslán formulářový prvek s názvem ').$_POST[$this->formPrefix.$inputName], 4);
         }
      }
   }

  /*
   * Privátní metody
   */
   private function createLangArray() {
      $lang = Locale::getAppLangs();

      $retArr = array();
      foreach ($lang as $l) {
         $retArr[$l] = null;
      }
      return $retArr;
   }

  /**
   * Metoda překóduje prvky na html entity (rekurzivní funkce)
   * @param mixed $value -- hodnoty
   * @return mixed -- překódované hodnoty
   */
   private function codeHtmlEncode($value){
      $codeValue = null;

      if(is_array($value)){
         foreach ($value as $key => $val) {
            $codeValue[$key] = $this->codeHtmlEncode($val);
         }
      } else {
         // protože je zaplé gpc magic quotes
         $value = stripslashes($value);
         $codeValue = htmlspecialchars($value, ENT_QUOTES);
      }

      return $codeValue;
   }

  /**
   * Metoda překóduje html entity na prvky (rekurzivní funkce)
   * @param mixed $value -- hodnoty
   * @return mixed -- dekódované hodnoty
   */
   private function codeHtmlDecode($value){
      $codeValue = null;

      if(is_array($value)){
         foreach ($value as $key => $val) {
            $codeValue[$key] = $this->codeHtmlDecode($val);
         }
      } else {
         $codeValue = htmlspecialchars_decode($value, ENT_QUOTES);
      }

      return $codeValue;
   }

  /**
   * Metoda pro validaci prvků
   * @param string $value -- název prvku
   * @param mixed $value -- hodnota, která se má kontrolovat
   * @param mixed $validation -- typ validace (buď kód nebo název funkce)
   *
   * @todo -- kontrola pole
   */
   private function validateItem($itemName, $value, $validation) {
      if(is_int($validation)){
         switch ($validation) {
            case self::VALIDATION_EMAIL:
               if(!$this->validateEMail($value)){
                  $this->errMsg()->addMessage(_('Nebyla zadána korektní e-mailová adresa'));
                  $this->addErrorItem($itemName);
               }
               break;

            default:
               throw new CoreException(_('Tento typ validace CODE: ').$validationCode
                  ._('není implementovát. Implementuj!'));
               break;
         }
      } else if(function_exists($validation)){
         if(!$validation($value)){
            $this->errMsg()->addMessage(_('Nebyl zadán správný typ prvku'));
            $this->addErrorItem($itemName);
         }
      }
   }

  /**
   * Metoda pro validaci emailu
   * @param string $email -- emailová adresa
   */
   private function validateEMail($email) {
      $validator = new UrlValidator();
      return $validator->checkMail($email);
   }

  /**
   * Metoda kontroluje délky řetězců
   * @param string $itemName -- název prvku
   * @param mixed $value -- hodnota prvku
   * @param int $maxChars -- maximální délka řetězce
   * @param int $minChars -- minimální délka řetězce
   *
   * @todo -- dodělat při zadání pole
   */
   private function checkLenght($itemName, $value, $maxChars, $minChars) {
      if(!is_array($value)){
         // pokud je omezena maximální délka
         if($maxChars != null){
            if(strlen($value) > $maxChars){
               $this->errMsg()->addMessage(_('Zadaná hodnota je příliš dlouhá'));
               $this->addErrorItem($itemName);
            }
         }
         // pokud je omezena minimální délka
         if($minChars != null){
            if(strlen($value) <$minChars){
               $this->errMsg()->addMessage(_('Zadaná hodnota je příliš krátká'));
               $this->addErrorItem($itemName);
            }
         }
      }


   }

  /**
   * Metoda přidá chybovou hlášku o nevyplněném prvku
   */
   private function addMissingValueError() {
      $this->errMsg()->addMessage(_('Nebyly vyplněny všechny povinné údaje'));
   }

  /**
   * Přidá název prvku do chybných prvků
   * @param string $name -- název prvku
   */
   private function addErrorItem($name, $subItem = null) {
      

      if($subItem == null){
         $this->errorItems[$name] = true;
      } else {
         $this->errorItems[$name][$subItem] = true;
      }
      $this->isError = true;
   }

  /**
   * @todo Odstranit
   */
   public function debug() {
      echo "<pre>";
      print_r($this);
      echo '</pre>';
   }
}
?>
