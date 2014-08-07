<?php

class People_Controller extends Controller {
   const DEFAULT_RECORDS_ON_PAGE = 10;

   const DATA_DIR = 'people';


   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      $model = new People_Model();
      $model->where(People_Model::COLUMN_ID_CATEGORY.' = :idc', array('idc' => $this->category()->getId()))
         ->order(array(
            People_Model::COLUMN_ORDER => Model_ORM::ORDER_ASC,
            People_Model::COLUMN_SURNAME => Model_ORM::ORDER_ASC,
            People_Model::COLUMN_NAME => Model_ORM::ORDER_ASC,
         ));

      if($this->getRequestParam('sid', null) != null){
         $all = $model->records();
         $page = 1;
         $counter = 1;
         foreach($all as $person) {
            if($counter > $this->category()->getParam('recordsonpage', self::DEFAULT_RECORDS_ON_PAGE)){
               $counter = 1;
               $page++;
            }
            if($person->{People_Model::COLUMN_ID} == $this->getRequestParam('sid')){
               $this->link()->param(Component_Scroll::GET_PARAM, $page)->anchor('person-'.$person->{People_Model::COLUMN_ID})
               ->rmParam('sid')->reload();
            }
            $counter++;
         }


      }

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

      $scrollComponent = null;
      if($this->category()->getParam('recordsonpage', self::DEFAULT_RECORDS_ON_PAGE) != 0){
         $scrollComponent = new Component_Scroll();
         $scrollComponent->setConfig(Component_Scroll::CONFIG_CNT_ALL_RECORDS, $model->count());

         $scrollComponent->setConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE,
              $this->category()->getParam('recordsonpage', self::DEFAULT_RECORDS_ON_PAGE));

