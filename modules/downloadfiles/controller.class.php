<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class DownloadFiles_Controller extends Controller {
   const PARAM_ALLOWED_TYPES = 'ft';
   const PARAM_COLS = 'cols';
   const PARAM_PASS = 'pass';

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
      
      $pass = $this->category()->getParam(self::PARAM_PASS);
      $session = new Session('dwfiles-'.$this->category()->getId());
      
      $loadFiles = false;
      if($pass == null || $this->category()->getRights()->isWritable() || $session->get()){
         $loadFiles = true;
      } else {
         
         $fLogin = new Form('login_');
         $grp = $fLogin->addGroup('pass', $this->tr('Pro přístup zadejte heslo'));
         $ePass = new Form_Element_Password('pass', $this->tr('Heslo'));
         $ePass->addValidation(new Form_Validator_NotEmpty());
         $fLogin->addElement($ePass, $grp);
         
         $send = new Form_Element_Submit('login', $this->tr('Přihlásit'));
         $fLogin->addElement($send, $grp);
         
         if($fLogin->isSend() && $ePass->isValid() && $fLogin->pass->getValues() != $pass){
            $ePass->setError($this->tr('Bylo zadáno nesprávné heslo pro přístup'));
         }
         
         if($fLogin->isValid()){
            // set session
            $session->set(true);
            $this->link()->redirect();
         }
         $this->view()->formLogin = $fLogin;
      }
      
      if($loadFiles){
         // load items
         $model = new DownloadFiles_Model();

         if($this->rights()->isWritable()){
            if($this->getRequestParam('activate', false)){
               $model
                  ->where(DownloadFiles_Model::COLUMN_ID." = :id", array('id' => $this->getRequestParam('activate')))
                  ->update(array(DownloadFiles_Model::COLUMN_ACTIVE => true));
               $this->link()->rmParam('activate')->reload();
            }
         }

         $files = $model
               ->where(DownloadFiles_Model::COLUMN_ID_CATEGORY.' = :idc '
                  . ($this->rights()->isWritable() == false ? ' AND '.DownloadFiles_Model::COLUMN_ACTIVE." = 1" : null),
                     array('idc' => $this->category()->getId()))
               ->order(array(
                  DownloadFiles_Model::COLUMN_COLUMN => Model_ORM::ORDER_ASC, 
                  DownloadFiles_Model::COLUMN_TIME_ADD => Model_ORM::ORDER_DESC,
                  ))
               ->records();

         $this->view()->files = $files;
         $this->view()->dataDir = $this->module()->getDataDir(true);
      }
   }
   
   
   public function addController() {
      $this->checkWritebleRights();
      
      $form = $this->createForm();
      
      if($form->isValid()){
         $model = new DownloadFiles_Model();
         $fileRec = $model->newRecord();
         
         $file = $form->file->getValues();
         
         $fileRec->{DownloadFiles_Model::COLUMN_ID_CATEGORY} = $this->category()->getId();
         $fileRec->{DownloadFiles_Model::COLUMN_ID_USER} = Auth::getUserId();
         $fileRec->{DownloadFiles_Model::COLUMN_FILE} = $file['name'];
         $fileRec->{DownloadFiles_Model::COLUMN_NAME} = $form->name->getValues();
         $fileRec->{DownloadFiles_Model::COLUMN_TEXT} = $form->text->getValues();
         $fileRec->{DownloadFiles_Model::COLUMN_ACTIVE} = $form->active->getValues();
         if(isset ($form->column)){
            $fileRec->{DownloadFiles_Model::COLUMN_COLUMN} = $form->column->getValues();
         }
         
         $model->save($fileRec);
         $this->infoMsg()->addMessage($this->tr('Soubor byl uložen'));
         $this->log('nahran soubor ke stazeni: '. $file['name']);
         $this->link()->route()->reload();
      }
      
      $this->view()->form = $form;
      
   }
   
   public function editController() {
      $this->checkWritebleRights();
      
      $model = new DownloadFiles_Model();
      
      $fileRec = $model->record($this->getRequest('id'));
      
      if(!$this->checkValidEditFileRecord($fileRec) ){
         return false;
      }
      
      $form = $this->createForm($fileRec);
      
      if($form->isValid()){
         $file = $form->file->getValues();

         if($file != null){
            if($fileRec->{DownloadFiles_Model::COLUMN_FILE} != $file['name'] 
               && is_file($this->module()->getDataDir().$fileRec->{DownloadFiles_Model::COLUMN_FILE})){
               @unlink($this->module()->getDataDir().$fileRec->{DownloadFiles_Model::COLUMN_FILE});
            }
            $fileRec->{DownloadFiles_Model::COLUMN_FILE} = $file['name'];
            $this->log('nahran soubor ke stazeni: '. $file['name']);
         }
         
         $fileRec->{DownloadFiles_Model::COLUMN_NAME} = $form->name->getValues();
         $fileRec->{DownloadFiles_Model::COLUMN_TEXT} = $form->text->getValues();
         $fileRec->{DownloadFiles_Model::COLUMN_ACTIVE} = $form->active->getValues();
         if(isset ($form->column)){
            $fileRec->{DownloadFiles_Model::COLUMN_COLUMN} = $form->column->getValues();
         }
         
         $model->save($fileRec);
         $this->infoMsg()->addMessage($this->tr('Soubor byl uložen'));
         $this->link()->route()->reload();
      }
      
      $this->view()->form = $form;
      $this->view()->file = $fileRec;
   }

   private function createForm(Model_ORM_Record $fileObj = null)
   {
      $form = new Form('dwfile_');
      
      $elemName = new Form_Element_Text('name', $this->tr('Název'));
      $elemName->setLangs();
      $elemName->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      $elemName->addFilter(new Form_Filter_StripTags());
      $form->addElement($elemName);
      
      $elemText = new Form_Element_TextArea('text', $this->tr('Popis'));
      $elemText->addFilter(new Form_Filter_StripTags());
      $elemText->setLangs();
      $form->addElement($elemText);
      
      $elemFile = new Form_Element_File('file', $this->tr('Soubor'));
      $elemFile->setUploadDir($this->module()->getDataDir());
      $elemFile->addValidation(new Form_Validator_NotEmpty());
      $elemFile->addValidation(new Form_Validator_FileExtension(
         $this->category()->getParam(self::PARAM_ALLOWED_TYPES, Form_Validator_FileExtension::ALL)));
      $form->addElement($elemFile);

      if($this->category()->getParam(self::PARAM_COLS, 1) > 1){
         $elemCol = new Form_Element_Select('column', $this->tr('Sloupec'));
         for ($col = 1; $col <= $this->category()->getParam(self::PARAM_COLS, 1); $col++) {
            $elemCol->setOptions(array($this->tr("Sloupec ").$col => (string)$col), true);
         }
         $form->addElement($elemCol);
      }

      $elemActive = new Form_Element_Checkbox('active', $this->tr('Aktivní'));
      $elemActive->setValues(true);
      $form->addElement($elemActive);
      
      $elemSave = new Form_Element_SaveCancel('save');
      $form->addElement($elemSave);
      
      if($fileObj != null){
         $form->name->setValues($fileObj->{DownloadFiles_Model::COLUMN_NAME});
         $form->text->setValues($fileObj->{DownloadFiles_Model::COLUMN_TEXT});
         $form->active->setValues($fileObj->{DownloadFiles_Model::COLUMN_ACTIVE});
         $form->file->setSubLabel(sprintf($this->tr('Nahraný soubor: <strong>%s</strong>. Pokud nahrajete nový, dojde k přepsání.'), $fileObj->{DownloadFiles_Model::COLUMN_FILE}));
         $form->file->removeValidation('Form_Validator_NotEmpty');
         if(isset ($form->column)){
            $form->column->setValues($fileObj->{DownloadFiles_Model::COLUMN_COLUMN});
         }
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
      $form = new Form('delete_dwfile_');
      $elemName = new Form_Element_Hidden('id');
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemName);

      $elemSubmit = new Form_Element_Submit('submit', $this->tr('Smazat'));
      $form->addElement($elemSubmit);

      if($form->isValid()){
         $model = new DownloadFiles_Model();
         $fileRec = $model->record($form->id->getValues());
         // PROČ ????
         if(!$this->checkValidEditFileRecord($fileRec)){
            $this->log(sprintf('pokus o smazání souboru "%s" z cizí kategorie', $fileRec->{DownloadFiles_Model::COLUMN_FILE}));
            throw new InvalidArgumentException($this->tr('Tento soubor nelze smazat. nepatří do dané kategorie'));
         }
         
         if(is_file($this->module()->getDataDir().$fileRec->{DownloadFiles_Model::COLUMN_FILE})){
            @unlink($this->module()->getDataDir().$fileRec->{DownloadFiles_Model::COLUMN_FILE});
         }
         $model->delete($fileRec);
         $this->infoMsg()->addMessage($this->tr('Soubor byl smazán'));
         $this->link()->route()->reload();
      }
      $this->view()->formDelete = $form;
   }

   private function checkValidEditFileRecord(Model_ORM_Record $record)
   {
      if($record == false || ($record instanceof Model_ORM_Record && $record->isNew())
            || $record->{DownloadFiles_Model::COLUMN_ID_CATEGORY} != $this->category()->getId()){
         return false;
      }
      return true;
   }
   
   protected function settings(&$settings, Form &$form) {
      $fGrp = $form->addGroup('access', $this->tr('Přístup'));

      $elemPass = new Form_Element_Text('pass', $this->tr('Heslo pro přístup'));
      $elemPass->setSubLabel($this->tr('Pokud je heslo zadáno, položky se vypíší až po zadání hesla'));
      $form->addElement($elemPass, $fGrp);

      if(isset($settings[self::PARAM_PASS])) {
         $form->pass->setValues($settings[self::PARAM_PASS]);
      }
      
      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings[self::PARAM_PASS] = $form->pass->getValues();
      }
   }
}
