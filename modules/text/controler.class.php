<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class TextController extends Controller {
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
         //         $files = new UserFilesEplugin($this->getRights());
         //         $files->setIdArticle($this->getModule()->getId());
         //         $this->container()->addEplugin('files', $files);
      }
echo "tady";
      $form = new Form();
      $form->setPrefix(self::FORM_PREFIX);

      $form->crTextArea(self::FORM_TEXT, true, true, Form::CODE_HTMLDECODE)
      ->crSubmit(self::FORM_BUTTON_SEND);

      //        Pokud byl odeslán formulář
      if($form->checkForm()){
         $text = $this->createModel("TextDetailModel");
         if(!$text->saveEditText($form->getValue(self::FORM_TEXT))){
            throw new UnexpectedValueException($this->_m('Text se nepodařilo uložit'));
         }

         $this->infoMsg()->addMessage($this->_m('Text byl uložen'));
         $this->getLink()->action()->reload();
      }
   }
}

?>