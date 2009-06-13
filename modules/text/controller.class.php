<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class Text_Controller extends Controller {
   /**
    * Názvy formůlářových prvků
    * @var string
    */
   const FORM_PREFIX = 'text_';
   const FORM_BUTTON_SEND = 'send';
   const FORM_TEXT = 'text';

   /**
    * Názvy parametrů modulu
    */
   const PARAM_FILES = 'files';
   const PARAM_THEME = 'theme';

   /**
    * Kontroler pro zobrazení textu
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
   }

   /**
    * Kontroler pro editaci textu
    */
   public function edittextController() {
      $this->checkWritebleRights();

      if($this->getModule()->getParam(self::PARAM_FILES, true)){
         // Uživatelské soubory
         $files = new Eplugin_UserFiles($this->sys());
         $this->view()->EPLfiles = $files;
      }

      $form = new Form();
      $form->setPrefix(self::FORM_PREFIX);

      $form->crTextArea(self::FORM_TEXT, true, true, Form::CODE_HTMLDECODE)
      ->crSubmit(self::FORM_BUTTON_SEND);

      //        Pokud byl odeslán formulář
      if($form->checkForm()){
         $text = Text_Model_Detail($this->sys());
         if(!$text->saveEditText($form->getValue(self::FORM_TEXT))){
            throw new UnexpectedValueException($this->_m('Text se nepodařilo uložit'));
         }

         $this->infoMsg()->addMessage($this->_m('Text byl uložen'));
         $this->getLink()->action()->reload();
      }
   }
}

?>