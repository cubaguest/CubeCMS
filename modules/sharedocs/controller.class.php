<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 * @todo
 * - řazení uživatelů ve výpisu práv
 * - Smazání souborů při mazání adresáře
 * - Mazání adresáře z jeho detailu
 */

class ShareDocs_Controller extends Controller {
   const DATA_DIR = 'sharedocs';

   const TOKEN_MAX_HOURS = 48;

   const RIGHT_NONE = 0;
   const RIGHT_READ_ONLY = 1;
   const RIGHT_WRITE = 2;
   
   const PARAM_NUMBER_STORED_REVS = 'nsr';

   protected function init()
   {
      parent::init();
      $this->module()->setDataDir(self::DATA_DIR);
   }

      /**
 * Kontroler pro zobrazení textu
 */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      $modelDirs = new ShareDocs_Model_Dirs();
      
      if($this->rights()->isControll()){
         
         $formEdit = new Form('edit_dir');
         
         $elemName = new Form_Element_Text('name', $this->tr('Název'));
         $elemName->addValidation(new Form_Validator_NotEmpty());
         $elemName->addFilter(new Form_Filter_StripTags());
         $formEdit->addElement($elemName);
         
         $elemTitle = new Form_Element_TextArea('title', $this->tr('Popisek'));
         $elemTitle->addFilter(new Form_Filter_StripTags());
         $formEdit->addElement($elemTitle);
         
         $elemERights = new Form_Element_Checkbox('editRights', $this->tr('Přejít k úpravě práv'));
         $formEdit->addElement($elemERights);
         
         $elemDirId = new Form_Element_Hidden('id', $this->tr('ID složky'));
         $formEdit->addElement($elemDirId);
         
         $elemSend = new Form_Element_Submit('send', $this->tr('Uložit'));
         $formEdit->addElement($elemSend);
         
         if($formEdit->isValid()){
            if($formEdit->id->getValues() == null){
               // new record
               $record = $modelDirs->newRecord();
            } else {
               // edit record
               $record = $modelDirs->record($formEdit->id->getValues());
            }
            
            $record->{ShareDocs_Model_Dirs::COLUMN_NAME} = $formEdit->name->getValues();
            $record->{ShareDocs_Model_Dirs::COLUMN_TITLE} = $formEdit->title->getValues();
            $record->{ShareDocs_Model_Dirs::COLUMN_ID_CATEGORY} = $this->category()->getId();
            $record->{ShareDocs_Model_Dirs::COLUMN_DATE_LAST_CHANGE} = new DateTime();

            $modelDirs->save($record);
            
            // rights?
            $this->infoMsg()->addMessage($this->tr('Složka byla uložena'));
            
            if($formEdit->editRights->getValues() == true){
               $this->link()->route('editDirAccess', array('iddir' => $record->getPK()))->param('back', 'detail')->reload();
            }
            $this->link()->reload();
         }
         
         $this->view()->formEditDir = $formEdit;
         
         $this->tryFormDeleteDir();
      }
      
      
		if( $this->rights()->isControll() ){
			// načtení adresářů bez omezení
      	$modelDirs
            ->join(ShareDocs_Model_Dirs::COLUMN_ID, array("tshf" => "ShareDocs_Model_Files" ), ShareDocs_Model_Files::COLUMN_ID_DIRECTORY )
            ->join(array("tshf" => ShareDocs_Model_Files::COLUMN_ID), array("tshfr" => "ShareDocs_Model_Revs" ), 
               ShareDocs_Model_Revs::COLUMN_ID_FILE, array(ShareDocs_Model_Revs::COLUMN_DATE_ADD))
                  
         	->order(array(
               ShareDocs_Model_Dirs::COLUMN_NAME => Model_ORM::ORDER_ASC,
               ShareDocs_Model_Revs::COLUMN_DATE_ADD => Model_ORM::ORDER_DESC,
               ))
         	->where(ShareDocs_Model_Dirs::COLUMN_ID_CATEGORY.' = :idc', 
               array('idc' => $this->category()->getId()))
            ->groupBy( array( ShareDocs_Model_Dirs::COLUMN_ID ) );
         	
         $this->view()->controll = true;
      } else {
         $modelDirs
            ->order(array(ShareDocs_Model_Dirs::COLUMN_NAME => Model_ORM::ORDER_ASC))
            // Info o změně
            ->join(ShareDocs_Model_Dirs::COLUMN_ID, array("tshf" => "ShareDocs_Model_Files" ), ShareDocs_Model_Files::COLUMN_ID_DIRECTORY )
            ->join(array("tshf" => ShareDocs_Model_Files::COLUMN_ID), array("tshfr" => "ShareDocs_Model_Revs" ), 
               ShareDocs_Model_Revs::COLUMN_ID_FILE, array(ShareDocs_Model_Revs::COLUMN_DATE_ADD) )
            // oprávnění      
            ->join(ShareDocs_Model_Dirs::COLUMN_ID, array("tgrps" => "ShareDocs_Model_GroupsAcc" ), 
            ShareDocs_Model_GroupsAcc::COLUMN_ID_DIR )
            ->join(ShareDocs_Model_Dirs::COLUMN_ID, array("tusrs" => "ShareDocs_Model_UsersAcc" ), 
            ShareDocs_Model_UsersAcc::COLUMN_ID_DIR )
            ->where(ShareDocs_Model_Dirs::COLUMN_ID_CATEGORY.' = :idc AND '
            ."( "
            ."tgrps.".ShareDocs_Model_GroupsAcc::COLUMN_ID_GROUP." = :idg OR "
            ."tusrs.".ShareDocs_Model_UsersAcc::COLUMN_ID_USER." = :idu OR "
            .ShareDocs_Model_Dirs::COLUMN_IS_PUBLIC." = 1"
            .") " , 
            array(
               "idc" => $this->category()->getId(),
               "idu" => Auth::getUserId(),
               "idg" => Auth::getGroupId(),
            ))
            ->groupBy( array( ShareDocs_Model_Dirs::COLUMN_ID ) );
            
      }
      