         $model->limit($scrollComponent->getStartRecord(), $scrollComponent->getRecordsOnPage());
      }

      $this->view()->compScroll = $scrollComponent;
      $this->view()->people = $model->records();
   }

   /**
    * Kontroler pro přidání novinky
    */
   public function addController() {
      $this->checkWritebleRights();
      $addForm = $this->createForm();

      if ($addForm->isValid()) {
         $model = new People_Model();
         $record = $model->newRecord();
         $this->processEditForm($addForm, $record);
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
      $model = new People_Model();
      $person = $model->record($this->getRequest('id'));
      if($person == false) {
         throw new UnexpectedPageException();
      }

      $editForm = $this->createForm($person);

      

      if ($editForm->isValid()) {
         $this->processEditForm($editForm, $person);
         $this->infoMsg()->addMessage($this->tr('Osoba byla uložena'));
         $this->link()->route()->reload();
      }
      $this->view()->form = $editForm;
      $this->view()->person = $person;
   }

   protected function processEditForm(Form $form, Model_ORM_Record $person = null)
   {
      
      if ($form->image->getValues() != null OR ($form->haveElement('imgdel') AND $form->imgdel->getValues() == true)) {
         // smaže se původní
         if(is_file($this->category()->getModule()->getDataDir().$person->{People_Model::COLUMN_IMAGE})){
            /* if upload file with same name it's overwrited and then deleted. This make error!!! */
//               @unlink($this->category()->getModule()->getDataDir().$person->{People_Model::COLUMN_IMAGE});
         }
         $person->{People_Model::COLUMN_IMAGE} = null;
      }

      if ($form->image->getValues() != null) {
         $file = $form->image->createFileObject();
         $person->{People_Model::COLUMN_IMAGE} = $file->getName();
         unset ($file);
      }
      
      $person->{People_Model::COLUMN_ID_CATEGORY} = $this->category()->getId();
      $person->{People_Model::COLUMN_NAME} = $form->name->getValues();
      $person->{People_Model::COLUMN_SURNAME} = $form->surname->getValues();
      $person->{People_Model::COLUMN_DEGREE} = $form->degree->getValues();
      $person->{People_Model::COLUMN_DEGREE_AFTER} = $form->degreeAfter->getValues();
      $person->{People_Model::COLUMN_TEXT} = $form->text->getValues();
      $person->{People_Model::COLUMN_TEXT_CLEAR} = strip_tags($form->text->getValues());
      $person->{People_Model::COLUMN_AGE} = $form->age->getValues();
      $person->{People_Model::COLUMN_LABEL} = $form->label->getValues();
      $person->{People_Model::COLUMN_EMAIL} = $form->email->getValues();
      $person->{People_Model::COLUMN_PHONE} = $form->phone->getValues();
      $person->{People_Model::COLUMN_SOCIAL_URL} = $form->socialUrl->getValues();

      
      
      // pokud byla zadáno pořadí, zařadíme na pořadí. Jinak dáme na konec
      $person->save($person);
      
      return $person;
   }


   /**
    * Metoda  vytvoří element formuláře
    * @return Form
    */
   protected function createForm(Model_ORM_Record $person = null) {
      $form = new Form('person_');

      $gbase = $form->addGroup('basic', $this->tr('Informace o osobě'));

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
      
      $iAge = new Form_Element_Text('age', $this->tr('Věk'));
      $iAge->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $form->addElement($iAge, $gbase);
      
      $iEmail = new Form_Element_Text('email', $this->tr('Kontaktní e-mail'));
      $iEmail->addValidation(new Form_Validator_Email());
      $form->addElement($iEmail, $gbase);
      
      $iPhone = new Form_Element_Text('phone', $this->tr('Kontaktní telefon'));
      $iPhone->addValidation(new Form_Validator_Regexp(Form_Validator_Regexp::REGEXP_PHONE_CZSK));
      $form->addElement($iPhone, $gbase);
      
      $iSocilaUrl = new Form_Element_Text('socialUrl', $this->tr('Adresa sociálního profilu'));
      $iSocilaUrl->setSubLabel($this->tr('Url adresa profilu na sociální síti (Facebook, Google+) nebo osobní stránky'));
      $iSocilaUrl->addValidation(new Form_Validator_Url());
      $form->addElement($iSocilaUrl, $gbase);
      
      $iLabel = new Form_Element_Text('label', $this->tr('Funkce'));
      $iLabel->setSubLabel($this->tr('Zařazení, přezdívka, krátký popis a podobně'));
      $iLabel->addFilter(new Form_Filter_StripTags());
      $form->addElement($iLabel, $gbase);

      $iText = new Form_Element_TextArea('text', $this->tr('Popis'));
      $iText->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iText, $gbase);

      
//      $iOrder = new Form_Element_Text('order', $this->tr('Pořadí'));
//      $iOrder->setSubLabel($this->tr('Určuje pořadí osoby v seznamu'));
//      $iOrder->addValidation(new Form_Validator_IsNumber());
//      $form->addElement($iOrder, $gothr);
//      
      $iImage = new Form_Element_File('image', $this->tr('Portrét'));
      $iImage->addValidation(new Form_Validator_FileExtension('jpg;png'));
      $iImage->setUploadDir($this->module()->getDataDir());
      $iImage->setOverWrite(false);
      $form->addElement($iImage, $gbase);

      $iSubmit = new Form_Element_SaveCancel('save');
      $form->addElement($iSubmit, $gbase);

      if($person){
         // element pro odstranění obrázku
         if($person->{People_Model::COLUMN_IMAGE} != null){
            $elemRemImg = new Form_Element_Checkbox('imgdel', $this->tr('Odstranit uložený portrét'));
            $elemRemImg->setSubLabel($this->tr('Uložen portrét').': '.$person->{People_Model::COLUMN_IMAGE});
            $form->addElement($elemRemImg, 'basic');
         }
         $form->name->setValues($person->{People_Model::COLUMN_NAME});
         $form->surname->setValues($person->{People_Model::COLUMN_SURNAME});
         $form->degree->setValues($person->{People_Model::COLUMN_DEGREE});
         $form->degreeAfter->setValues($person->{People_Model::COLUMN_DEGREE_AFTER});
         $form->text->setValues($person->{People_Model::COLUMN_TEXT});
         $form->age->setValues($person->{People_Model::COLUMN_AGE});
         $form->label->setValues($person->{People_Model::COLUMN_LABEL});
         $form->email->setValues($person->{People_Model::COLUMN_EMAIL});
         $form->phone->setValues($person->{People_Model::COLUMN_PHONE});
         $form->socialUrl->setValues($person->{People_Model::COLUMN_SOCIAL_URL});
      }
      
      if($form->isSend() && $form->save->getValues() == false){
         $this->link()->route()->reload();
      }
      
      return $form;
   }

   public function editOrderController()
   {
      $this->checkWritebleRights();
      
      $model = new People_Model();
      $people = $model->where(People_Model::COLUMN_ID_CATEGORY.' = :idc', array('idc' => $this->category()->getId()))
         ->order(array(
            People_Model::COLUMN_ORDER => Model_ORM::ORDER_ASC,
            People_Model::COLUMN_SURNAME => Model_ORM::ORDER_ASC,
            People_Model::COLUMN_NAME => Model_ORM::ORDER_ASC,
         ))->records();

      $form = new Form('person_order_');
      
      $eId = new Form_Element_Hidden('id');
      $eId->setDimensional();
      
      $form->addElement($eId);
      
      $eSave = new Form_Element_SaveCancel('save');
      $form->addElement($eSave);

      if($form->isSend() && $form->save->getValues() == false){
         $this->link()->route()->reload();
      }
      
      if($form->isValid()){
         $ids = $form->id->getValues();
         
         $stmt = $model->query("UPDATE {THIS} SET `".People_Model::COLUMN_ORDER."` = :ord WHERE ".People_Model::COLUMN_ID." = :id");
         foreach ($ids as $index => $id) {
            $stmt->bindValue('id', $id);
            $stmt->bindValue('ord', $index+1);
            $stmt->execute();
         }
         
         $this->infoMsg()->addMessage($this->tr('Pořadí bylo uloženo'));
         $this->link()->route()->reload();
      }
      
      $this->view()->people = $people;
      $this->view()->form = $form;
   }


   /**
    * Smazání článků při odstranění kategorie
    * @param Category $category
    */
   public static function clearOnRemove(Category $category) {
      $model = new People_Model();
      $model->where(People_Model::COLUMN_ID_CATEGORY. " = :idc", array('idc' => $category->getId()))->delete();
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
      } else {
         $form->cropimg->setValues($this->category()->getGlobalParam('cropimg', self::DEFAULT_IMAGE_CROP));
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