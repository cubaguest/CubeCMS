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
   public function mainController()
   {
      //		Kontrola práv
      $this->checkReadableRights();
      // pokud nebyl datový adresář vytvořen, vytvoří se
      if (!is_dir($this->module()->getDataDir())) {
         mkdir($this->module()->getDataDir(), 0777, true);
      }

      // mazání položky/položek
      $this->checkDeleteItem();

      $pass = $this->category()->getParam(self::PARAM_PASS);
      $session = new Session('dwfiles-' . $this->category()->getId());

      $loadFiles = false;
      if ($pass == null || $this->category()->getRights()->isWritable() || $session->get()) {
         $loadFiles = true;
      } else {

         $fLogin = new Form('login_');
         $grp = $fLogin->addGroup('pass', $this->tr('Pro přístup zadejte heslo'));
         $ePass = new Form_Element_Password('pass', $this->tr('Heslo'));
         $ePass->addValidation(new Form_Validator_NotEmpty());
         $fLogin->addElement($ePass, $grp);

         $send = new Form_Element_Submit('login', $this->tr('Přihlásit'));
         $fLogin->addElement($send, $grp);

         if ($fLogin->isSend() && $ePass->isValid() && $fLogin->pass->getValues() != $pass) {
            $ePass->setError($this->tr('Bylo zadáno nesprávné heslo pro přístup'));
         }

         if ($fLogin->isValid()) {
            // set session
            $session->set(true);
            $this->link()->redirect();
         }
         $this->view()->formLogin = $fLogin;
      }

      if ($loadFiles) {
         // load items
         $model = new DownloadFiles_Model();

         if ($this->rights()->isWritable()) {
            if ($this->getRequestParam('activate', false)) {
               $model
                  ->where(DownloadFiles_Model::COLUMN_ID . " = :id", array('id' => $this->getRequestParam('activate')))
                  ->update(array(DownloadFiles_Model::COLUMN_ACTIVE => true));
               $this->link()->rmParam('activate')->reload();
            }
         }

         $files = $model
            ->joinFK(DownloadFiles_Model::COLUMN_ID_SECTION)
            ->where('DownloadFiles_Model_Sections_1.' . DownloadFiles_Model_Sections::COLUMN_ID_CATEGORY . ' = :idc '
               . ($this->rights()->isWritable() == false ? ' AND ' . DownloadFiles_Model::COLUMN_ACTIVE . " = 1" : null), array('idc' => $this->category()->getId()))
            ->order(array(
               DownloadFiles_Model_Sections::COLUMN_ORDER => Model_ORM::ORDER_ASC,
               DownloadFiles_Model::COLUMN_COLUMN => Model_ORM::ORDER_ASC,
               DownloadFiles_Model::COLUMN_TIME_ADD => Model_ORM::ORDER_DESC,
            ))
            ->records();

         $modelSec = new DownloadFiles_Model_Sections();
         $this->view()->sections = $modelSec->where(DownloadFiles_Model_Sections::COLUMN_ID_CATEGORY.' = :idc', array('idc' => $this->category()->getId() ))
                 ->records();
         
         $this->view()->files = $files;
         $this->view()->dataDir = $this->module()->getDataDir(true);
      }
   }

   public function addController()
   {
      $this->checkWritebleRights();

      $form = $this->createForm();

      if ($form->isValid()) {
         $model = new DownloadFiles_Model();
         $fileRec = $model->newRecord();

         $file = $form->file->getValues();

         $fileRec->{DownloadFiles_Model::COLUMN_ID_USER} = Auth::getUserId();
         $fileRec->{DownloadFiles_Model::COLUMN_FILE} = $file['name'];
         $fileRec->{DownloadFiles_Model::COLUMN_NAME} = $form->name->getValues();
         $fileRec->{DownloadFiles_Model::COLUMN_TEXT} = $form->text->getValues();
         $fileRec->{DownloadFiles_Model::COLUMN_ACTIVE} = $form->active->getValues();
         if (isset($form->column)) {
            $fileRec->{DownloadFiles_Model::COLUMN_COLUMN} = $form->column->getValues();
         }

         $grp = $form->groupNewName->getValues();
         if ($grp[Locales::getDefaultLang()] != null) {
            $sec = DownloadFiles_Model_Sections::getNewRecord();
            $sec->{DownloadFiles_Model_Sections::COLUMN_NAME} = $form->groupNewName->getValues();
            $sec->{DownloadFiles_Model_Sections::COLUMN_ID_CATEGORY} = $this->category()->getId();
            $sec->save();
            $fileRec->{DownloadFiles_Model::COLUMN_ID_SECTION} = $sec->getPK();
         } else if(isset ($form->groupId)) {
            $fileRec->{DownloadFiles_Model::COLUMN_ID_SECTION} = $form->groupId->getValues();
         } else {
            $sec = DownloadFiles_Model_Sections::getNewRecord();
            $sec->{DownloadFiles_Model_Sections::COLUMN_NAME} = array(Locales::getDefaultLang() => 'Výchozí');
            $sec->{DownloadFiles_Model_Sections::COLUMN_ID_CATEGORY} = $this->category()->getId();
            $sec->save();
            $fileRec->{DownloadFiles_Model::COLUMN_ID_SECTION} = $sec->getPK();
         }

         $model->save($fileRec);
         $this->infoMsg()->addMessage($this->tr('Soubor byl uložen'));
         $this->log('nahran soubor ke stazeni: ' . $file['name']);
         $this->link()->route()->reload();
      }

      $this->view()->form = $form;
   }

   public function editController()
   {
      $this->checkWritebleRights();

      $model = new DownloadFiles_Model();

      $fileRec = $model
         ->joinFK(DownloadFiles_Model::COLUMN_ID_SECTION)
         ->record($this->getRequest('id'));

      if (!$this->checkValidEditFileRecord($fileRec)) {
         return false;
      }

      $form = $this->createForm($fileRec);

      if ($form->isValid()) {
         $file = $form->file->getValues();

         if ($file != null) {
            if ($fileRec->{DownloadFiles_Model::COLUMN_FILE} != $file['name'] && is_file($this->module()->getDataDir() . $fileRec->{DownloadFiles_Model::COLUMN_FILE})) {
               @unlink($this->module()->getDataDir() . $fileRec->{DownloadFiles_Model::COLUMN_FILE});
            }
            $fileRec->{DownloadFiles_Model::COLUMN_FILE} = $file['name'];
            $this->log('nahran soubor ke stazeni: ' . $file['name']);
         }

         $fileRec->{DownloadFiles_Model::COLUMN_NAME} = $form->name->getValues();
         $fileRec->{DownloadFiles_Model::COLUMN_TEXT} = $form->text->getValues();
         $fileRec->{DownloadFiles_Model::COLUMN_ACTIVE} = $form->active->getValues();
         if (isset($form->column)) {
            $fileRec->{DownloadFiles_Model::COLUMN_COLUMN} = $form->column->getValues();
         }

         $grp = $form->groupNewName->getValues();
         if ($grp[Locales::getDefaultLang()] != null) {
            $sec = DownloadFiles_Model_Sections::getNewRecord();
            $sec->{DownloadFiles_Model_Sections::COLUMN_NAME} = $form->groupNewName->getValues();
            $sec->{DownloadFiles_Model_Sections::COLUMN_ID_CATEGORY} = $this->category()->getId();
            $sec->save();
            $fileRec->{DownloadFiles_Model::COLUMN_ID_SECTION} = $sec->getPK();
         } else {
            $fileRec->{DownloadFiles_Model::COLUMN_ID_SECTION} = $form->groupId->getValues();
         }

         $model->save($fileRec);
         $this->infoMsg()->addMessage($this->tr('Soubor byl uložen'));
         $this->link()->route()->reload();
      }

      $this->view()->form = $form;
      $this->view()->file = $fileRec;
   }

   public function editFilesGroupsController()
   {
      $this->checkControllRights();

      $form = new Form('allAsign');

      $iGroup = new Form_Element_Select('groupId', $this->tr('Sekce'));
      $iGroup->setSubLabel($this->tr('Zařazení do sekce umožňuje soubory seskupovat podle typu.'));
//      $iGroup->setDimensional();
      // již uložené skupiny
      $model = new DownloadFiles_Model_Sections();
      $groups = $model
         ->where(DownloadFiles_Model_Sections::COLUMN_ID_CATEGORY . " = :idc", array('idc' => $this->category()->getId()))
         ->records();

      if ($groups != false) {
         foreach ($groups as $grp) {
            if ((string) $grp->{DownloadFiles_Model_Sections::COLUMN_NAME} != null) {
               $iGroup->addOption((string) Utils_String::getLangString($grp->{DownloadFiles_Model_Sections::COLUMN_NAME}), (int) $grp->getPK());
            }
         }
         $form->addElement($iGroup);
      }

      $iNewGroup = new Form_Element_Text('groupNewName', $this->tr('Nový název sekce'));
      $iNewGroup->setMultiple();
      $iNewGroup->setLangs();
      if ($groups != false) {
         $iNewGroup->setSubLabel($this->tr('Pokud není zadán, použije se sekce z předchozího výběru.'));
      } else {
         $iNewGroup->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang()));
      }

      $form->addElement($iNewGroup);

      $elemSave = new Form_Element_SaveCancel('save', array($this->tr('Uložit'), $this->tr('Zavřít')));
      $form->addElement($elemSave);

      if ($form->isSend() && $form->save->getvalues() == false) {
         $this->link()->route()->redirect();
      }

      if ($form->isValid()) {
         $groupsIds = $form->groupId->getValues();
         $newGroups = $form->groupNewName->getValues();
         $m = new DownloadFiles_Model();
         foreach ($groupsIds as $key => $idgrp) {
            $idSec = $idgrp;
            if ($newGroups[$key][Locales::getDefaultLang()] != null) {
               $sec = DownloadFiles_Model_Sections::getNewRecord();
               $sec->{DownloadFiles_Model_Sections::COLUMN_ID_CATEGORY} = $this->category()->getId();
               $sec->{DownloadFiles_Model_Sections::COLUMN_NAME} = $newGroups[$key];
               $sec->save();
               $idSec = $sec->getPK();
            }
            $m->where(DownloadFiles_Model::COLUMN_ID . ' = :idf', array('idf' => (int) $key))
               ->update(array(DownloadFiles_Model::COLUMN_ID_SECTION => $idSec));
         }

         $this->infoMsg()->addMessage($this->tr('Rozdělení bylo uloženo'));
         $this->link()->redirect();
      }


      $this->view()->form = $form;

      $modelF = new DownloadFiles_Model();
      $files = $modelF
         ->joinFK(DownloadFiles_Model::COLUMN_ID_SECTION)
         ->where('DownloadFiles_Model_Sections_1.' . DownloadFiles_Model_Sections::COLUMN_ID_CATEGORY . ' = :idc '
            . ($this->rights()->isWritable() == false ? ' AND ' . DownloadFiles_Model::COLUMN_ACTIVE . " = 1" : null), array('idc' => $this->category()->getId()))
         ->order(array(
            DownloadFiles_Model_Sections::COLUMN_ORDER => Model_ORM::ORDER_ASC,
            DownloadFiles_Model::COLUMN_COLUMN => Model_ORM::ORDER_ASC,
            DownloadFiles_Model::COLUMN_TIME_ADD => Model_ORM::ORDER_DESC,
         ))
         ->records();

      $this->view()->files = $files;
      $this->view()->dataDir = $this->module()->getDataDir(true);
   }

   public function editGroupsController()
   {
      $this->checkControllRights();

      $form = new Form('groupsNames');

      $iGroupName = new Form_Element_Text('groupName', $this->tr('Název sekce'));
      $iGroupName->setMultiple();
      $iGroupName->setLangs();
      $iGroupName->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang()));

      $form->addElement($iGroupName);