      $this->view()->controll = false;
      if($this->rights()->isControll() ){
         $this->view()->controll = true;
      }
      $dirs = $modelDirs->records();
      $this->view()->dirs = $dirs;
      
      self::runTokenClearer();
   }
   
   protected function tryFormDeleteDir(){
      $formDelete = new Form('del_dir_');
         
      $formId = new Form_Element_Hidden('id');
      $formDelete->addElement($formId);
         
      $elemDel = new Form_Element_Submit('delete', $this->tr('Smazat složku'));
      $formDelete->addElement($elemDel);
         
      if($formDelete->isValid()){
         $this->deleteDir($formDelete->id->getValues() );
         $this->infoMsg()->addMessage($this->tr('Složka včetně všech souborů byla smazána'));
         $this->link()->route()->reload();
      }
         
      $this->view()->formDelDir = $formDelete;
   }


   protected function deleteDir($dirId)
   {
      $modelDirs = new ShareDocs_Model_Dirs();
      if($dirId != null ){
         // @TODO delete files
         
         $modelDirs->delete((int)$dirId);
      }
   }
   
   protected function deleteFile($fileId)
   {
      $modelRevs = new ShareDocs_Model_Revs();
      
      $revs = $modelRevs->where(ShareDocs_Model_Revs::COLUMN_ID_FILE." = :idf",
         array('idf' => $fileId ) )->records();
      if($revs == false ){
         return;
      }
      foreach($revs as $rev){
         try {
            $file = new File($rev->{ShareDocs_Model_Revs::COLUMN_FILENAME}, $this->getModule()->getDataDir());
            $file->delete();
         } catch (Exception $e) {
            // tady asi logování
         }
      }
      
      // delete file record
      $modelFiles = new ShareDocs_Model_Files();
      $modelFiles->delete($fileId );
      
   }
   
   /**
    * Controller zobrazí obsah adresáře
    * @return boolean 
    */
   public function dirListController()
   {
      $this->checkReadableRights();
      $modelDirs = new ShareDocs_Model_Dirs();
      
      $iddir =  $this->getRequest('iddir', 0);
      $directory = $modelDirs->record($iddir);
      
      
      // kontrola přístupu k adresáři
      $right = $this->getDirectoryRights($directory);
      
      if($right == self::RIGHT_NONE || $directory == false || $directory->isNew()){
         return false;
      }
      
      $this->view()->directory = $directory;
      
      $modelFiles = new ShareDocs_Model_Files();
      $files = $modelFiles->where(ShareDocs_Model_Files::COLUMN_ID_DIRECTORY." = :idd", array('idd' => $iddir))->records();
      
      $this->view()->files = $files;
      
      $this->view()->controll = false;
		if($this->rights()->isControll()) {
         $this->tryFormDeleteDir();
         $this->view()->controll = true;
      }      
      
      $this->view()->right = $right;
   }

   // **** OBSLUHA SOUBORŮ **** //
   
   public function editFileController()
   {
      $this->checkReadableRights();
      $modelDir = new ShareDocs_Model_Dirs();
      
      $dirRec = $modelDir->record($this->getRequest('iddir', 0));
      
      // kontrola práv k přístupu do adresáře
      $right = $this->getDirectoryRights($dirRec);
      
      if($right != self::RIGHT_WRITE || $dirRec == false || $dirRec->isNew()){
         return false;
      }
      $idDir = $dirRec->{ShareDocs_Model_Dirs::COLUMN_ID};
      
      
      $modelF = new ShareDocs_Model_Files();
      $modelRev = new ShareDocs_Model_Revs();
      
      $idFile = $this->getRequest('idfile', 0);
      if($idFile != 0){
         $fileRec = $modelF->record($idFile);
      } else {
         $fileRec = $modelF->newRecord();
      }
      
      if($fileRec == false){
         return false;
      }
      
      
      $formEditFile = new Form('edit_file_');
      
      $elemName = new Form_Element_Text('name', $this->tr('Název'));
      $elemName->setSubLabel($this->tr('Pokud není zadán, použije se originální název souboru.'));
      $elemName->addFilter(new Form_Filter_StripTags());
      $formEditFile->addElement($elemName);
      
      $elemTitle = new Form_Element_TextArea('title', $this->tr('Popis'));
      $elemTitle->addFilter(new Form_Filter_StripTags());
      $formEditFile->addElement($elemTitle);
      
      $elemFile = new Form_Element_File('file', $this->tr('Soubor'));
      $elemFile->addValidation(new Form_Validator_NotEmpty());
      $formEditFile->addElement($elemFile);
      
      
      if(!$fileRec->isNew()){
         $formEditFile->file->removeValidation('Form_Validator_NotEmpty');
         $formEditFile->file->setSubLabel($this->tr('Při nahrání nového souoru bude vytvořena další revize dokumentu.'));
         $formEditFile->name->addValidation(new Form_Validator_NotEmpty());
         
         $formEditFile->name->setValues($fileRec->{ShareDocs_Model_Files::COLUMN_NAME});
         $formEditFile->title->setValues($fileRec->{ShareDocs_Model_Files::COLUMN_TITLE});
         
         $elemNote = new Form_Element_TextArea('note', $this->tr('Poznámka k nové revizi'));
         $elemNote->addFilter(new Form_Filter_StripTags());
         $formEditFile->addElement($elemNote);
      }
      
      $elemSave = new Form_Element_SaveCancel('save');
      $formEditFile->addElement($elemSave);
      
      if($formEditFile->isSend() && $formEditFile->save->getValues() == false){
         if(!$fileRec->isNew()){
            $this->link()->route('file')->reload();
         } else {
            $this->link()->route('dirList')->reload();
         }
      }
      
      if($formEditFile->isValid()){
         
         $file = null;
         if($formEditFile->file->getValues() != null){
            $file = new File($formEditFile->file);
            $fileName = $this->createUniqueFileName($file);
            $file->copy($this->getModule()->getDataDir(), false, $fileName);
            // pokud je nahrán nový soubor dojde k odemknutí
            $fileRec->{ShareDocs_Model_Files::COLUMN_LOCKED} = false;
         }
         
         $fileRec->{ShareDocs_Model_Files::COLUMN_ID_DIRECTORY} = $idDir;
         $fileRec->{ShareDocs_Model_Files::COLUMN_TITLE} = $formEditFile->title->getValues();
         $fileRec->{ShareDocs_Model_Files::COLUMN_NAME} = $formEditFile->name->getValues() != null ? $formEditFile->name->getValues() : $fileRec->{ShareDocs_Model_Files::COLUMN_NAME};
         
         $modelF->save($fileRec);
         
         if($file instanceof File){
            $revRecord = $modelRev->newRecord();
            $revRecord->{ShareDocs_Model_Revs::COLUMN_ID_FILE} = $fileRec->getPK();
            $revRecord->{ShareDocs_Model_Revs::COLUMN_FILENAME} = $fileName;
            $revRecord->{ShareDocs_Model_Revs::COLUMN_ORIG_FILENAME} = $file->getName();
         
            if(isset ($formEditFile->note)){
               $revRecord->{ShareDocs_Model_Revs::COLUMN_NOTE} = $formEditFile->note->getValues();
            } else {
               $revRecord->{ShareDocs_Model_Revs::COLUMN_NOTE} = $this->tr('Nahrání souboru');
            }
            $revRecord->{ShareDocs_Model_Revs::COLUMN_ID_USER} = Auth::getUserId();
            
            $lastRev = $modelRev
               ->where(ShareDocs_Model_Revs::COLUMN_ID_FILE." = :idf", array('idf' => $fileRec->getPK()))
               ->order(array(ShareDocs_Model_Revs::COLUMN_NUMBER => Model_ORM::ORDER_DESC))
               ->record();
            $revRecord->{ShareDocs_Model_Revs::COLUMN_NUMBER} = $lastRev == false ? 1 : $lastRev->{ShareDocs_Model_Revs::COLUMN_NUMBER};
            
            $modelRev->save($revRecord);
         }
         
         $this->cleanOldRevisions($fileRec);
         // info + redirect to file
         $this->log( sprintf('Práce se souborem id: %s name: %s', $fileRec->getPK(), $fileRec->{ShareDocs_Model_Files::COLUMN_NAME} ) );
         $this->infoMsg()->addMessage($this->tr('Soubor byl uložen'));
         $this->link()->route('file', array('idfile' => $fileRec->getPK()))->reload();
      }
      
      $this->view()->formFile = $formEditFile;
      $this->view()->file = $fileRec;
      $this->view()->dir = $dirRec;
      
      $this->view()->right = $right;
   }
   
   public function fileController()
   {
      $this->checkReadableRights();
      
      self::runTokenClearer();
      $idFile = $this->getRequest('idfile', 0);
      
      $modelFile = new ShareDocs_Model_Files();
      
      $fileRec = $modelFile
         ->where(ShareDocs_Model_Files::COLUMN_ID.' = :idf', array('idf' => $idFile))
         ->join(ShareDocs_Model_Files::COLUMN_ID, array('frevs' => 'ShareDocs_Model_Revs'), ShareDocs_Model_Revs::COLUMN_ID_FILE)
         ->join(array('frevs' => ShareDocs_Model_Revs::COLUMN_ID_USER), 'Model_Users', Model_Users::COLUMN_ID, array(
            Model_Users::COLUMN_NAME, Model_Users::COLUMN_SURNAME, Model_Users::COLUMN_USERNAME, 
         ))
         ->order(array('frevs.'.ShareDocs_Model_Revs::COLUMN_DATE_ADD => Model_ORM::ORDER_DESC))
         ->record();
      
      // kontrola práv k adresáři
      $right = $this->getDirectoryRights($fileRec->{ShareDocs_Model_Files::COLUMN_ID_DIRECTORY});
      
      if($right == self::RIGHT_NONE || $fileRec == false || $fileRec->isNew()){
         return false;
      }
      
      /*
       * Stažení souboru
       * 
       * Protože nejde odeslat dvě odpovědi najednou, je stáhnutí a zamknutí aktuální verze v odlišných odpovědích. 
       * Soubor je stáhnut přes iframe, ale zamknut přes form.
       */
      // pokud je v requestu stažení dojde ke stažení $_GET parametr "dw"
      if($this->getRequestParam('dw', false) != false){
         $this->link()->rmParam('dw');
         self::sendFileToClient($fileRec);
      }
      
      // stažení souboru bez zamykání nabo zamykýní
      $formDownload = new Form('file_download_');
      
      // lock pouze pokud není uživatel nebo skupina readonly
      // TODO 
      if($fileRec->{ShareDocs_Model_Files::COLUMN_LOCKED} == false && $right == self::RIGHT_WRITE){
         $elemLock = new Form_Element_Checkbox('lock', $this->tr('Zamknout soubor'));
         $elemLock->setValues(true);
         $formDownload->addElement($elemLock);
      }
      
      $elemFileRev = new Form_Element_Hidden('rev');
      $elemFileRev->setValues(null);
      $formDownload->addElement($elemFileRev);
      
      $elemSend = new Form_Element_Submit('donwload', $this->tr('Stáhnout soubor'));
      $formDownload->addElement($elemSend);
      
      if($formDownload->isValid()){
         if(isset ($formDownload->lock) && $formDownload->lock->getValues() == true){
            $this->lockFile($fileRec, true, Auth::getUserId());
         }
         
         // revize
         if($formDownload->rev->getValues() != null){
            self::sendFileToClient($fileRec, $formDownload->rev->getValues());
         } else {
            // stažení aktuální podoby
            $this->view()->downloadFile = true;
         }
      }
      
      $this->view()->formDownload = $formDownload;
      
      /*
       *  Změna zámku pouze pokud jsou práva pro zápis
       */
      if($right == self::RIGHT_WRITE){
         $formChangeFileLock = new Form('change_file_lock_');
         $elemLock = new Form_Element_Hidden('lock', $this->tr('Změnit zámek'));
         $formChangeFileLock->addElement($elemLock);
         $elemSubmit = new Form_Element_Submit('chenge');
         $formChangeFileLock->addElement($elemSubmit);
      
         if( $fileRec->{ShareDocs_Model_Files::COLUMN_LOCKED} == true ){
            $elemSubmit->setLabel($this->tr('Odemknout'));
            $elemLock->setValues(false);
         } else {
            $elemSubmit->setLabel($this->tr('Zamknout'));
            $elemLock->setValues(true);
         }
      
         if($formChangeFileLock->isValid()){
            // TODO doplnit změnu
            $this->lockFile($fileRec, $formChangeFileLock->lock->getValues(), Auth::getUserId());
         
            if($formChangeFileLock->lock->getValues() == true){
               $this->infoMsg()->addMessage($this->tr('Soubor byl zamknut'));
            } else {
               $this->infoMsg()->addMessage($this->tr('Soubor byl odemknut'));
            }
            $this->link()->reload();
         }
         
         $formDelete = new Form("file_delete_");
         
         $eId = new Form_Element_Hidden('id');
         $eId->setValues($fileRec->{ShareDocs_Model_Files::COLUMN_ID});
         $formDelete->addElement($eId);
         
         $eDel = new Form_Element_Submit('delete', $this->tr('Smazat'));
         $formDelete->addElement($eDel);
         
         if($formDelete->isValid() ){
            $this->deleteFile($formDelete->id->getValues() );
            $this->infoMsg()->addMessage($this->tr('Soubor byl smazán') );
            $this->link()->route("dirList")->reload();
         }
         $this->view()->formDelete = $formDelete;
         
         $this->view()->formChangeLock = $formChangeFileLock;
      }
      
      /*
       * Nahrání nové revize dokumentu 
       */
      if($right == self::RIGHT_WRITE){
         $formUploadNewRevision = new Form('file_upload_');
         
         $elemFile = new Form_Element_File('file', $this->tr('Soubor'));
         $elemFile->addValidation(new Form_Validator_NotEmpty());
         $formUploadNewRevision->addElement($elemFile);
      
         $elemNote = new Form_Element_TextArea('note', $this->tr('Popis k verzi'));
         $elemNote->addFilter(new Form_Filter_StripTags());
         $formUploadNewRevision->addElement($elemNote);
      
         $elemUpload = new Form_Element_Submit('upload', $this->tr('Nahrát'));
         $formUploadNewRevision->addElement($elemUpload);
      
         if($formUploadNewRevision->isValid()){
            $file = new File($formUploadNewRevision->file);
            $fileName = $this->createUniqueFileName($file);
            $file->copy($this->getModule()->getDataDir(), false, $fileName);
         
            $modelRev = new ShareDocs_Model_Revs();
         
            $revRecord = $modelRev->newRecord();
            $revRecord->{ShareDocs_Model_Revs::COLUMN_ID_FILE} = $fileRec->getPK();
            $revRecord->{ShareDocs_Model_Revs::COLUMN_FILENAME} = $fileName;
            $revRecord->{ShareDocs_Model_Revs::COLUMN_NOTE} = $formUploadNewRevision->note->getValues();
            $revRecord->{ShareDocs_Model_Revs::COLUMN_ORIG_FILENAME} = $file->getName();
            $revRecord->{ShareDocs_Model_Revs::COLUMN_ID_USER} = Auth::getUserId();
            
            if(!$fileRec->isNew()){
               $lastRev = $modelRev
                  ->where(ShareDocs_Model_Revs::COLUMN_ID_FILE." = :idf", array('idf' => $fileRec->getPK()))
                  ->order(array(ShareDocs_Model_Revs::COLUMN_NUMBER => Model_ORM::ORDER_DESC))
                  ->record();
               $revRecord->{ShareDocs_Model_Revs::COLUMN_NUMBER} = $lastRev->{ShareDocs_Model_Revs::COLUMN_NUMBER}+1;
            }
            $modelRev->save($revRecord);
         
            $this->infoMsg()->addMessage($this->tr('Revize byla uložena.'));
            if($fileRec->{ShareDocs_Model_Files::COLUMN_LOCKED} == true){
               $this->infoMsg()->addMessage($this->tr('Soubor byl odemknut'));
               $fileRec->{ShareDocs_Model_Files::COLUMN_LOCKED} = false;
            }
         
            // odemknutí souboru
            $modelFile->save($fileRec);
         
            // mazání strých revizí
            $this->cleanOldRevisions($fileRec);
            $this->log( sprintf('Nahrána revize souboru id: %s', $fileRec->getPK() ) );
            $this->link()->reload();
         }
         $this->view()->formUpload = $formUploadNewRevision;
      }
      /*
       * Informace o souboru
       */
      $this->view()->file = $fileRec;
      
      $modelUser = new Model_Users();
      // pokud je soubor zamknut
      $this->view()->fileLock = null;
      if($fileRec->{ShareDocs_Model_Files::COLUMN_LOCKED} == true){
         $userLockRec = $modelUser->record($fileRec->{ShareDocs_Model_Files::COLUMN_LOCKED_ID_USER});
         if($userLockRec != false && !$userLockRec->isNew()){
            $this->view()->fileLock = $userLockRec;
         }
      }
      $this->view()->fileSize = filesize($this->module()->getDataDir().$fileRec->{ShareDocs_Model_Revs::COLUMN_FILENAME});
      
      /*
       * načtení revizí
       */
      $modelRevisions = new ShareDocs_Model_Revs();
      // revize dokumentu
      $revisions = $modelRevisions
         ->where(ShareDocs_Model_Revs::COLUMN_ID_FILE.' = :idf', array('idf' => $idFile))
         ->joinFK(ShareDocs_Model_Revs::COLUMN_ID_USER)
         ->order(array(ShareDocs_Model_Revs::COLUMN_NUMBER => Model_ORM::ORDER_DESC))
//         ->limit(0, self::OLD_REVISIONS+1)
         ->records();
      
      $this->view()->revisions = $revisions;
      $this->view()->revisionsCount = count($revisions);
      
      $this->view()->right = $right;
   }
   
   // **** OBSLUHA ADRESÁŘŮ **** //
   
   public function editDirAccessController()
   {
      $this->checkControllRights();
      
      $iddir =  $this->getRequest('iddir', 0);
      
      $modelGroups = new Model_Groups();
      $groups = $modelGroups
//         ->where(Model_Groups::COLUMN_IS_ADMIN.' = 0', array())
         ->records();
      $tmpArr = array();
      foreach ($groups as $grp) {
         $tmpArr[$grp->{Model_Groups::COLUMN_ID}] = $grp->{Model_Groups::COLUMN_NAME};
      }
      $this->view()->groups = $tmpArr;
      
      
      $modelUsers = new Model_Users();
      $users = $modelUsers
         ->order(array(Model_Users::COLUMN_USERNAME => Model_ORM::ORDER_ASC ))
         ->records();
      $tmpArr = array();
      foreach ($users as $user) {
         $tmpArr[$user->{Model_Users::COLUMN_ID}] = $user->{Model_Users::COLUMN_USERNAME}.' ('.$user->{Model_Users::COLUMN_NAME}.' '.$user->{Model_Users::COLUMN_SURNAME}.')';
      }
      $this->view()->users = $tmpArr;
      
      
      $this->view()->idDir = $iddir;
      
      $modelDirs = new ShareDocs_Model_Dirs();
      $directory = $modelDirs->record($iddir);
      $this->view()->directory = $directory;
      
      $formPublicAcc = new Form("dir_public_acc");
      
      $elemIsPublic = new Form_Element_Checkbox('is_public', 
         $this->tr("Složka je veřejná"));
      $elemIsPublic->setSubLabel($this->tr("Složka bude zobrazena všem uživatelům."));
      $elemIsPublic->setValues($directory->{ShareDocs_Model_Dirs::COLUMN_IS_PUBLIC});
      $formPublicAcc->addElement($elemIsPublic );
      
      $elemIsPublicW = new Form_Element_Checkbox('is_public_write', 
         $this->tr("Povolení zápisu"));
      $elemIsPublicW->setSubLabel($this->tr("Do složky mohou vkládat soubory všichni uživatelé."));
      $elemIsPublicW->setValues($directory->{ShareDocs_Model_Dirs::COLUMN_IS_PUBLIC_WRITE});
      $formPublicAcc->addElement($elemIsPublicW );
      
      $elemSubmit = new Form_Element_Submit('save', $this->tr('Uložit'));
      $formPublicAcc->addElement($elemSubmit );
      
      if($formPublicAcc->isValid() ){
         $directory->{ShareDocs_Model_Dirs::COLUMN_IS_PUBLIC} = $formPublicAcc->is_public->getValues();
         $directory->{ShareDocs_Model_Dirs::COLUMN_IS_PUBLIC_WRITE} = $formPublicAcc->is_public_write->getValues();
         
         $modelDirs->save($directory);
         $this->infoMsg()->addMessage($this->tr('Oprávnění bylo uloženo'));
         $this->link()->reload();
      }
      
      $this->view()->formPublicAcc = $formPublicAcc;
   }

   public function groupsListController()
   {
      $this->checkControllRights();
      
      $idDir = $this->getRequest('iddir', 0);
      
      // objekt komponenty JGrid
      $jqGrid = new Component_JqGrid();
      
      $model = new ShareDocs_Model_GroupsAcc();
      $model->joinFK(ShareDocs_Model_GroupsAcc::COLUMN_ID_GROUP);
      
      $model ->order(array(ShareDocs_Model_GroupsAcc::COLUMN_ID => Model_ORM::ORDER_ASC))
             ->where(ShareDocs_Model_GroupsAcc::COLUMN_ID_DIR, (int)$idDir);
      
      $jqGrid->respond()->setRecords($model->count());
      
      $accessList = $model->limit(($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage(), $jqGrid->request()->rows)->records();
      // out
      foreach ($accessList as $accItem) {
         array_push($jqGrid->respond()->rows, array(
            'id' => $accItem->{ShareDocs_Model_GroupsAcc::COLUMN_ID},
            // texty   
            'gid' => $accItem->{Model_Groups::COLUMN_ID},
            'group' => $accItem->{Model_Groups::COLUMN_NAME}.' ('.$accItem->{Model_Groups::COLUMN_LABEL}.')',
            ShareDocs_Model_GroupsAcc::COLUMN_READ_ONLY => $accItem->{ShareDocs_Model_GroupsAcc::COLUMN_READ_ONLY},
         ));
      }
      
      $this->view()->respond = $jqGrid->respond();
   }

   public function editGroupAccController()
   {
      $this->checkControllRights();
      $idDir = $this->getRequest('iddir', 0);
      
      $model = new ShareDocs_Model_GroupsAcc();
      
      $this->view()->allOk = false;
      // část komponenty jqgrid pro formy
      $jqGridReq = new Component_JqGrid_FormRequest();
      switch ($jqGridReq->getRequest()) {
         case Component_JqGrid_FormRequest::REQUEST_TYPE_ADD:
            $record = $model->newRecord();
            
            $record->{ShareDocs_Model_GroupsAcc::COLUMN_ID_DIR} = $idDir;
            $record->{ShareDocs_Model_GroupsAcc::COLUMN_ID_GROUP} = $jqGridReq->group;
            $record->{ShareDocs_Model_GroupsAcc::COLUMN_READ_ONLY} = $jqGridReq->{ShareDocs_Model_GroupsAcc::COLUMN_READ_ONLY};
            $model->save($record);
            
            $this->infoMsg()->addMessage($this->tr('Skpina byla uložena'));
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_DELETE:
            foreach ($jqGridReq->getIds() as $id) {
               $model->delete($id);
            }
            $this->infoMsg()->addMessage($this->tr('Vybrané skupiny byly smazány'));
            break;
         default:
            $this->errMsg()->addMessage($this->tr('Nepodporovaný typ operace'));
            break;
      }
      $this->log( sprintf('Úprava oprávnění složky %s', $idDir ) );
      if ($this->errMsg()->isEmpty()) {
         $this->view()->allOk = true;
      }
   }


   //**** USERS ACC *****/
   
   public function usersListController()
   {
      $this->checkControllRights();
      
      $idDir = $this->getRequest('iddir', 0);
      
      // objekt komponenty JGrid
      $jqGrid = new Component_JqGrid();
      
      $model = new ShareDocs_Model_UsersAcc();
      $model->joinFK(ShareDocs_Model_UsersAcc::COLUMN_ID_USER);
      
      $model ->order(array(ShareDocs_Model_UsersAcc::COLUMN_ID_USER => Model_ORM::ORDER_ASC))
             ->where(ShareDocs_Model_UsersAcc::COLUMN_ID_DIR, (int)$idDir);
      
      $jqGrid->respond()->setRecords($model->count());
      
      $accessList = $model->limit(($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage(), $jqGrid->request()->rows)->records();
      // out
      foreach ($accessList as $accItem) {
         array_push($jqGrid->respond()->rows, array(
            'id' => $accItem->{ShareDocs_Model_UsersAcc::COLUMN_ID},
            // texty   
            'uid' => $accItem->{Model_Users::COLUMN_ID},
            'user' => $accItem->{Model_Users::COLUMN_USERNAME}.' ('.$accItem->{Model_Users::COLUMN_NAME}.' '.$accItem->{Model_Users::COLUMN_SURNAME}.')',
            ShareDocs_Model_UsersAcc::COLUMN_READ_ONLY => $accItem->{ShareDocs_Model_UsersAcc::COLUMN_READ_ONLY},
         ));
      }
      
      $this->view()->respond = $jqGrid->respond();
   }

   public function editUserAccController()
   {
      $this->checkControllRights();
      $idDir = $this->getRequest('iddir', 0);
      
      $model = new ShareDocs_Model_UsersAcc();
      
      $this->view()->allOk = false;
      // část komponenty jqgrid pro formy
      $jqGridReq = new Component_JqGrid_FormRequest();
      switch ($jqGridReq->getRequest()) {
         case Component_JqGrid_FormRequest::REQUEST_TYPE_ADD:
            $record = $model->newRecord();
            
            $record->{ShareDocs_Model_UsersAcc::COLUMN_ID_DIR} = $idDir;
            $record->{ShareDocs_Model_UsersAcc::COLUMN_ID_USER} = $jqGridReq->user;
            $record->{ShareDocs_Model_UsersAcc::COLUMN_READ_ONLY} = $jqGridReq->{ShareDocs_Model_UsersAcc::COLUMN_READ_ONLY};
            $model->save($record);
            
            $this->infoMsg()->addMessage($this->tr('Uživatel byl uložen'));
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_DELETE:
            foreach ($jqGridReq->getIds() as $id) {
               $model->delete($id);
            }
            $this->infoMsg()->addMessage($this->tr('Vybraní uživatelé byli smazáni'));
            break;
         default:
            $this->errMsg()->addMessage($this->tr('Nepodporovaný typ operace'));
            break;
      }
      $this->log( sprintf('Úprava oprávnění složky %s', $idDir ) );
      if ($this->errMsg()->isEmpty()) {
         $this->view()->allOk = true;
      }
   }

   public function setReadOnlyController()
   {
      $type = $this->getRequestParam('type', 'user');
      $id = $this->getRequestParam('id', 0);
      $status = $this->getRequestParam('status', 'true') == "false" ? false : true;
      
      $this->view()->result = false;
      
      if($id == 0){
         return;
      }
      
      if( $type == "user") {
         $model = new ShareDocs_Model_UsersAcc();
         $usr = $model->record($id);
         $usr->{ShareDocs_Model_UsersAcc::COLUMN_READ_ONLY} = $status;
         $model->save($usr);
         $this->view()->result = true;
      } else if( $type == "group") {
         $model = new ShareDocs_Model_GroupsAcc();
         $grp = $model->record($id);
         $grp->{ShareDocs_Model_GroupsAcc::COLUMN_READ_ONLY} = $status;
         $model->save($grp);
         $this->view()->result = true;
      }
   }

      //**** ODKAZY *****/  
   
   public function generatePublicLinkController()
   {
      $this->checkReadableRights();
      
      // kontrola práv k adresáři
      
      // generování tokenu
      $idFile = $this->getRequest('idfile',0);
      
      $modelFiles = new ShareDocs_Model_Files();
      $fileRec = $modelFiles->record($idFile);
      
      if($this->getDirectoryRights($fileRec->{ShareDocs_Model_Files::COLUMN_ID_DIRECTORY}) == self::RIGHT_NONE || 
         $fileRec == false || $fileRec->isNew()){
         return false;
      }
      
      $modelToken = new ShareDocs_Model_Tokens();
      
      $token = vve_generate_token(32, true);
      
      
      $rec = $modelToken->newRecord();
      $rec->{ShareDocs_Model_Tokens::COLUMN_ID_FILE} = $fileRec->getPK();
      $rec->{ShareDocs_Model_Tokens::COLUMN_TOKEN} = $token;
      
      $modelToken->save($rec);
      $this->log( sprintf('Generován veřejný odkaz na soubor id: %s', $idFile ) );
      
      $linkStatic = new Url_Link_ModuleStatic();
      $this->view()->link = (string)$linkStatic->module($this->module()->getName())->action('download', 'php')->param('token', $token);
      
   }

   /**
    * Kontroler pro odeslání souboru pomocí tokenu
    */
   public static function downloadController()
   {
      self::runTokenClearer();
      
      $link = new Url_Link(true);
      echo "download";
      if(!isset ($_GET['token'])){
         $link->setFile('error.html')->reload();
      }
      $token = $_GET['token'];
      
      $modelTokens = new ShareDocs_Model_Tokens();
      
      $tokenRec = $modelTokens->where(ShareDocs_Model_Tokens::COLUMN_TOKEN.' = :token', array('token' => $token))->record();
      
      if($tokenRec == false || $tokenRec->isNew()){
         $link->setFile('error.html')->reload();
      }
      
      self::sendFileToClient($tokenRec->{ShareDocs_Model_Tokens::COLUMN_ID_FILE}, false);
   }

   /**
    * Metoda pro výmaz neplatných tokenů
    */
   protected static function runTokenClearer()
   {
      $model = new ShareDocs_Model_Tokens();
      $model->where('TIME_TO_SEC( TIMEDIFF(NOW(), '.ShareDocs_Model_Tokens::COLUMN_DATE_ADD.') ) > :totime', 
         array("totime" => self::TOKEN_MAX_HOURS*3600))
         ->delete();
   }


   // **** SUPPORT METHODS ***** /

   private function createUniqueFileName(File $file)
   {
      // explode the IP of the remote client into four parts
      $arrIp = explode('.', $_SERVER['REMOTE_ADDR']);
      // get both seconds and microseconds parts of the time
      list($usec, $sec) = explode(' ', microtime());
      // fudge the time we just got to create two 16 bit words
      $usec = (integer) ($usec * 65536);
      $sec = ((integer) $sec) & 0xFFFF;
      // fun bit--convert the remote client's IP into a 32 bit
      // hex number then tag on the time.
      // Result of this operation looks like this xxxxxxxx-xxxx-xxxx
      $strUid = sprintf("%.10s-%08x-%04x-%04x.dat", md5(Auth::getUserName()), ($arrIp[0] << 24) | ($arrIp[1] << 16) | ($arrIp[2] << 8) | $arrIp[3], $sec, $usec);
      // tack on the extension and return the filename
      return $strUid;
   }

   /**
    * Statiscká metoda odešle soubor ke klientovi
    * @param Model_ORM_Record $file -- objekt souboru
    * @param bool $lock -- jeslit se má daný soubor zamknout
    */
   private static function sendFileToClient($fileObj, $rev = null)
   {
      if($fileObj instanceof Model_ORM_Record){
         
      } else {
         $model = new ShareDocs_Model_Files();
         $fileObj = $model->record($fileObj);
         if($fileObj == false){
            return false;
         }
      }
      
      $modelRevs = new ShareDocs_Model_Revs();
      
      if($rev == null){
         $modelRevs->where(ShareDocs_Model_Revs::COLUMN_ID_FILE.' = :idf', array('idf' => $fileObj->getPK()));
      } else {
         $modelRevs->where(ShareDocs_Model_Revs::COLUMN_ID_FILE.' = :idf AND '.ShareDocs_Model_Revs::COLUMN_ID.' = :idrev', 
               array('idf' => $fileObj->getPK(), 'idrev' => $rev) );
      }
         
      $fileRevision = $modelRevs->order(array(ShareDocs_Model_Revs::COLUMN_DATE_ADD => Model_ORM::ORDER_DESC))->record();
      
      $file = AppCore::getAppDataDir().self::DATA_DIR.DIRECTORY_SEPARATOR.$fileRevision->{ShareDocs_Model_Revs::COLUMN_FILENAME};
      
      if (file_exists($file)) {
         header('Content-Description: File Transfer');
         header('Content-Type: application/octet-stream');
         header('Content-Disposition: attachment; filename='.basename( vve_cr_safe_file_name( $fileRevision->{ShareDocs_Model_Revs::COLUMN_ORIG_FILENAME}) ) );
         header('Content-Transfer-Encoding: binary');
         header('Expires: 0');
         header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
         header('Pragma: public');
         header('Content-Length: ' . filesize($file));
         ob_clean();
         flush();
         readfile($file);
         exit;
      }
   }

   /**
    * Metoda vrací práva k danému adresář
    * @param int/Model_ORM_Record -- objekt adresáře nebo id
    * @return int -- Class Const RIGHT_XXX
    */
   private function getDirectoryRights($dir) 
   {
      if($this->getRights()->isControll()){
         return self::RIGHT_WRITE;
      }
      
      if(!($dir instanceof Model_ORM_Record)){
         $model = new ShareDocs_Model_Dirs();
         $dir = $model->record((int)$dir);
      }
      
      if($dir->{ShareDocs_Model_Dirs::COLUMN_IS_PUBLIC}){
         if($dir->{ShareDocs_Model_Dirs::COLUMN_IS_PUBLIC_WRITE}){
            return self::RIGHT_WRITE;
         }
         return self::RIGHT_READ_ONLY;
      }
      
      if($dir == false || $dir->isNew()){
         return self::RIGHT_NONE;
      }
      
      // kontrola uživatele
      $modelUserAcc = new ShareDocs_Model_UsersAcc();
      
      $acc = $modelUserAcc->where(
            ShareDocs_Model_UsersAcc::COLUMN_ID_DIR.' = :idd AND '.ShareDocs_Model_UsersAcc::COLUMN_ID_USER.' = :idu',
            array('idd' => $dir->getPK(), 'idu' => Auth::getUserId() )
         )->record();
      
      if($acc != false){
         return $acc->{ShareDocs_Model_UsersAcc::COLUMN_READ_ONLY} == true ? self::RIGHT_READ_ONLY : self::RIGHT_WRITE;
      }
       
      // kontrola skupiny
      $modelGrpAcc = new ShareDocs_Model_GroupsAcc();
      
      $acc = $modelGrpAcc->where(
            ShareDocs_Model_GroupsAcc::COLUMN_ID_DIR.' = :idd AND '.  ShareDocs_Model_GroupsAcc::COLUMN_ID_GROUP.' = :idg',
            array('idd' => $dir->getPK(), 'idg' => Auth::getGroupId() )
         )->record();
      
      if($acc != false){
         return $acc->{ShareDocs_Model_GroupsAcc::COLUMN_READ_ONLY} == true ? self::RIGHT_READ_ONLY : self::RIGHT_WRITE;
      }
      
      return self::RIGHT_NONE;
	}

   private function cleanOldRevisions($file) {
      if($this->category()->getParam(self::PARAM_NUMBER_STORED_REVS) == 0){
         return;
      }
      // get all revisions
      $modelRevs = new ShareDocs_Model_Revs();
      $revs = $modelRevs
         ->where(ShareDocs_Model_Revs::COLUMN_ID_FILE.' = :idf', array('idf' => $file->{ShareDocs_Model_Files::COLUMN_ID}))
         ->order(array(ShareDocs_Model_Revs::COLUMN_DATE_ADD => Model_ORM::ORDER_DESC))
         ->records();
      
      $numRevs = count($revs);
      if( $revs == false || $numRevs <= ($this->category()->getParam(self::PARAM_NUMBER_STORED_REVS, 10)+1) ){
         return;
      }   
      
      foreach ($revs as $key => $rev) {
         if($key != $numRevs-1
            && $key > $this->category()->getParam(self::PARAM_NUMBER_STORED_REVS, 10)-1){
            // výmaz staré revize
            if(is_file($this->module()->getDataDir().$rev->{ShareDocs_Model_Revs::COLUMN_FILENAME})){
               @unlink($this->module()->getDataDir().$rev->{ShareDocs_Model_Revs::COLUMN_FILENAME});
               $modelRevs->delete($rev);
            }
         }
      }
      
   }

   /**
    * metoda pro zamykání a odemykání souboru
    * @param Model_ORM_Record $fileRec
    * @param bool $lock
    * @param int $idUser 
    */
   private function lockFile(Model_ORM_Record $fileRec, $lock, $idUser = 0)
   {
      $modelFile = new ShareDocs_Model_Files();
      $fileRec->{ShareDocs_Model_Files::COLUMN_LOCKED} = $lock;
      $fileRec->{ShareDocs_Model_Files::COLUMN_LOCKED_ID_USER} = $idUser;
      $modelFile->save($fileRec);
   }


   // **** SYSTEM METHODS **** /

   public function autorun()
   {
      self::runTokenClearer();
   }

   public function settings(&$settings, Form &$form) {
      $fGrpRevSettings = $form->addGroup('revSettings', $this->tr('Nastavení revizí'));

      $elemNumRevs = new Form_Element_Select('revs_number', $this->tr('Počet uchovávaných revizí'));
      $elemNumRevs->setSubLabel($this->tr('Omezí uchovávání revizí a šetří místo. Výchozí: 10. Zadejte 0 pro vypnutí omezení.'));
      $elemNumRevs->setValues(10);
      if(isset($settings[self::PARAM_NUMBER_STORED_REVS])) {
         $elemNumRevs->setValues($settings[self::PARAM_NUMBER_STORED_REVS]);
      }
      $form->addElement($elemNumRevs, $fGrpRevSettings);


      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings[self::PARAM_NUMBER_STORED_REVS] = $form->revs_number->getValues();
      }
   }
   
}

?>
