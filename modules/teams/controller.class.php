<?php

class Teams_Controller extends Controller {
   const DEFAULT_IMAGE_WIDTH = 90;
   const DEFAULT_IMAGE_HEIGHT = 120;
   const DEFAULT_IMAGE_CROP = false;
   const DEFAULT_RECORDS_ON_PAGE = 10;

   const DATA_DIR = 'team-persons';


   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController() {
      //    Kontrola práv
      $this->checkReadableRights();
      
      $model = new Teams_Model_Persons();
      
      if($this->category()->getRights()->isWritable()){
         $formDel = new Form('person_del_');

         $elemId = new Form_Element_Hidden('id');
         $formDel->addElement($elemId);

         $elemSubmit = new Form_Element_Submit('delete', $this->tr('Smazat'));
         $formDel->addElement($elemSubmit);

         if($formDel->isValid()){
            $model->delete($formDel->id->getValues());

            $this->infoMsg()->addMessage($this->tr('Osoba byla smazána'));
            $this->link()->rmParam()->reload();
         }
         $this->view()->formDelete = $formDel;
      }
      
      $this->view()->teams = $this->getTeams();
      
      // načtení textu
      $textM = new Text_Model();
      $textRecord = $textM->where(Text_Model::COLUMN_ID_CATEGORY.' = :idc AND '.Text_Model::COLUMN_SUBKEY.' = :subkey', 
         array('idc' => $this->category()->getId(), 'subkey' => Text_Controller::TEXT_MAIN_KEY) )->record();
      $this->view()->text = $textRecord;
   }

   /**
    * Kontroler pro přidání novinky
    */
   public function addController() {
      $this->checkWritebleRights();
      $addForm = $this->createForm();

      if ($addForm->isValid()) {
         $idTeam = 0;
         $newGrpName = $addForm->groupNewName->getValues();
         if($newGrpName[Locales::getDefaultLang()] != null){
            // nová skupina
            $modelGrp = new Teams_Model();
            $newGrp = $modelGrp->newRecord();
            $newGrp->{Teams_Model::COLUMN_ID_CATEGORY} = $this->category()->getId();
            $newGrp->{Teams_Model::COLUMN_NAME} = $addForm->groupNewName->getValues();
            $idTeam = $modelGrp->save($newGrp);
         } else if(isset ($addForm->groupId)){
            $idTeam = (int)$addForm->groupId->getValues();
         } else {
            throw new UnexpectedValueException($this->tr('Vloženo neočekávané id týmu'), 1);
         }
         
         $model = new Teams_Model_Persons();
         $record = $model->newRecord();
         $record->{Teams_Model_Persons::COLUMN_ID_TEAM} = $idTeam;
         
         if ($addForm->image->getValues() != null) {
            $image = new File_Image($addForm->image);
            // store original
            $image->move($this->module()->getDataDir());
            $record->{Teams_Model_Persons::COLUMN_IMAGE} = $image->getName();
         }
         
         $this->assignRecordData($addForm, $record);
         
         $this->infoMsg()->addMessage($this->tr('Osoba byla uložena'));
//         if($addForm->gotoEdit->getValues() == true){
//            $this->link()->route("editPhoto", array('id' => $record->getPK()))->reload();
//         } else {
            $this->link()->route()->reload();
//         }
      }
      $this->view()->form = $addForm;
   }

