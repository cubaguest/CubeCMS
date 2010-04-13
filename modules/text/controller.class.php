<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class Text_Controller extends Controller {
   const TEXT_MAIN_KEY = 'main';
   const TEXT_PANEL_KEY = 'panel';
/**
 * Kontroler pro zobrazení textu
 */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      $model = new Text_Model_Detail();
      // text
      $this->view()->text = $model->getText(Category::getSelectedCategory()->getId(),self::TEXT_MAIN_KEY);
   }

   public function contentController() {
      $this->mainController();
   }

   /**
    * Kontroler pro editaci textu
    */
   public function editController() {
      $this->checkWritebleRights();

      $form = new Form("text_");
      
      $label = new Form_Element_Text('label', $this->_('Nadpis'));
      $label->setSubLabel($this->_('Doplní se namísto nadpisu stránky'));
      $label->setLangs();
      $form->addElement($label);

      $textarea = new Form_Element_TextArea('text', $this->_("Text"));
      $textarea->setLangs();
      $textarea->addValidation(new Form_Validator_NotEmpty(null, Locale::getDefaultLang(true)));
      $form->addElement($textarea);

      $model = new Text_Model_Detail();
      $text = $model->getText($this->category()->getId(), self::TEXT_MAIN_KEY);
      if($text != false){
         $form->text->setValues($text->{Text_Model_Detail::COLUMN_TEXT});
         $form->label->setValues($text->{Text_Model_Detail::COLUMN_LABEL});
      }

      $submit = new Form_Element_Submit('send', $this->_("Uložit"));
      $form->addElement($submit);

      if($form->isValid()){
         try {
            $model->saveText($form->text->getValues(), $form->label->getValues(),
                    $this->category()->getId(), self::TEXT_MAIN_KEY);
            $this->infoMsg()->addMessage($this->_('Text byl uložen'));
            $this->link()->route()->reload();
         } catch (PDOException $e) {
            new CoreErrors($e);
         }
      }
      // view
      $this->view()->template()->form = $form;
   }

   /**
    * Kontroler pro editaci textu
    */
   public function editPanelController() {
      $this->checkWritebleRights();

      $form = new Form("text_");

      $textarea = new Form_Element_TextArea('text', $this->_("Text"));
      $textarea->setLangs();
      $form->addElement($textarea);

      $model = new Text_Model_Detail();
      $text = $model->getText($this->category()->getId(), self::TEXT_PANEL_KEY);
      if($text != false){
         $form->text->setValues($text->{Text_Model_Detail::COLUMN_TEXT});
      }

      $submit = new Form_Element_Submit('send', $this->_("Uložit"));
      $form->addElement($submit);

      if($form->isValid()){
         try {
            $model->saveText($form->text->getValues(), null, $this->category()->getId(),self::TEXT_PANEL_KEY);
            $this->infoMsg()->addMessage($this->_('Text panelu byl uložen'));
            $this->link()->route()->reload();
         } catch (PDOException $e) {
            new CoreErrors($e);
         }
      }
      // view
      $this->view()->template()->form = $form;
   }

   public function textController() {}
}

?>