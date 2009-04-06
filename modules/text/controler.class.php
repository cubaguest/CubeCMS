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

      //		Model pro načtení textu
      $model = new TextDetailModel();
      $this->container()->addData('text', $model->getText());
      $this->container()->text = $model->getText();

      //		pokud má uživatel právo zápisu vytvoříme odkaz pro editaci
      if($this->getRights()->isWritable()){
         $this->container()->addLink('link_edit', $this->getLink()->action($this->getAction()->editText()));
      }
   }

   /**
    * Kontroler pro editaci textu
    */
   public function edittextController() {
      $this->checkWritebleRights();

      if($this->getModule()->getParam(self::PARAM_FILES, true)){
         // Uživatelské soubory
         $files = new UserFilesEplugin($this->getRights());
         $files->setIdArticle($this->getModule()->getId());
         $this->container()->addEplugin('files', $files);
      }

      $form = new Form();
      $form->setPrefix(self::FORM_PREFIX);

      $form->crTextArea(self::FORM_TEXT, true, true, Form::CODE_HTMLDECODE)
      ->crSubmit(self::FORM_BUTTON_SEND);

      $text = new TextDetailModel();
      $form->setValue(self::FORM_TEXT, $text->getAllLangText());

      //        Pokud byl odeslán formulář
      if($form->checkForm()){
         if(!$text->saveEditText($form->getValue(self::FORM_TEXT))){
            throw new UnexpectedValueException(_m('Text se nepodařilo uložit'));
         }
         $this->infoMsg()->addMessage(_m('Text byl uložen'));
         $this->getLink()->action()->reload();
      }
      //    Data do šablony
      $this->container()->addData('TEXT_DATA', $form->getValues());
      $this->container()->addData('ERROR_ITEMS', $form->getErrorItems());

      //		Odkaz zpět
      $this->container()->addLink('BUTTON_BACK', $this->getLink()->action());
   }
}

?>