   /**
    * controller pro úpravu novinky
    */
   public function editController() {
      $this->checkWritebleRights();

      // načtení dat
      $model = new Teams_Model_Persons();
      $person = $model->record($this->getRequest('id'));
      if($person == false) return false;

      $editForm = $this->createForm($person);

      if ($editForm->isValid()) {

         $grpName = $editForm->groupNewName->getValues();
         if($grpName[Locales::getDefaultLang()] != null){
            // nová skupina
            $modelGrp = new Teams_Model();
            $newGrp = $modelGrp->newRecord();
            $newGrp->{Teams_Model::COLUMN_ID_CATEGORY} = $this->category()->getId();
            $newGrp->{Teams_Model::COLUMN_NAME} = $editForm->groupNewName->getValues();
            
            $c = $modelGrp->columns(array('m' => 'MAX(`'.  Teams_Model::COLUMN_ORDER.'`)'))
            ->where(Teams_Model::COLUMN_ID_CATEGORY.' = :idc', 
               array('idc' => $this->category()->getId() ))->record()->m;
            
            $newGrp->{Teams_Model::COLUMN_ORDER} = $c+1;
            
            $idTeam = $modelGrp->save($newGrp);
         } else if(isset ($editForm->groupId)){
            $idTeam = (int)$editForm->groupId->getValues();
         } else {
            throw new UnexpectedValueException($this->tr('Vloženo neočekávané id týmu'), 1);
         }
         
         $img = $editForm->image->getValues();
         if($img){
            $person->{Teams_Model_Persons::COLUMN_IMAGE} = $img['name'];
         }
         $person->{Teams_Model_Persons::COLUMN_ID_TEAM} = $idTeam;
         
         $this->assignRecordData($editForm, $person);

         $this->infoMsg()->addMessage($this->tr('Osoba byla uložena'));
         $this->link()->route()->reload();
      }
      $this->view()->form = $editForm;
      $this->view()->person = $person;
   }
   
   protected function assignRecordData(Form $form, Model_ORM_Record $person)
   {
      $person->{Teams_Model_Persons::COLUMN_NAME} = $form->name->getValues();
         $person->{Teams_Model_Persons::COLUMN_SURNAME} = $form->surname->getValues();
         $person->{Teams_Model_Persons::COLUMN_WORK} = $form->work->getValues();
         $person->{Teams_Model_Persons::COLUMN_DEGREE} = $form->degree->getValues();
         $person->{Teams_Model_Persons::COLUMN_DEGREE_AFTER} = $form->degreeAfter->getValues();
         $person->{Teams_Model_Persons::COLUMN_TEXT} = $form->text->getValues();
         $person->{Teams_Model_Persons::COLUMN_LINK} = $form->link->getValues();
         $person->{Teams_Model_Persons::COLUMN_PHONE} = $form->phone->getValues();
         $person->{Teams_Model_Persons::COLUMN_EMAIL} = $form->email->getValues();
         $person->{Teams_Model_Persons::COLUMN_SOCIAL} = $form->linkSocial->getValues();
         $person->save();
   }
   
   public function editPhotoController() {
      $this->checkWritebleRights();

      // načtení dat
      $model = new Teams_Model_Persons();
      $person = $model->record($this->getRequest('id'));
      if($person == false) return false;

      $editForm = new Form('edit_photo_');

      $elemX = new Form_Element_Hidden('start_x');
      $editForm->addElement($elemX);
      $elemY = new Form_Element_Hidden('start_y');
      $editForm->addElement($elemY);

      $elemW = new Form_Element_Hidden('width');
      $editForm->addElement($elemW);

      $elemH = new Form_Element_Hidden('height');
      $editForm->addElement($elemH);

      $elemSubmit = new Form_Element_SaveCancel('save');
      $editForm->addElement($elemSubmit);
      
      if($editForm->isSend() && $editForm->save->getValues() == false){
         $this->link()->route()->reload();
      }
      
      if ($editForm->isValid()) {
         $fileName = str_replace("-resized", "", $person->{Teams_Model_Persons::COLUMN_IMAGE});
         $image = new File_Image($this->category()->getModule()->getDataDir().$fileName);
          
         $imgNew = $image->copy($this->module()->getDataDir(), true, $person->{Teams_Model_Persons::COLUMN_IMAGE},false);
            
         $imgNew->getData()
            ->crop(
               $editForm->start_x->getValues(), $editForm->start_y->getValues(), 
               $editForm->width->getValues(), $editForm->height->getValues()
               )
            ->resize(
               $this->category()->getParam('imgw', self::DEFAULT_IMAGE_WIDTH), 
               $this->category()->getParam('imgh', self::DEFAULT_IMAGE_HEIGHT), 
               $this->category()->getParam('cropimg', self::DEFAULT_IMAGE_CROP) == true ? File_Image_Base::RESIZE_CROP : File_Image_Base::RESIZE_AUTO
               )
               ;
         $imgNew->save();
            
//         $person->{Teams_Model_Persons::COLUMN_IMAGE} = $imgNew->getName();
//         $model->save($person);

         $this->infoMsg()->addMessage($this->tr('portrét byl uložen'));
         $this->link()->route()->reload();
      }
      $this->view()->form = $editForm;
      $this->view()->person = $person;
      
      $this->view()->imgW = $this->category()->getParam('imgw', self::DEFAULT_IMAGE_WIDTH);
      $this->view()->imgH = $this->category()->getParam('imgh', self::DEFAULT_IMAGE_HEIGHT);
      $this->view()->imgC = $this->category()->getParam('cropimg', self::DEFAULT_IMAGE_CROP);
   }

