<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class DayMenu_Controller extends Controller {
   /**
 * Kontroler pro zobrazení textu
 */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      
      $dayKey = $this->getRequestParam('day', date('N'));
      $this->view()->day = $dayKey;
      
      
      $model = new Text_Model();
      // text
      $text = $model->where(Text_Model::COLUMN_ID_CATEGORY.' = :idc AND '.Text_Model::COLUMN_SUBKEY.' = :subkey', 
         array('idc' => $this->category()->getId(), 'subkey' => $dayKey))
         ->record();
      
      $this->view()->text = $text;
   }

   /**
    * Kontroler pro editaci textu
    */
   public function editController() {
      $this->checkWritebleRights();
      $this->view()->day = $this->getRequest('day', 1);

      $form = new Form("text_");
      
      $textarea = new Form_Element_TextArea('text', $this->tr("Text"));
      $textarea->setLangs();
      $textarea->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      $form->addElement($textarea);
      
      $textP = new Form_Element_TextArea('textPanel', $this->tr("Text v panelu"));
      $textP->setLangs();
      $form->addElement($textP);

      $model = new Text_Model();
      $textRecord = $model->where(Text_Model::COLUMN_ID_CATEGORY." = :idc AND ".Text_Model::COLUMN_SUBKEY.' = :sk',
              array('idc' => $this->category()->getId(), 'sk' => $this->getRequest('day', 1)))
              ->record();
      
      $textRecordPanel = $model->where(Text_Model::COLUMN_ID_CATEGORY." = :idc AND ".Text_Model::COLUMN_SUBKEY.' = :sk',
              array('idc' => $this->category()->getId(), 'sk' => 'p_'.$this->getRequest('day', 1)))
              ->record();
      
      
      if($textRecord != false){
         $form->text->setValues($textRecord->{Text_Model_Detail::COLUMN_TEXT});
      } else {
         $textRecord = $model->newRecord();
         $textRecord->{Text_Model::COLUMN_ID_CATEGORY} = $this->category()->getId();
         $textRecord->{Text_Model::COLUMN_SUBKEY} = $this->view()->day;
      }
      
      if($textRecordPanel != false){
         $form->textPanel->setValues($textRecordPanel->{Text_Model_Detail::COLUMN_TEXT});
      } else {
         $textRecordPanel = $model->newRecord();
         $textRecordPanel->{Text_Model::COLUMN_ID_CATEGORY} = $this->category()->getId();
         $textRecordPanel->{Text_Model::COLUMN_SUBKEY} = 'p_'.$this->view()->day;
      }
      

      $submit = new Form_Element_SaveCancel('send');
      $form->addElement($submit);

      if($form->isSend() AND $form->send->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }

      if($form->isValid()){
         try {
            // odtranění script, nebezpečných tagů a komentřů
            $text = vve_strip_html_comment($form->text->getValues());

            $textRecord->{Text_Model::COLUMN_TEXT} = $text;
            $textRecord->{Text_Model::COLUMN_TEXT_CLEAR} = strip_tags($text);
            $model->save($textRecord);
            
            $textRecordPanel->{Text_Model::COLUMN_TEXT} = $form->textPanel->getValues();
            $model->save($textRecordPanel);
            
            $this->log('úprava textu');
            $this->infoMsg()->addMessage($this->tr('Menu bylo uloženo'));
            $this->link()->route()->reload();
         } catch (PDOException $e) {
            new CoreErrors($e);
         }
      }
      // view
      $this->view()->template()->form = $form;
   }

   public function settings(&$settings, Form &$form) {
      $fGrpViewSet = $form->addGroup('view', $this->tr('Nastavení vzhledu'));
      $fGrpEditSet = $form->addGroup('editSettings', $this->tr('Nastavení úprav'));

      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
      }
   }
}

?>