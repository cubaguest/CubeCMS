<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class PressReports_Controller extends Controller {

   /**
 * Kontroler pro zobrazení textu
 */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      // pokud nebyl datový adresář vytvořen, vytvoří se
      if(!is_dir($this->module()->getDataDir())){
         mkdir($this->module()->getDataDir(), 0777, true);
      }

      // mazání položky/položek
      $this->checkDeleteItem();
      
      // load items
      $model = new PressReports_Model();
      
      $reports = $model
            ->where(PressReports_Model::COLUMN_ID_CATEGORY.' = :idc', array('idc' => $this->category()->getId()))
            ->order(array( PressReports_Model::COLUMN_TIME_ADD => Model_ORM::ORDER_DESC ))
            ->records();
      
      $this->view()->reports = $reports;
      $this->view()->dataDir = $this->module()->getDataDir(true);
   }
   
   
   public function addController() {
      $this->checkWritebleRights();
      
      $form = $this->createForm();
      
      if($form->isValid()){
         $model = new PressReports_Model();
         $report = $model->newRecord();
         
         $file = $form->file->getValues();
         
         $report->{PressReports_Model::COLUMN_ID_CATEGORY} = $this->category()->getId();
         $report->{PressReports_Model::COLUMN_ID_USER} = Auth::getUserId();
         $report->{PressReports_Model::COLUMN_FILE} = $file['name'];
         $report->{PressReports_Model::COLUMN_NAME} = $form->name->getValues();
         $report->{PressReports_Model::COLUMN_AUTHOR} = $form->author->getValues();
         
         $model->save($report);
         $this->infoMsg()->addMessage($this->tr('Zpráva byla uložena'));
         $this->log('nahrán soubor tiskove zprávy: '. $file['name']);
         $this->link()->route()->reload();
      }
      $this->view()->form = $form;
   }
   
   public function editController() {
      $this->checkWritebleRights();
      
      $model = new PressReports_Model();
      
      $report = $model->record($this->getRequest('id'));
      
      if(!$this->checkValidEditFileRecord($report) ){
         return false;
      }
      
      $form = $this->createForm($report);
      
      if($form->isValid()){
         $file = $form->file->getValues();

         if($file != null){
            if($report->{PressReports_Model::COLUMN_FILE} != $file['name'] 
               && is_file($this->module()->getDataDir().$report->{PressReports_Model::COLUMN_FILE})){
               @unlink($this->module()->getDataDir().$report->{PressReports_Model::COLUMN_FILE});
            }
            $report->{PressReports_Model::COLUMN_FILE} = $file['name'];
            $this->log('nahran soubor ke stazeni: '. $file['name']);
         }
         
         $report->{PressReports_Model::COLUMN_NAME} = $form->name->getValues();
         $report->{PressReports_Model::COLUMN_AUTHOR} = $form->author->getValues();
         
         $model->save($report);
         $this->infoMsg()->addMessage($this->tr('Tisková zpráva byla uložena'));
         $this->link()->route()->reload();
      }
      
      $this->view()->form = $form;
      $this->view()->message = $message;
   }

   private function createForm(Model_ORM_Record $report = null)
   {
      $form = new Form('dwfile_');
      
      $elemName = new Form_Element_Text('name', $this->tr('Název'));
      $elemName->setLangs();
      $elemName->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      $elemName->addFilter(new Form_Filter_StripTags());
      $form->addElement($elemName);
      
      $elemAuthor = new Form_Element_Text('author', $this->tr('Autor'));
      $elemAuthor->addFilter(new Form_Filter_StripTags());
      $form->addElement($elemAuthor);
      
//       $elemText = new Form_Element_TextArea('text', $this->tr('Popis'));
//       $elemText->addFilter(new Form_Filter_StripTags());
//       $elemText->setLangs();
//       $form->addElement($elemText);
      
      $elemFile = new Form_Element_File('file', $this->tr('Soubor'));
      $elemFile->setUploadDir($this->module()->getDataDir());
      $elemFile->addValidation(new Form_Validator_NotEmpty());
      $elemFile->addValidation(new Form_Validator_FileExtension(Form_Validator_FileExtension::DOC));
      $form->addElement($elemFile);
      
      $elemSave = new Form_Element_SaveCancel('save');
      $form->addElement($elemSave);
      
      if($report != null){
         $form->name->setValues($report->{PressReports_Model::COLUMN_NAME});
         $form->author->setValues($report->{PressReports_Model::COLUMN_AUTHOR});
//          $form->text->setValues($report->{DownloadFiles_Model::COLUMN_TEXT});
         $form->file->setSubLabel(sprintf($this->tr('Nahraný soubor: <strong>%s</strong>. Pokud nahrajete nový, dojde k přepsání.'), 
               $report->{PressReports_Model::COLUMN_FILE}));
         $form->file->removeValidation('Form_Validator_NotEmpty');
      }
      
      if($form->isSend() && $form->save->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }
      
      return $form;
   }

   protected function checkDeleteItem()
   {
      if(!$this->rights()->isWritable() && !$this->rights()->isControll()){
         return;
      }
      $form = new Form('delete_press_report_');
      $elemName = new Form_Element_Hidden('id');
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemName);

      $elemSubmit = new Form_Element_Submit('submit', $this->tr('Smazat'));
      $form->addElement($elemSubmit);

      if($form->isValid()){
         $model = new PressReports_Model();
         $report = $model->record($form->id->getValues());
         
         if(!$this->checkValidEditFileRecord($report)){
            $this->log(sprintf('pokus o smazání souboru "%s" z cizí kategorie', $report->{PressReports_Model::COLUMN_NAME}));
            throw new InvalidArgumentException($this->tr('Tuto tiskovou zprávu nelze smazat, nepatří do dané kategorie'));
         }
         
         if(is_file($this->module()->getDataDir().$report->{PressReports_Model::COLUMN_FILE})){
            @unlink($this->module()->getDataDir().$report->{PressReports_Model::COLUMN_FILE});
         }
         $model->delete($fileRec);
         $this->infoMsg()->addMessage($this->tr('Tisková zpráva byla smazána'));
         $this->link()->route()->reload();
      }
      $this->view()->formDelete = $form;
   }

   private function checkValidEditFileRecord(Model_ORM_Record $record)
   {
      if($record == false || ($record instanceof Model_ORM_Record && $record->isNew())
            || $record->{PressReports_Model::COLUMN_ID_CATEGORY} != $this->category()->getId()){
         return false;
      }
      return true;
   }
   
}

?>
