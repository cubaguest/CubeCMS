<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of form_validate_noemptyclass
 *
 * @author jakub
 */
class Form_Validator_NotEmpty extends Form_Validator implements Form_Validator_Interface {

/**
 * Které položky jsou povinné
 * @var array
 */
   private $columsNotEmpty = array();

   /**
    * Proměnná s název css třídy, která se přidá ke každému elementu
    * @var string
    */
   public static $cssClass = 'form-required';

   public function  __construct($errMsg = null, $columsNotEmpty = null) {
      if($errMsg == null) {
         parent::__construct($this->tr('Nebyla vyplněna povinná položka "%s"'));
      } else {
         parent::__construct($errMsg);
      }
      if(!is_array($columsNotEmpty) AND $columsNotEmpty != null) {
         $columsNotEmpty = array($columsNotEmpty);
      }
      $this->columsNotEmpty = $columsNotEmpty;
   }

   /**
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element) {
      $element->htmlLabel()->addClass(self::$cssClass);
//      $element->html()->setAttrib('required', 'true')->addClass(self::$cssClass);
      $element->html()->addClass(self::$cssClass);
      $element->htmlLabel()->setAttrib('title', $this->tr('prvek je povinný'));
   }

   public function validate(Form_Element $elemObj) {
      switch (get_class($elemObj)) {
      // input text
         case 'Form_Element_Text':
         case 'Form_Element_TextArea':
         case 'Form_Element_Password':
            if($elemObj->isMultiLang()) {
               // pokud se kontrolují jen některé sloupce
               if(!empty ($this->columsNotEmpty)){
                  $retu = $this->checkEmptyValues($elemObj->getUnfilteredValues(), $this->columsNotEmpty);
                  if($retu !== true){
                     $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel().' '.$retu));
                     $this->isValid = false;
                  }
               }
               // pokud mají být vyplněny všechny sloupce
               else {
                  if(!$this->checkEmptyAllValues($elemObj->getUnfilteredValues())){
                     $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel()));
                     $this->isValid = false;
                  }
               }
            } else {
               if($elemObj->isDimensional()) {
                  if(!$this->checkEmptyAllValues($elemObj->getUnfilteredValues())) {
                     $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel()));
                     $this->isValid = false;
                  }
               } else {
                  if($elemObj->getUnfilteredValues() == null OR $elemObj->getUnfilteredValues() == "") {
                     $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel()));
                     $this->isValid = false;
                  }
               }
            }
            break;
         // input checkbox
         case 'Form_Element_Checkbox':
         case 'Form_Element_File':
            if($elemObj->getUnfilteredValues() == false) {
               $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel()));
               $this->isValid = false;
            }
            break;
         case 'Form_Element_Select':
            if(count($elemObj->getUnfilteredValues()) == 0){
               $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel()));
               $this->isValid = false;
            }
            break;
         case 'Form_Element_Radio':
            if($elemObj->getUnfilteredValues() === false){
               $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel()));
               $this->isValid = false;
            }
            break;
         default:
            break;
      }
      return $this->isValid();

   }

   /**
    * Metoda zkontroluje jestli jsou prvky z pole colum vyplněny
    * @param string/array $values -- pole s hodnotami
    * @param array $colums -- pole s nutnými prvky
    * @param string $key -- název klíče k prováděnému poli
    * @return bool/string -- true pokud je vše v pořádku, jinak řetězec z pole
    * s kontrolovanými prvky
    */
   private function checkEmptyValues($values, $colums, $key = null) {
      if(!is_array($values)) {
         if(key_exists($key, $colums) AND $values == null){
            return $colums[$key];
         }
      } else {
         foreach ($values as $valKey => $val) {
            $ret = $this->checkEmptyValues($val, $colums, $valKey);
            if($ret !== true){
               return $ret;
            }
         }
      }
      return true;
   }

   /**
    * Metoda zkontroluje jestli jsou všechny prvky vyplněny
    * @param array/string $values
    * @return boolean -- pokud je jeden prvek prázný vrací false
    */
   private function checkEmptyAllValues($values) {
      if(!is_array($values)) {
         if($values == null){
            return false;
         }
      } else {
         foreach ($values as $val) {
            if(!$this->checkEmptyAllValues($val)){
               return false;
            }
         }
      }
      return true;
   }
}
?>
