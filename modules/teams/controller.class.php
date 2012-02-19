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
      //		Kontrola práv
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
   }

   /**
    * Kontroler pro přidání novinky
    */
   public function addController() {
      $this->checkWritebleRights();
      $addForm = $this->createForm();

      if ($addForm->isValid()) {
         $idTeam = 0;
         
         if($addForm->groupNewName->getValues() != null){
            // nová skupina
            $modelGrp = new Teams_Model();
            $newGrp = $modelGrp->newRecord();
            $newGrp->{Teams_Model::COLUMN_ID_CATEGORY} = $this->category()->getId();
            $newGrp->{Teams_Model::COLUMN_NAME} = $addForm->groupNewName->getValues();
            
            $c = $modelGrp->columns(array('m' => 'MAX(`'.  Teams_Model::COLUMN_ORDER.'`)'))
            ->where(Teams_Model::COLUMN_ID_CATEGORY.' = :idc', 
               array('idc' => $this->category()->getId() ))->record()->m;
            
            $newGrp->{Teams_Model::COLUMN_ORDER} = $c+1;
            
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
            $name = vve_cr_safe_file_name($addForm->name->getValues()." ".$addForm->surname->getValues());
            $image->copy($this->module()->getDataDir(), false, $name.".".$image->getExtension());
            // store resized
            $resized = $image->copy($this->module()->getDataDir(), true, $name."-resized.".$image->getExtension());
            
            $resized->getData()->resize(
               $this->category()->getParam('imgw', self::DEFAULT_IMAGE_WIDTH), 
               $this->category()->getParam('imgh', self::DEFAULT_IMAGE_HEIGHT), 
               $this->category()->getParam('cropimg', self::DEFAULT_IMAGE_CROP) == true ? File_Image_Base::RESIZE_CROP : File_Image_Base::RESIZE_AUTO);
            $resized->save();
            $record->{Teams_Model_Persons::COLUMN_IMAGE} = $resized->getName();
            $image->delete();
         }
         
         $record->{Teams_Model_Persons::COLUMN_NAME} = $addForm->name->getValues();
         $record->{Teams_Model_Persons::COLUMN_SURNAME} = $addForm->surname->getValues();
         $record->{Teams_Model_Persons::COLUMN_DEGREE} = $addForm->degree->getValues();
         $record->{Teams_Model_Persons::COLUMN_DEGREE_AFTER} = $addForm->degreeAfter->getValues();
         $record->{Teams_Model_Persons::COLUMN_TEXT} = $addForm->text->getValues();
         $record->{Teams_Model_Persons::COLUMN_TEXT_CLEAR} = strip_tags($addForm->text->getValues());
         $record->{Teams_Model_Persons::COLUMN_LINK} = $addForm->link->getValues();
         
         // zařadit na konec kupiny
         $c = $model->columns(array('m' => 'MAX(`'.Teams_Model_Persons::COLUMN_ORDER.'`)'))
            ->where(Teams_Model_Persons::COLUMN_DELETED.' = 0 AND '.Teams_Model_Persons::COLUMN_ID_TEAM.' = :idt', 
               array('idt' => $idTeam ))->record()->m;
            
         $record->{Teams_Model_Persons::COLUMN_ORDER} = $c + 1;
         
         $model->save($record);
         
         $this->infoMsg()->addMessage($this->tr('Osoba byla uložena'));
         $this->link()->route()->reload();
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

      // element pro odstranění obrázku
      if($person->{Teams_Model_Persons::COLUMN_IMAGE} != null){
         $elemRemImg = new Form_Element_Checkbox('imgdel', $this->tr('Odstranit uložený portrét'));
         $elemRemImg->setSubLabel($this->tr('Uložen portrét').': '.$person->{Teams_Model_Persons::COLUMN_IMAGE});
         $editForm->addElement($elemRemImg, 'others');
      }

      if ($editForm->isValid()) {
         
         if($editForm->groupNewName->getValues() != null){
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
         
         
         if ($editForm->image->getValues() != null OR ($editForm->haveElement('imgdel') AND $editForm->imgdel->getValues() == true)) {
            // smaže se původní
            if(is_file($this->category()->getModule()->getDataDir().$person->{Teams_Model_Persons::COLUMN_IMAGE})){
               /* if upload file with same name it's overwrited and then deleted. This make error!!! */
//               @unlink($this->category()->getModule()->getDataDir().$person->{Teams_Model_Persons::COLUMN_IMAGE});
            }
            $person->{Teams_Model_Persons::COLUMN_IMAGE} = null;
         }

         if ($editForm->image->getValues() != null) {
            $image = new File_Image($editForm->image);
            // store original
            $name = vve_cr_safe_file_name($editForm->name->getValues()." ".$editForm->surname->getValues());
            $image->copy($this->module()->getDataDir(), false, $name.".".$image->getExtension());
            // store resized
            $resized = $image->copy($this->module()->getDataDir(), true, $name."-resized.".$image->getExtension());
            
            $resized->getData()->resize(
               $this->category()->getParam('imgw', self::DEFAULT_IMAGE_WIDTH), 
               $this->category()->getParam('imgh', self::DEFAULT_IMAGE_HEIGHT), 
               $this->category()->getParam('cropimg', self::DEFAULT_IMAGE_CROP) == true ? File_Image_Base::RESIZE_CROP : File_Image_Base::RESIZE_AUTO);
            $resized->save();
            $person->{Teams_Model_Persons::COLUMN_IMAGE} = $resized->getName();
            $image->delete();
         }
         
         $person->{Teams_Model_Persons::COLUMN_NAME} = $editForm->name->getValues();
         $person->{Teams_Model_Persons::COLUMN_SURNAME} = $editForm->surname->getValues();
         $person->{Teams_Model_Persons::COLUMN_DEGREE} = $editForm->degree->getValues();
         $person->{Teams_Model_Persons::COLUMN_DEGREE_AFTER} = $editForm->degreeAfter->getValues();
         $person->{Teams_Model_Persons::COLUMN_TEXT} = $editForm->text->getValues();
         $person->{Teams_Model_Persons::COLUMN_TEXT_CLEAR} = strip_tags($editForm->text->getValues());
         $person->{Teams_Model_Persons::COLUMN_ID_TEAM} = $idTeam;
         $person->{Teams_Model_Persons::COLUMN_LINK} = $editForm->link->getValues();
         
         // tohle zde není, protože není jak to řadit zde
//         $person->{Teams_Model_Persons::COLUMN_ORDER} = $c + 1;
         
         $model->save($person);

         $this->infoMsg()->addMessage($this->tr('Osoba byla uložena'));
         $this->link()->route()->reload();
      }
      $this->view()->form = $editForm;
      $this->view()->person = $person;
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
      $iSurName->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iSurName, $gbase);

      $iDegree = new Form_Element_Text('degree', $this->tr('Titul'));
      $form->addElement($iDegree, $gbase);

      $iDegreeA = new Form_Element_Text('degreeAfter', $this->tr('Titul za jménem'));
      $form->addElement($iDegreeA, $gbase);

      $iText = new Form_Element_TextArea('text', $this->tr('Popis'));
      $iText->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iText, $gbase);

      $iGroup = new Form_Element_Select('groupId', $this->tr('Skupina'));
      $iGroup->setSubLabel($this->tr('Zařazení do skupiny umožňuje seskupovat osoby do jednoho týmu.'));
      
      // již uložené skupiny
      $model = new Teams_Model();
      $groups = $model
         ->where(Teams_Model::COLUMN_ID_CATEGORY." = :idc", array('idc' => $this->category()->getId()))
         ->order(array(Teams_Model::COLUMN_ORDER => Model_ORM::ORDER_ASC))
         ->records(PDO::FETCH_OBJ);
      
      if($groups != false){
         foreach ($groups as $grp) {
            $iGroup->addOption($grp->{Teams_Model::COLUMN_NAME}, $grp->{Teams_Model::COLUMN_ID});
         }
         $form->addElement($iGroup, $gothr);
      }
      
      $iNewGroup = new Form_Element_Text('groupNewName', $this->tr('Nový název skupiny'));
      if($groups != false){
         $iNewGroup->setSubLabel($this->tr('Pokud není zadán, použije se skupina z předchozího výběru.'));
      } else {
         $iNewGroup->addValidation(new Form_Validator_NotEmpty());
      }
      
      $form->addElement($iNewGroup, $gothr);
      
      $iImage = new Form_Element_File('image', $this->tr('Portrét'));
      $iImage->setSubLabel($this->tr('Velikost obrázku je upravena automaticky'));
      $iImage->addValidation(new Form_Validator_FileExtension('jpg;png'));
      $iImage->setUploadDir(AppCore::getAppCacheDir());
      $form->addElement($iImage, $gothr);
      
      $iLink = new Form_Element_Text('link', $this->tr('Prolink'));
      $iLink->addValidation(New Form_Validator_Url());
      $iLink->setSubLabel($this->tr('Například odkaz na profil uživatele na stránkách či jinou externí službu (facebook)'));
      $form->addElement($iLink, $gothr);

      $iSubmit = new Form_Element_SaveCancel('save');
      $form->addElement($iSubmit);

      if($person instanceof Model_ORM_Record){
         $form->name->setValues($person->{Teams_Model_Persons::COLUMN_NAME});
         $form->surname->setValues($person->{Teams_Model_Persons::COLUMN_SURNAME});
         $form->groupId->setValues($person->{Teams_Model_Persons::COLUMN_ID_TEAM});
         $form->degree->setValues($person->{Teams_Model_Persons::COLUMN_DEGREE});
         $form->degreeAfter->setValues($person->{Teams_Model_Persons::COLUMN_DEGREE_AFTER});
         $form->text->setValues($person->{Teams_Model_Persons::COLUMN_TEXT});
         $form->link->setValues($person->{Teams_Model_Persons::COLUMN_LINK});
//         $form->order->setValues($person->{groupId::COLUMN_ORDER});
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
         $modelTeams = new Teams_Model();
         $stmt = $modelTeams->query("UPDATE {THIS} SET `".Teams_Model::COLUMN_ORDER."` = :ord WHERE ".Teams_Model::COLUMN_ID." = :id");
         foreach ($teamOrders as $id => $order) {
            $stmt->bindValue('id', $id);
            $stmt->bindValue('ord', $order);
            $stmt->execute();
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
               'name' => $item->{Teams_Model::COLUMN_NAME},
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
      $elemImgW->setSubLabel($this->tr('Výchozí: ') . self::DEFAULT_IMAGE_WIDTH . ' px');
      $elemImgW->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemImgW, 'images');

      $elemImgH = new Form_Element_Text('imgh', $this->tr('Výška portrétu'));
      $elemImgH->setSubLabel($this->tr('Výchozí: ') . self::DEFAULT_IMAGE_HEIGHT . ' px');
      $elemImgH->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemImgH, 'images');

      $elemCropImage = new Form_Element_Checkbox('cropimg', $this->tr('Ořezávat portréty'));
      $form->addElement($elemCropImage, 'images');

      if (isset($settings['imgw'])) {
         $form->imgw->setValues($settings['imgw']);
      }
      if (isset($settings['imgh'])) {
         $form->imgh->setValues($settings['imgh']);
      }
      if (isset($settings['cropimg'])) {
         $form->cropimg->setValues($settings['cropimg']);
      }

      if (isset($settings['recordsonpage'])) {
         $form->numOnPage->setValues($settings['recordsonpage']);
      }
      // znovu protože mohl být už jednou validován bez těchto hodnot
      if ($form->isValid()) {
         $settings['imgw'] = $form->imgw->getValues();
         $settings['imgh'] = $form->imgh->getValues();
         $settings['cropimg'] = $form->cropimg->getValues();
         $settings['recordsonpage'] = $form->numOnPage->getValues();
      }
   }

}
?>