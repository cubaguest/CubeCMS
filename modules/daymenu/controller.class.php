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
      
      $date = $this->getDate();
      
      $model = new DayMenu_Model();
      if($this->rights()->isWritable()){
         $model->where(DayMenu_Model::COLUMN_DATE.' = :d', array('d' => $date->format("Y-m-d")));
      } else {
         $model->where(DayMenu_Model::COLUMN_DATE.' = :d AND '.DayMenu_Model::COLUMN_CONCEPT.' = 0', array('d' => $date->format("Y-m-d")));
      }
      
      $text = $model->record();
      
      $this->view()->text = $text;
      $this->view()->date = $date;
   }

   private function getDate()
   {
      $date = $this->getRequestParam('date');
      
      if($date == null){
         $date = new DateTime();
         if(date("H") > 14){
            $date->modify("+1 day");
         }
      } else {
         $date = new DateTime($date);
      }
      return $date;
   }


   /**
    * Kontroler pro editaci textu
    */
   public function editController() {
      $this->checkWritebleRights();
      $date = $this->getDate();

      $form = new Form("text_");
      
      $textarea = new Form_Element_TextArea('text', $this->tr("Text"));
      $textarea->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      $form->addElement($textarea);
      
      $textP = new Form_Element_TextArea('textPanel', $this->tr("Text v panelu"));
      $form->addElement($textP);
      
      $eDate = new Form_Element_Text('date', $this->tr('Datum'));
      $eDate->setSubLabel($this->tr('POZOR: Změna data přepíše menu na zadaný den, pokud bylo zadáno.'));
      $eDate->addValidation(new Form_Validator_NotEmpty());
      $eDate->addValidation(new Form_Validator_Date());
      $eDate->addFilter(new Form_Filter_DateTimeObj());
      $eDate->setValues(vve_date("%x", $date));
      $form->addElement($eDate);
      
      $eConcept = new Form_Element_Checkbox('concept', $this->tr('Koncept'));
      $eConcept->setSubLabel($this->tr('Pokud je položka koncept, není zobrazena normálním uživatelům.'));
      $form->addElement($eConcept);

      $model = new DayMenu_Model();
      
      $record = $model->where(DayMenu_Model::COLUMN_DATE.' = :d', array('d' => $date->format("Y-m-d")))->record();
      
      if($record != false){
         $form->text->setValues($record->{DayMenu_Model::COLUMN_TEXT});
         $form->textPanel->setValues($record->{DayMenu_Model::COLUMN_TEXT_PANEL});
         $form->concept->setValues($record->{DayMenu_Model::COLUMN_CONCEPT});
      } else {
         $record = $model->newRecord();
         // použije se předdefinovaná šablona pokud je nastavena
         $modelTpl = new Templates_Model();
         if($this->category()->getParam('tplid', 0) != 0){
            $tpl = $modelTpl->record($this->category()->getParam('tplid'));
            if($tpl != false){
               $form->text->setValues($tpl->{Templates_Model::COLUMN_CONTENT});
            }
         }
         if($this->category()->getParam('tplpid', 0) != 0){
            $tpl = $modelTpl->record($this->category()->getParam('tplpid'));
            if($tpl != false){
               $form->textPanel->setValues($tpl->{Templates_Model::COLUMN_CONTENT});
            }
         }
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
            $textClear = strip_tags($form->text->getValues());
            $textPanel = vve_strip_html_comment($form->textPanel->getValues());

            $record->{DayMenu_Model::COLUMN_TEXT} = $text;
            $record->{DayMenu_Model::COLUMN_TEXT_CLEAR} = $textClear;
            $record->{DayMenu_Model::COLUMN_TEXT_PANEL} = $textPanel;
            $record->{DayMenu_Model::COLUMN_DATE} = $form->date->getValues();
            $record->{DayMenu_Model::COLUMN_CONCEPT} = $form->concept->getValues();
            
            // kontrola data, pokud je již záznam v db, musí se smazat jinak jsou tam dva
            if($form->date->getValues()->format('Y-m-d') != $date->format('Y-m-d')){
               $model->where(DayMenu_Model::COLUMN_DATE.' = :d', array('d' => $form->date->getValues()->format('Y-m-d')))->delete();
            }
            // uložení
            $model->save($record);
            
            $this->log('úprava textu');
            $this->infoMsg()->addMessage($this->tr('Menu bylo uloženo'));
            $this->link()->route()->reload();
         } catch (PDOException $e) {
            new CoreErrors($e);
         }
      }
      // view
      $this->view()->template()->form = $form;
      $this->view()->date = $date;
   }

   public function settings(&$settings, Form &$form) {
      $fGrpViewSet = $form->addGroup('view', $this->tr('Nastavení vzhledu'));
      
      $fGrpEditSet = $form->addGroup('editSettings', $this->tr('Nastavení úprav'));

      $eDefaultTplSelect = new Form_Element_Select('tplid', $this->tr('Šablona obsahu'));
      $eDefaultPanelTplSelect = new Form_Element_Select('tplpid', $this->tr('Šablona obsahu panelu'));
      
      $modelTpls = new Templates_Model();
      
      $templates = $modelTpls->where(Templates_Model::COLUMN_TYPE.' = :type', array('type' => Templates_Model::TEMPLATE_TYPE_TEXT))->records();
      
      if($templates != false){
         $eDefaultTplSelect->setOptions(array($this->tr('Žádná') => 0));
         $eDefaultPanelTplSelect->setOptions(array($this->tr('Žádná') => 0));
         foreach ($templates as $tpl) {
            $eDefaultTplSelect->setOptions(array($tpl->{Templates_Model::COLUMN_NAME} => $tpl->{Templates_Model::COLUMN_ID}), true);
            $eDefaultPanelTplSelect->setOptions(array($tpl->{Templates_Model::COLUMN_NAME} => $tpl->{Templates_Model::COLUMN_ID}), true);
         }
         if(isset ($settings['tplid'])){
            $eDefaultTplSelect->setValues($settings['tplid']);
         }
         if(isset ($settings['tplpid'])){
            $eDefaultPanelTplSelect->setValues($settings['tplpid']);
         }
         $form->addElement($eDefaultTplSelect, $fGrpEditSet);
         $form->addElement($eDefaultPanelTplSelect, $fGrpEditSet);
      }
      
      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings['tplid'] = $form->tplid->getValues();
         $settings['tplpid'] = $form->tplpid->getValues();
      }
   }
}

?>