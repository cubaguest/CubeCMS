<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class Text_Controller extends Controller {
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
   public function editController() {
      $this->checkWritebleRights();

      $form = new Form("text_");
      
      $label = new Form_Element_Text('label', $this->_('Nadpis'));
      $label->setSubLabel($this->_('Doplní se k nadpisu kategorie a stránky'));
      $label->setLangs();
      $form->addElement($label);

      $textarea = new Form_Element_TextArea('text', $this->_("Text"));
      $textarea->setLangs();
      $textarea->addValidation(new Form_Validator_NotEmpty(null, Locale::getDefaultLang(true)));
      $form->addElement($textarea);

      $textareaPanel = new Form_Element_TextArea('paneltext', $this->_("Text panelu"));
      $textareaPanel->setSubLabel($this->_('Je zobrazen v panelu, pokud je panel zapnut'));
      $textareaPanel->setLangs();
      $form->addElement($textareaPanel);

      $model = new Text_Model_Detail();
      $text = $model->getText($this->category()->getId());
      if($text != false){
         $form->text->setValues($text->{Text_Model_Detail::COLUMN_TEXT});
         $form->label->setValues($text->{Text_Model_Detail::COLUMN_LABEL});
         $form->paneltext->setValues($text->{Text_Model_Detail::COLUMN_TEXT_PANEL});
      }

      $submit = new Form_Element_Submit('send', $this->_("Uložit"));
      $form->addElement($submit);

      if($form->isValid()){
         try {
            $model->saveText($form->text->getValues(), $form->label->getValues(),
               $form->paneltext->getValues(), $this->category()->getId());
            $this->infoMsg()->addMessage($this->_('Text byl uložen'));
            $this->link()->route()->reload();
         } catch (PDOException $e) {
            new CoreErrors($e);
         }
      }

      // view
      $this->view()->template()->form = $form;
      $this->view()->template()->addTplFile("textedit.phtml");
      $this->view()->template()->addCssFile("style.css");
      //
   }

   public function textController() {}
}

?>