   /**
    * Metoda  vytvoří element formuláře
    * @return Form
    */
   protected function createForm(Model_ORM_Record $person = null) {
      $form = new Form('person_');

      $gbase = $form->addGroup('basic', $this->tr('Základní informace o osobě'));
      $gothr = $form->addGroup('others', $this->tr('ostatní'));

      $iName = new Form_Element_Text('name', $this->tr('Jméno'));
      $iName->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iName, $gbase);

      $iSurName = new Form_Element_Text('surname', $this->tr('Přijmení'));
//      $iSurName->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iSurName, $gbase);

      $iDegree = new Form_Element_Text('degree', $this->tr('Titul'));
      $form->addElement($iDegree, $gbase);

      $iDegreeA = new Form_Element_Text('degreeAfter', $this->tr('Titul za jménem'));
      $form->addElement($iDegreeA, $gbase);
      
      $iWork = new Form_Element_Text('work', $this->tr('Činnost/práce'));
      $iWork->setSubLabel($this->tr('Například co daná osoba dělá.'));
      $form->addElement($iWork, $gbase);
      
      $iPhone = new Form_Element_Text('phone', $this->tr('Telefon'));
      $form->addElement($iPhone, $gbase);
      
      $iMail = new Form_Element_Text('email', $this->tr('E-mail'));
      $iMail->addValidation(new Form_Validator_Email());
      $form->addElement($iMail, $gbase);

      $iText = new Form_Element_TextArea('text', $this->tr('Popis'));
      $iText->setLangs();
//      $iText->addValidation(New Form_Validator_NotEmpty(null, Locales::getDefaultLang()));
      $form->addElement($iText, $gbase);

      $iGroup = new Form_Element_Select('groupId', $this->tr('Skupina'));
      $iGroup->setSubLabel($this->tr('Zařazení do skupiny umožňuje seskupovat osoby do jednoho týmu.'));
      
      // již uložené skupiny
      $model = new Teams_Model();
      $groups = $model
         ->where(Teams_Model::COLUMN_ID_CATEGORY." = :idc", array('idc' => $this->category()->getId()))
         ->order(array(Teams_Model::COLUMN_ORDER => Model_ORM::ORDER_ASC))
         ->records();
      
      if($groups != false){
         foreach ($groups as $grp) {
            if((string)$grp->{Teams_Model::COLUMN_NAME} != null){
               $iGroup->addOption((string)Utils_String::getLangString($grp->{Teams_Model::COLUMN_NAME}), $grp->{Teams_Model::COLUMN_ID});
            }
         }
         $form->addElement($iGroup, $gothr);
      }
      
