<?php

class People_Controller extends Controller {
   const DEFAULT_IMAGE_WIDTH = 90;
   const DEFAULT_IMAGE_HEIGHT = 120;
   const DEFAULT_IMAGE_CROP = false;
   const DEFAULT_RECORDS_ON_PAGE = 10;

   const DATA_DIR = 'people';


   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      $model = new People_Model();
      $model->where(People_Model::COLUMN_DELETED.' = 0 AND '.People_Model::COLUMN_ID_CATEGORY.' = :idc', array('idc' => $this->category()->getId()))
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
               $this->link()->param(Component_Scroll::GET_PARAM, $page)->anchor('lecturer-'.$person->{People_Model::COLUMN_ID})
               ->rmParam('sid')->reload();
            }
            $counter++;
         }


      }

      if($this->category()->getRights()->isWritable()){
         $formDel = new Form('person_del_');

         $elemId = new Form_Element_Hidden('id');
         $formDel->addElement($elemId);

         $elemSubmit = new Form_Element_Submit('delete', $this->_('Smazat'));
         $formDel->addElement($elemSubmit);

         if($formDel->isValid()){
            $model->delete($formDel->id->getValues());

            $this->infoMsg()->addMessage($this->_('Osoba byla smazána'));
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
         $record->{People_Model::COLUMN_ID_CATEGORY} = $this->category()->getId();
         
         if ($addForm->image->getValues() != null) {
            $image = $addForm->image->createFileObject('Filesystem_File_Image');
            $image->resampleImage($this->category()->getParam('imgw', self::DEFAULT_IMAGE_WIDTH),
                    $this->category()->getParam('imgh', self::DEFAULT_IMAGE_HEIGHT),
                    $this->category()->getParam('cropimg', self::DEFAULT_IMAGE_CROP));
            $image->save();
            $record->{People_Model::COLUMN_IMAGE} = $image->getName();
         }
         
         $record->{People_Model::COLUMN_NAME} = $addForm->name->getValues();
         $record->{People_Model::COLUMN_SURNAME} = $addForm->surname->getValues();
         $record->{People_Model::COLUMN_DEGREE} = $addForm->degree->getValues();
         $record->{People_Model::COLUMN_DEGREE_AFTER} = $addForm->degreeAfter->getValues();
         $record->{People_Model::COLUMN_TEXT} = $addForm->text->getValues();
         $record->{People_Model::COLUMN_TEXT_CLEAR} = strip_tags($addForm->text->getValues());
         
         // pokud byla zadáno pořadí, zařadíme na pořadí. Jinak dáme na konec
         if($addForm->order->getValues() != null){ 
            $record->{People_Model::COLUMN_ORDER} = $addForm->order->getValues();
         } else {
            $c = $model->columns(array('m' => 'MAX(`'.People_Model::COLUMN_ORDER.'`)'))
               ->where(People_Model::COLUMN_DELETED.' = 0 AND ' . People_Model::COLUMN_ID_CATEGORY.' = :idc', 
                  array('idc' => $this->category()->getId()))->record()->m;
            
            $record->{People_Model::COLUMN_ORDER} = $c + 1;
         }
         
         $model->save($record);
         
         $this->infoMsg()->addMessage($this->_('Osoba byla uložena'));
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
      if($person == false) return false;

      $editForm = $this->createForm();

      // element pro odstranění obrázku
      if($person->{People_Model::COLUMN_IMAGE} != null){
         $elemRemImg = new Form_Element_Checkbox('imgdel', $this->_('Odstranit uložený portrét'));
         $elemRemImg->setSubLabel($this->_('Uložen portrét').': '.$person->{People_Model::COLUMN_IMAGE});
         $editForm->addElement($elemRemImg, 'others');
      }


      $editForm->name->setValues($person->{People_Model::COLUMN_NAME});
      $editForm->surname->setValues($person->{People_Model::COLUMN_SURNAME});
      $editForm->degree->setValues($person->{People_Model::COLUMN_DEGREE});
      $editForm->degreeAfter->setValues($person->{People_Model::COLUMN_DEGREE_AFTER});
      $editForm->text->setValues($person->{People_Model::COLUMN_TEXT});
      $editForm->order->setValues($person->{People_Model::COLUMN_ORDER});

      if ($editForm->isValid()) {
         if ($editForm->image->getValues() != null OR ($editForm->haveElement('imgdel') AND $editForm->imgdel->getValues() == true)) {
            // smaže se původní
            if(is_file($this->category()->getModule()->getDataDir().$person->{People_Model::COLUMN_IMAGE})){
               /* if upload file with same name it's overwrited and then deleted. This make error!!! */
//               @unlink($this->category()->getModule()->getDataDir().$person->{People_Model::COLUMN_IMAGE});
            }
            $person->{People_Model::COLUMN_IMAGE} = null;
         }

         if ($editForm->image->getValues() != null) {
            $image = $editForm->image->createFileObject('Filesystem_File_Image');
            $image->resampleImage($this->category()->getParam('imgw', self::DEFAULT_IMAGE_WIDTH),
                    $this->category()->getParam('imgh', self::DEFAULT_IMAGE_HEIGHT),
                    $this->category()->getParam('cropimg', self::DEFAULT_IMAGE_CROP));
            $image->save();
            $person->{People_Model::COLUMN_IMAGE} = $image->getName();
            unset ($image);
         }

         $person->{People_Model::COLUMN_NAME} = $editForm->name->getValues();
         $person->{People_Model::COLUMN_SURNAME} = $editForm->surname->getValues();
         $person->{People_Model::COLUMN_DEGREE} = $editForm->degree->getValues();
         $person->{People_Model::COLUMN_DEGREE_AFTER} = $editForm->degreeAfter->getValues();
         $person->{People_Model::COLUMN_TEXT} = $editForm->text->getValues();
         $person->{People_Model::COLUMN_TEXT_CLEAR} = strip_tags($editForm->text->getValues());
         
         // pokud byla zadáno pořadí, zařadíme na pořadí. Jinak dáme na konec
         if($editForm->order->getValues() != null){ 
            $person->{People_Model::COLUMN_ORDER} = $editForm->order->getValues();
         } else {
            $c = $model->columns(array('m' => 'MAX(`'.People_Model::COLUMN_ORDER.'`)'))
               ->where(People_Model::COLUMN_DELETED.' = 0 AND ' . People_Model::COLUMN_ID_CATEGORY.' = :idc', 
                  array('idc' => $this->category()->getId()))->record()->m;
            
            $person->{People_Model::COLUMN_ORDER} = $c + 1;
         }
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
   protected function createForm() {
      $form = new Form('person_');

      $gbase = $form->addGroup('basic', $this->_('Základní informace o osobě'));
      $gothr = $form->addGroup('others', $this->_('ostatní'));

      $iName = new Form_Element_Text('name', $this->_('Jméno'));
      $iName->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iName, $gbase);

      $iSurName = new Form_Element_Text('surname', $this->_('Přijmení'));
      $iSurName->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iSurName, $gbase);

      $iDegree = new Form_Element_Text('degree', $this->_('Titul'));
      $form->addElement($iDegree, $gbase);

      $iDegreeA = new Form_Element_Text('degreeAfter', $this->_('Titul za jménem'));
      $form->addElement($iDegreeA, $gbase);

      $iText = new Form_Element_TextArea('text', $this->_('Popis'));
      $iText->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iText, $gbase);

      $iOrder = new Form_Element_Text('order', $this->_('Pořadí'));
      $iOrder->setSubLabel($this->tr('Určuje pořadí osoby v seznamu'));
      $iOrder->addValidation(new Form_Validator_IsNumber());
      $form->addElement($iOrder, $gothr);
      
      $iImage = new Form_Element_File('image', $this->_('Portrét'));
      $iImage->addValidation(new Form_Validator_FileExtension('jpg;png'));
      $iImage->setUploadDir($this->module()->getDataDir());
      $form->addElement($iImage, $gothr);

      $iSubmit = new Form_Element_SaveCancel('save');
      $form->addElement($iSubmit);

      if($form->isSend() && $form->save->getValues() == false){
         $this->link()->route()->reload();
      }
      
      return $form;
   }

   public function editOrderController()
   {
      $this->checkWritebleRights();
      
      $model = new People_Model();
      $people = $model->where(People_Model::COLUMN_DELETED.' = 0 AND '.People_Model::COLUMN_ID_CATEGORY.' = :idc', array('idc' => $this->category()->getId()))
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