//      $iOrder = new Form_Element_Hidden('order');
//      $iOrder->setMultiple();
//      $form->addElement($iOrder);

      $elemSave = new Form_Element_SaveCancel('save', array($this->tr('Uložit'), $this->tr('Zavřít')));
      $form->addElement($elemSave);

      if ($form->isSend() && $form->save->getvalues() == false) {
         $this->link()->route()->redirect();
      }

      if ($form->isValid()) {
         $grps = $form->groupName->getValues();
         $i = 1;
         foreach ($grps as $id => $grp) {
            $rec = DownloadFiles_Model_Sections::getRecord($id);
            $rec->{DownloadFiles_Model_Sections::COLUMN_ORDER} = $i;
            $rec->{DownloadFiles_Model_Sections::COLUMN_NAME} = $grp;
            $rec->save();
            $i++;
         }

         $this->infoMsg()->addMessage($this->tr('Změny byly uloženy'));
         $this->link()->redirect();
      }


      $this->view()->form = $form;

      $modelF = new DownloadFiles_Model_Sections();
      $sections = $modelF
         ->columns(array('*', 'files' => 'COUNT(' . DownloadFiles_Model::COLUMN_ID . ')'))
         ->join(DownloadFiles_Model_Sections::COLUMN_ID, 'DownloadFiles_Model', DownloadFiles_Model::COLUMN_ID_SECTION)
         ->order(array(
            DownloadFiles_Model_Sections::COLUMN_ORDER => Model_ORM::ORDER_ASC,
         ))
         ->groupBy(DownloadFiles_Model::COLUMN_ID_SECTION)
         ->records();

      $this->view()->sections = $sections;
   }

   public function moveCatController($id)
   {
      $this->checkWritebleRights();

      $file = DownloadFiles_Model::getRecord($id);

      if (!$file) {
         throw new UnexpectedPageException();
      }

      $form = new Form('action_move_');
      $elemId = new Form_Element_Hidden('id');
      $elemId->setValues($file->{DownloadFiles_Model::COLUMN_ID});
      $form->addElement($elemId);

//      $cats = Model_Category::getCategoryListByModule(array('actions', 'actionswgal'));
      $eNewCat = new Form_Element_Select('idcat', $this->tr('Nová kateogrie'));
//      $struct = Category_Structure::getStructure();
      $cOpts = array();
      $secs = DownloadFiles_Model_Sections::getSectionsWithCats();

      foreach ($secs as $sec) {
         if ($sec->{Model_Category::COLUMN_CAT_ID} == $this->category()->getId()) {
            continue;
         }
         $catname = (string) $sec->{Model_Category::COLUMN_NAME} . ' (ID: ' . $sec->{Model_Category::COLUMN_CAT_ID} . ')';
         if (!isset($cOpts[$catname])) {
            $cOpts[$catname] = array();
         }
         $cOpts[$catname][(string) $sec->{DownloadFiles_Model_Sections::COLUMN_NAME}] = $sec->getPK();
      }

      $cats = Model_Category::getCategoryListByModule($this->module()->getName());

      foreach ($cats as $cat) {
         if ($cat->{Model_Category::COLUMN_CAT_ID} == $this->category()->getId()) {
            continue;
         }
         $catname = (string) $cat->{Model_Category::COLUMN_NAME} . ' (ID: ' . $cat->{Model_Category::COLUMN_CAT_ID} . ')';
         if (!isset($cOpts[$catname])) {
            $cOpts[$catname][$this->tr('Výchozí')] = 'base_' . $cat->getPK();
         }
      }

      $cats = Model_Category::getCategoryListByModule('smaseminars');
      foreach ($cats as $cat) {
         if ($cat->{Model_Category::COLUMN_CAT_ID} == $this->category()->getId()) {
            continue;
         }
         $catname = (string) $cat->{Model_Category::COLUMN_NAME} . ' (ID: ' . $cat->{Model_Category::COLUMN_CAT_ID} . ')';
         if (!isset($cOpts[$catname])) {
            $cOpts[$catname][$this->tr('Ke stažení')] = 'smasem_' . $cat->getPK();
         }
      }

      $eNewCat->setOptions($cOpts);
      $form->addElement($eNewCat);

      $eRedirect = new Form_Element_Checkbox('redirect', $this->tr('Přejít do nové kategorie souboru'));
      $form->addElement($eRedirect);

      $eSave = new Form_Element_SaveCancel('save');
      $form->addElement($eSave);

      if ($form->isSend() && $form->save->getValues() == false) {
         $this->link()->route()->redirect();
      }

      if ($form->isValid()) {
         $idc = str_replace('base_', '', $form->idcat->getValues());
         if (strpos($form->idcat->getValues(), 'smasem_') !== false && class_exists('SMASeminars_Model_Files')) {
            // přesun do speciální kategorie - semináře
            $idc = str_replace('smasem_', '', $form->idcat->getValues());
            $newCat = Category_Structure::getStructure()->getCategory($idc)->getCatObj();
            // přesun
            $fileObj = new File($file->{DownloadFiles_Model::COLUMN_FILE}, $this->category()->getModule()->getDataDir());
            FS_Dir::checkStatic($newCat->getModule()->getDataDir());
            $fileObj->move($newCat->getModule()->getDataDir());

            $fileRec = SMASeminars_Model_Files::getNewRecord();
            $fileRec->{SMASeminars_Model_Files::COLUMN_FILE} = $fileObj->getName();
            $fileRec->{SMASeminars_Model_Files::COLUMN_ID_CATEGORY} = $newCat->getId();
            $fileRec->{SMASeminars_Model_Files::COLUMN_LABEL} = $fileObj->getName();
            $fileRec->save();
         } else {
            if (strpos($form->idcat->getValues(), 'base') !== false) {
               $newCat = Category_Structure::getStructure()->getCategory($idc)->getCatObj();
               // vytvoř sekci výchozí
               $newSec = DownloadFiles_Model_Sections::getNewRecord();
               $newSec->{DownloadFiles_Model_Sections::COLUMN_ID_CATEGORY} = $idc;
               $newSec->{DownloadFiles_Model_Sections::COLUMN_NAME} = 'Základní';
               $newSec->save();
               $file->{DownloadFiles_Model::COLUMN_ID_SECTION} = $newSec->getPK();
            } else {
               $sec = DownloadFiles_Model_Sections::getRecord($idc);
               $newCat = Category_Structure::getStructure()->getCategory($sec->{DownloadFiles_Model_Sections::COLUMN_ID_CATEGORY})->getCatObj();
               $file->{DownloadFiles_Model::COLUMN_ID_SECTION} = $form->idcat->getValues();
            }

            $fileObj = new File($file->{DownloadFiles_Model::COLUMN_FILE}, $this->category()->getModule()->getDataDir());
            if ($newCat->getModule()->getDataDir() != $this->category()->getModule()->getDataDir()) {
               FS_Dir::checkStatic($newCat->getModule()->getDataDir());
               $fileObj->move($newCat->getModule()->getDataDir());
            }

            $file->{DownloadFiles_Model::COLUMN_FILE} = $fileObj->getName(); // kvůli duplicitním souborům
            $file->save();
         }
         $this->infoMsg()->addMessage($this->tr('Soubor byl přesunut'));
         $this->log(sprintf('Přesun souboru %s id: %s mezi kategoriemi', $file->{DownloadFiles_Model::COLUMN_NAME}, $file->getPK()));
         if ($form->redirect->getValues()) {
            $this->link()->category($newCat->getUrlKey())->route()->redirect();
         } else {
            $this->link()->route()->redirect();
         }
      }
      $this->view()->form = $form;
      $this->view()->file = $file;
   }

   protected function getSimilarCats($struct, &$ret = array(), $level = 0)
   {
      foreach ($struct as $item) {
         /* @var $item Category_Structure */
         if (in_array($item->getCatObj()->getModule()->getName(), array('downloadfiles'))) {
            $ret[$item->getId()] = str_repeat('...', $level) . $item->getCatObj()->getName() . " (ID: " . $item->getId() . ')';
         }
         if (!$item->isEmpty()) {
            $this->getSimilarCats($item, $ret, $level + 1);
         }
      }
      return $ret;
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

      if ($this->category()->getParam(self::PARAM_COLS, 1) > 1) {
         $elemCol = new Form_Element_Select('column', $this->tr('Sloupec'));
         for ($col = 1; $col <= $this->category()->getParam(self::PARAM_COLS, 1); $col++) {
            $elemCol->setOptions(array($this->tr("Sloupec ") . $col => (string) $col), true);
         }
         $form->addElement($elemCol);
      }

      $iGroup = new Form_Element_Select('groupId', $this->tr('Sekce'));
      $iGroup->setSubLabel($this->tr('Zařazení do sekce umožňuje soubory seskupovat podle typu.'));

      // již uložené skupiny
      $model = new DownloadFiles_Model_Sections();
      $groups = $model
         ->where(DownloadFiles_Model_Sections::COLUMN_ID_CATEGORY . " = :idc", array('idc' => $this->category()->getId()))
         ->records();

      if ($groups != false) {
         foreach ($groups as $grp) {
            if ((string) $grp->{DownloadFiles_Model_Sections::COLUMN_NAME} != null) {
               $iGroup->addOption((string) Utils_String::getLangString($grp->{DownloadFiles_Model_Sections::COLUMN_NAME}), $grp->getPK());
            }
         }
         $form->addElement($iGroup);
      }

      $iNewGroup = new Form_Element_Text('groupNewName', $this->tr('Nový název sekce'));
      $iNewGroup->setLangs();
      if ($groups != false) {
         $iNewGroup->setSubLabel($this->tr('Pokud není zadán, použije se sekce z předchozího výběru.'));
      } else {
         $iNewGroup->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang()));
      }

      $form->addElement($iNewGroup);

      $elemActive = new Form_Element_Checkbox('active', $this->tr('Aktivní'));
      $elemActive->setValues(true);
      $form->addElement($elemActive);

      $elemSave = new Form_Element_SaveCancel('save');
      $form->addElement($elemSave);

      if ($fileObj != null) {
         $form->name->setValues($fileObj->{DownloadFiles_Model::COLUMN_NAME});
         $form->text->setValues($fileObj->{DownloadFiles_Model::COLUMN_TEXT});
         $form->active->setValues($fileObj->{DownloadFiles_Model::COLUMN_ACTIVE});
         $form->groupId->setValues($fileObj->{DownloadFiles_Model::COLUMN_ID_SECTION});
         $form->file->setSubLabel(sprintf($this->tr('Nahraný soubor: <strong>%s</strong> (%s). Pokud nahrajete nový, dojde k přepsání.'), $fileObj->{DownloadFiles_Model::COLUMN_FILE}, Utils_String::createSizeString(filesize($this->module()->getDataDir() . $fileObj->{DownloadFiles_Model::COLUMN_FILE}), true)
         ));
         $form->file->removeValidation('Form_Validator_NotEmpty');
         if (isset($form->column)) {
            $form->column->setValues($fileObj->{DownloadFiles_Model::COLUMN_COLUMN});
         }
      }

      if ($form->isSend() && $form->save->getValues() == false) {
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }

      return $form;
   }

   protected function checkDeleteItem()
   {
      if (!$this->rights()->isWritable() && !$this->rights()->isControll()) {
         return;
      }
      $form = new Form('delete_dwfile_');
      $elemName = new Form_Element_Hidden('id');
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemName);

      $elemSubmit = new Form_Element_Submit('submit', $this->tr('Smazat'));
      $form->addElement($elemSubmit);

      if ($form->isValid()) {
         $model = new DownloadFiles_Model();
         $fileRec = $model->record($form->id->getValues());
         // PROČ ????
         if (!$this->checkValidEditFileRecord($fileRec)) {
            $this->log(sprintf('pokus o smazání souboru "%s" z cizí kategorie', $fileRec->{DownloadFiles_Model::COLUMN_FILE}));
            throw new InvalidArgumentException($this->tr('Tento soubor nelze smazat. nepatří do dané kategorie'));
         }

         if (is_file($this->module()->getDataDir() . $fileRec->{DownloadFiles_Model::COLUMN_FILE})) {
            @unlink($this->module()->getDataDir() . $fileRec->{DownloadFiles_Model::COLUMN_FILE});
         }
         $model->delete($fileRec);
         $this->infoMsg()->addMessage($this->tr('Soubor byl smazán'));
         $this->link()->route()->reload();
      }
      $this->view()->formDelete = $form;
   }

   private function checkValidEditFileRecord(Model_ORM_Record $record)
   {
      $sec = DownloadFiles_Model_Sections::getRecord($record->{DownloadFiles_Model::COLUMN_ID_SECTION});
      if ($record == false || 
         ($record instanceof Model_ORM_Record && $record->isNew()) || 
         $sec->{DownloadFiles_Model_Sections::COLUMN_ID_CATEGORY} != $this->category()->getId()) {
         return false;
      }
      return true;
   }

   protected function settings(&$settings, Form &$form)
   {
      $fGrp = $form->addGroup('access', $this->tr('Přístup'));

      $elemPass = new Form_Element_Text('pass', $this->tr('Heslo pro přístup'));
      $elemPass->setSubLabel($this->tr('Pokud je heslo zadáno, položky se vypíší až po zadání hesla'));
      $form->addElement($elemPass, $fGrp);

      if (isset($settings[self::PARAM_PASS])) {
         $form->pass->setValues($settings[self::PARAM_PASS]);
      }

      // znovu protože mohl být už jednou validován bez těchto hodnot
      if ($form->isValid()) {
         $settings[self::PARAM_PASS] = $form->pass->getValues();
      }
   }

}
