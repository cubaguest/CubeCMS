<?php
/**
 * Validátor přípony nahrávaného souboru
 * 
 * @copyright  	Copyright (c) 2008-2011 Jakub Matas
 * @version    	$Id: $ Cube CMS 7.5 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Valídátor souborů po uploadu
 * 
 * @todo          Doplnit další formáty přípon
 */
class Form_Validator_FileExtension extends Form_Validator implements Form_Validator_Interface {
   const DOC = 1;
   const IMG = 2;
   const VID = 4;
   const AUD = 8;
   const BIN = 16;
   const ARCH = 32;
   
   const ALL = 512;
   
   /**
    * Soubory stránek (NENÍ BEZPEČNÉ !!!!)
    */
   const WEB = 1024;
   /**
    * Vše (NENÍ BEZPEČNÉ !!!!)
    */
   const ALL_NO_SAFE = 2048;

   /**
    * Pole s povolenými typ souborů
    * @var array
    */
   private $extensions = array();

   public function  __construct($extensions, $errMsg = null) {
      if(!is_array($extensions)){
         if(is_int($extensions)){ // konstanty třídy
            $cur = $extensions;
            $extensions = array();
            
            if(($cur-self::WEB) >= 0){
               $extensions = array_merge($extensions, array("htm","html","xhtml","xml","css","php","js"));
               $cur -= self::WEB;
            }
            if(($cur-self::ARCH) >= 0){
               $extensions = array_merge($extensions, array("zip", "rar", "7z", "bz2", "gz"));
               $cur -= self::ARCH;
            }
            if(($cur-self::BIN) >= 0){
               $extensions = array_merge($extensions, array("exe", "bat", "com", "bin", "sh"));
               $cur -= self::BIN;
            }
            if(($cur-self::AUD) >= 0){
               $extensions = array_merge($extensions, array("mp3","wav","wma","flac","ogg"));
               $cur -= self::AUD;
            }
            if(($cur-self::VID) >= 0){
               $extensions = array_merge($extensions, array("swf","flv","avi","wmv","mov"));
               $cur -= self::VID;
            }
            if(($cur-self::IMG) >= 0){
               $extensions = array_merge($extensions, array("swf","jpg","jpeg","gif","png","bmp","wmf"));
               $cur -= self::IMG;
            }
            if(($cur-self::DOC) >= 0){
               $extensions = array_merge($extensions, array("txt","doc","xls","rtf","ppt","pdf","csv","docx","odf"));
               $cur -= self::DOC;
            }
         } else {
            $extensions = str_replace(' ', null, $extensions);
            $extensions = explode(';', $extensions);
         }
      }
      $this->extensions = $extensions;

      if($errMsg == null) {
         parent::__construct($this->tr('V položce "%s" nebyl odeslán povolený typ souboru'));
      } else {
         parent::__construct($errMsg);
      }
   }

   /**
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element) {
      if(count($this->extensions) > 5){
         $mimes = implode(', ', array_slice($this->extensions, 0, 5) ).',<span title="'.implode(', ', $this->extensions).'"> ...</span>';
      } else {
         $mimes = implode(', ', $this->extensions);
      }
      $element->addValidationConditionLabel(sprintf($this->tr("soubor s příponou %s"),$mimes));
   }

   public function validate(Form_Element $elemObj) {
      $values = $elemObj->getUnfilteredValues();
      if($values == false || empty($values)){
         return true;
      }
      switch (get_class($elemObj)) {
         // input text
         case 'Form_Element_File':
            if($elemObj->isDimensional() OR $elemObj->isMultiLang()) {
               foreach ($values as $file){
                  if(!in_array(strtolower($file['extension']), $this->extensions)) {
                  $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel()));
                  $this->isValid = false;
                  return false;
               }
               }
            } else {
               if(!in_array(strtolower($values['extension']), $this->extensions)) {
                  $this->errMsg()->addMessage(sprintf($this->errMessage, $elemObj->getLabel()));
                  $this->isValid = false;
                  return false;
               }
            }
            break;
         default:
            break;
      }
      return true;
   }
}
?>