      $iNewGroup = new Form_Element_Text('groupNewName', $this->tr('Nový název skupiny'));
      $iNewGroup->setLangs();
      if($groups != false){
         $iNewGroup->setSubLabel($this->tr('Pokud není zadán, použije se skupina z předchozího výběru.'));
      } else {
         $iNewGroup->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang()));
      }
      
      $form->addElement($iNewGroup, $gothr);
      
      $iImage = new Form_Element_Image('image', $this->tr('Portrét'));
      $iImage->setSubLabel($this->tr('Velikost obrázku je upravena automaticky'));
      $iImage->setUploadDir($this->category()->getModule()->getDataDir());
      $iImage->setAllowDelete(true);
      $form->addElement($iImage, $gothr);
      
      $iLink = new Form_Element_Text('link', $this->tr('Prolink'));
      $iLink->addValidation(New Form_Validator_Url());
      $iLink->setSubLabel($this->tr('Například odkaz na profil uživatele na stránkách či jinou externí službu (linkedin)'));
      $form->addElement($iLink, $gothr);
      
      $iLinkSocial = new Form_Element_Text('linkSocial', $this->tr('Sociální síť'));
      $iLinkSocial->addValidation(New Form_Validator_Url());
      $iLinkSocial->setSubLabel($this->tr('Například odkaz na profil uživatele na stránkách či jinou externí službu (facebook)'));
      $form->addElement($iLinkSocial, $gothr);

      $iSubmit = new Form_Element_SaveCancel('save');
      $form->addElement($iSubmit);

      if($person instanceof Model_ORM_Record){
         if($person->{Teams_Model_Persons::COLUMN_IMAGE} != null){
            $form->image->setValues($person->{Teams_Model_Persons::COLUMN_IMAGE});
         }
         $form->name->setValues($person->{Teams_Model_Persons::COLUMN_NAME});
         $form->surname->setValues($person->{Teams_Model_Persons::COLUMN_SURNAME});
         $form->work->setValues($person->{Teams_Model_Persons::COLUMN_WORK});
         $form->groupId->setValues($person->{Teams_Model_Persons::COLUMN_ID_TEAM});
         $form->degree->setValues($person->{Teams_Model_Persons::COLUMN_DEGREE});
         $form->degreeAfter->setValues($person->{Teams_Model_Persons::COLUMN_DEGREE_AFTER});
         $form->text->setValues($person->{Teams_Model_Persons::COLUMN_TEXT});
         $form->link->setValues($person->{Teams_Model_Persons::COLUMN_LINK});
         $form->phone->setValues($person->{Teams_Model_Persons::COLUMN_PHONE});
         $form->email->setValues($person->{Teams_Model_Persons::COLUMN_EMAIL});
         $form->linkSocial->setValues($person->{Teams_Model_Persons::COLUMN_SOCIAL});
      }
      
      if($form->isSend() && $form->save->getValues() == false){
         $this->link()->route()->reload();
      }
      
      return $form;
   }

   public function editOrderController()
   {
      $this->checkWritebleRights();
      
      $form = new Form('person_order_');
      
      $ePTeamId = new Form_Element_Hidden('personTeamId');
      $ePTeamId->setDimensional();
      $form->addElement($ePTeamId);
      $ePOrd = new Form_Element_Hidden('personOrd');
      $ePOrd->setDimensional();
      $form->addElement($ePOrd);
      
      $eTeamId = new Form_Element_Hidden('teamId');
      $eTeamId->setDimensional();
      $form->addElement($eTeamId);
      
      $eTeamOrd = new Form_Element_Hidden('teamOrder');
      $eTeamOrd->setDimensional();
      $form->addElement($eTeamOrd);
      
      $eSave = new Form_Element_SaveCancel('save');
      $form->addElement($eSave);

      $eRemEmpty = new Form_Element_Checkbox('removeEmptyTeams', $this->tr('Odstranit prázdné týmy'));
      $form->addElement($eRemEmpty);
      
      if($form->isSend() && $form->save->getValues() == false){
         $this->link()->route()->reload();
      }
      
      if($form->isValid()){
         $teamOrders = $form->teamOrder->getValues();
         $personOrders = $form->personOrd->getValues();
         $personTeamId = $form->personTeamId->getValues();
         
         // update teams
         foreach ($teamOrders as $id => $order) {
            Teams_Model::setRecordPosition($id, $order);
         }
         
         // update persons
         $modelPersons = new Teams_Model_Persons();
         $stmt = $modelPersons->query("UPDATE {THIS} SET `".Teams_Model_Persons::COLUMN_ORDER."` = :ord, `".Teams_Model_Persons::COLUMN_ID_TEAM."` = :idteam "
            ."WHERE ".  Teams_Model_Persons::COLUMN_ID." = :id");
         foreach ($personTeamId as $id => $idTeam) {
            $stmt->bindValue('id', $id);
            $stmt->bindValue('ord', $personOrders[$id]);
            $stmt->bindValue('idteam', $idTeam);
            $stmt->execute();
         }
         
         $modelTeams = new Teams_Model();
         if($form->removeEmptyTeams->getValues() == true){
            $teams = $modelTeams->join(Teams_Model::COLUMN_ID, "Teams_Model_Persons", Teams_Model_Persons::COLUMN_ID_TEAM)
               ->order(array(
                  Teams_Model::COLUMN_ORDER => Model_ORM::ORDER_ASC,
                  Teams_Model::COLUMN_NAME => Model_ORM::ORDER_ASC,
                  Teams_Model_Persons::COLUMN_ORDER => Model_ORM::ORDER_ASC,
                  Teams_Model_Persons::COLUMN_NAME => Model_ORM::ORDER_ASC,
               ))
               ->groupBy(array(Teams_Model::COLUMN_ID))
               ->where(Teams_Model::COLUMN_ID_CATEGORY." = :idc", array('idc' => $this->category()->getId()))->records();
            $modelTeams = new Teams_Model();
            foreach ($teams as $id => $team) {
               if($team->{Teams_Model_Persons::COLUMN_ID} == null){
                  $modelTeams->delete($team);
               }
            }
         }
         
         $this->infoMsg()->addMessage($this->tr('Pořadí bylo uloženo'));
         $this->link()->route()->reload();
      }
      
      $this->view()->form = $form;
      $this->view()->teams = $this->getTeams();
   }

   public function editTextController() {
      $this->checkControllRights();
      $form = new Form('list_text_', true);
      
      $textM = new Text_Model();
      $textRecord = $textM->where(Text_Model::COLUMN_ID_CATEGORY.' = :idc AND '.Text_Model::COLUMN_SUBKEY.' = :subkey', 
         array('idc' => $this->category()->getId(), 'subkey' => Text_Controller::TEXT_MAIN_KEY) )->record();

      $elemText = new Form_Element_TextArea('text', $this->tr('Text'));
      $elemText->setLangs();
      if($textRecord != false){
         $elemText->setValues($textRecord->{Text_Model::COLUMN_TEXT});
      }
      $form->addElement($elemText);

      $elemS = new Form_Element_SaveCancel('save');
      $form->addElement($elemS);

      if($form->isSend() AND $form->save->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Úpravy úvodního textu byly zrušeny'));
         $this->link()->route()->redirect();
      }

      if($form->isValid()) {
         if($textRecord == false){
            $textRecord = $textM->newRecord();
         }
         
         $textRecord->{Text_Model::COLUMN_TEXT} = $form->text->getValues(); 
         $textRecord->{Text_Model::COLUMN_TEXT_CLEAR} = vve_strip_tags($form->text->getValues()); 
         $textRecord->{Text_Model::COLUMN_ID_CATEGORY} = $this->category()->getId(); 
         $textRecord->{Text_Model::COLUMN_SUBKEY} = Text_Controller::TEXT_MAIN_KEY; 
         
         $textM->save($textRecord);

         $this->infoMsg()->addMessage($this->tr('Úvodní text byl uložen'));
         $this->link()->route()->redirect();
      }

      $this->view()->form = $form;
   }
   
   private function getTeams()
   {
      $model = new Teams_Model();
      
      $model->join(Teams_Model::COLUMN_ID, "Teams_Model_Persons", Teams_Model_Persons::COLUMN_ID_TEAM)
         ->order(array(
            Teams_Model::COLUMN_ORDER => Model_ORM::ORDER_ASC,
            Teams_Model::COLUMN_NAME => Model_ORM::ORDER_ASC,
            Teams_Model_Persons::COLUMN_ORDER => Model_ORM::ORDER_ASC,
            Teams_Model_Persons::COLUMN_NAME => Model_ORM::ORDER_ASC,
         ))
         ->where(Teams_Model::COLUMN_ID_CATEGORY." = :idc", array('idc' => $this->category()->getId()))
         ;
      
      $recs = $model->records();

      
      $teams = array();
      foreach ($recs as $item) {
         $idt = $item->{Teams_Model::COLUMN_ID};
         if(!isset ($teams[$idt])){
            $teams[$idt] = array(
               'name' => vve_get_lang_string($item->{Teams_Model::COLUMN_NAME}),
               'order' => $item->{Teams_Model::COLUMN_ORDER},
               'persons' => array(),
            );
         }
         if($item->{Teams_Model_Persons::COLUMN_ID} != null){
            array_push($teams[$idt]['persons'], $item);
         }
      }
      
      return $teams;
   }


   /**
    * Smazání článků při odstranění kategorie
    * @param Category $category
    */
   public static function clearOnRemove(Category $category) {

   }

   /**
    * Metoda pro nastavení modulu
    */
   public function settings(&$settings, Form &$form) {
      $eOnPage = new Form_Element_Text('numOnPage', $this->tr('Počet osob na stránku'));
      $eOnPage->addValidation(new Form_Validator_IsNumber());
      $eOnPage->setSubLabel(sprintf($this->tr('Výchozí: %s osob na stránku'), self::DEFAULT_RECORDS_ON_PAGE));
      $form->addElement($eOnPage, 'view');


      $form->addGroup('images', $this->tr('Nasatvení obrázků'));

      $elemImgW = new Form_Element_Text('imgw', $this->tr('Šířka portrétu'));
      $elemImgW->setSubLabel($this->tr('Výchozí: ') . $this->category()->getGlobalParam('imgw', self::DEFAULT_IMAGE_WIDTH) . ' px');
      $elemImgW->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemImgW, 'images');

      $elemImgH = new Form_Element_Text('imgh', $this->tr('Výška portrétu'));
      $elemImgH->setSubLabel($this->tr('Výchozí: ') . $this->category()->getGlobalParam('imgh', self::DEFAULT_IMAGE_HEIGHT) . ' px');
      $elemImgH->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemImgH, 'images');

      $elemCropImage = new Form_Element_Checkbox('croping', $this->tr('Ořezávat portréty'));
      if($this->category()->getGlobalParam('croping') == true){
         $elemCropImage->setValues(true);
      }
      $form->addElement($elemCropImage, 'images');

      if (isset($settings['imgw'])) {
         $form->imgw->setValues($settings['imgw']);
      }
      if (isset($settings['imgh'])) {
         $form->imgh->setValues($settings['imgh']);
      }
      if (isset($settings['croping'])) {
         $form->croping->setValues($settings['croping']);
      }

      if (isset($settings['recordsonpage'])) {
         $form->numOnPage->setValues($settings['recordsonpage']);
      }
      // znovu protože mohl být už jednou validován bez těchto hodnot
      if ($form->isValid()) {
         $settings['imgw'] = $form->imgw->getValues();
         $settings['imgh'] = $form->imgh->getValues();
         $settings['croping'] = $form->croping->getValues();
         $settings['recordsonpage'] = $form->numOnPage->getValues();
      }
   }

}