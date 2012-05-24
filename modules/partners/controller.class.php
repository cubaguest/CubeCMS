<?php

class Partners_Controller extends Controller {
   const DATA_DIR = 'partners';

   protected function init()
   {
      $this->module()->setDataDir(self::DATA_DIR);
      parent::init();
   }

   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      $model = new Partners_Model();
      $whereValues = array('idc' => $this->category()->getId());
      $where = Partners_Model::COLUMN_ID_CATEGORY." = :idc";
      if(!$this->category()->getRights()->isWritable() && !$this->category()->getRights()->isControll()){
         $where .= " AND ".Partners_Model::COLUMN_DISABLED." = 0";
      }

      if($this->category()->getRights()->isWritable()){
         $formDel = new Form('partner_del_');
         $elemId = new Form_Element_Hidden('id');
         $formDel->addElement($elemId);
         $elemSubmit = new Form_Element_Submit('delete', $this->tr('Smazat'));
         $formDel->addElement($elemSubmit);
         if($formDel->isValid()){
            $model->delete($formDel->id->getValues());
            $this->infoMsg()->addMessage($this->tr('Partner byl smazán'));
            $this->link()->rmParam()->reload();
         }
         $this->view()->formDelete = $formDel;
         
         $formVis = new Form('partner_visibility_');
         $elemId = new Form_Element_Hidden('id');
         $formVis->addElement($elemId);
         $elemSubmit = new Form_Element_Submit('change', $this->tr('Změnit viditelnost'));
         $formVis->addElement($elemSubmit);
         if($formVis->isValid()){
            $partner = $model->record($formVis->id->getValues());
            $partner->{Partners_Model::COLUMN_DISABLED} = !$partner->{Partners_Model::COLUMN_DISABLED};
            $model->save($partner);
            $this->infoMsg()->addMessage($this->tr('Viditelnost partnera byla změněna'));
            $this->link()->rmParam()->reload();
         }
         $this->view()->formVisibility = $formVis;
      }

      $this->view()->partners = $model
         ->order(array(Partners_Model::COLUMN_ORDER => Model_ORM::ORDER_ASC, Partners_Model::COLUMN_NAME => Model_ORM::ORDER_ASC))
         ->where($where, $whereValues)
         ->records();
   }

   /**
    * Kontroler pro přidání novinky
    */
   public function addController() {
      $this->checkWritebleRights();
      $form = $this->createForm();

      if ($form->isValid()) {
         $model = new Partners_Model();
         $record = $model->newRecord();
         $record->{Partners_Model::COLUMN_ID_CATEGORY} = $this->category()->getId();
         
         if ($form->image->getValues() != null) {
            $image = $form->image->createFileObject('File_Image');
            $record->{Partners_Model::COLUMN_IMAGE} = $image->getName();
         }
         
         $record->{Partners_Model::COLUMN_NAME} = $form->name->getValues();
         $record->{Partners_Model::COLUMN_TEXT} = $form->text->getValues();
         $record->{Partners_Model::COLUMN_URL} = $form->url->getValues();
         $record->{Partners_Model::COLUMN_DISABLED} = $form->disabled->getValues();
         
         $c = $model->columns(array('m' => 'MAX(`'.Partners_Model::COLUMN_ORDER.'`)'))
               ->where(Partners_Model::COLUMN_ID_CATEGORY.' = :idc', 
                  array('idc' => $this->category()->getId()))->record()->m;
            
            $record->{Partners_Model::COLUMN_ORDER} = $c + 1;
         
         $model->save($record);
         
         $this->infoMsg()->addMessage($this->tr('Partner byl uložen'));
         $this->link()->route()->reload();
      }
      $this->view()->form = $form;
   }

   /**
    * controller pro úpravu novinky
    */
   public function editController() {
      $this->checkWritebleRights();

      // načtení dat
      $model = new Partners_Model();
      $partner = $model->record($this->getRequest('id'));
      if($partner == false) return false;

      $form = $this->createForm($partner);

      if ($form->isValid()) {
         if ($form->image->getValues() != null OR ($form->haveElement('imgdel') AND $form->imgdel->getValues() == true)) {
            // smaže se původní
//            if(is_file($this->category()->getModule()->getDataDir().$partner->{Partners_Model::COLUMN_IMAGE})){
               /* if upload file with same name it's overwrited and then deleted. This make error!!! */
//               @unlink($this->category()->getModule()->getDataDir().$person->{People_Model::COLUMN_IMAGE});
//            }
            $partner->{Partners_Model::COLUMN_IMAGE} = null;
         }

         if ($form->image->getValues() != null) {
            $image = $form->image->createFileObject('File_Image');
            $partner->{Partners_Model::COLUMN_IMAGE} = $image->getName();
         }

         $partner->{Partners_Model::COLUMN_NAME} = $form->name->getValues();
         $partner->{Partners_Model::COLUMN_TEXT} = $form->text->getValues();
         $partner->{Partners_Model::COLUMN_URL} = $form->url->getValues();
         $partner->{Partners_Model::COLUMN_DISABLED} = $form->disabled->getValues();
         
         $model->save($partner);

         $this->infoMsg()->addMessage($this->tr('Partner byl uložen'));
         $this->link()->route()->reload();
      }
      $this->view()->form = $form;
      $this->view()->partner = $partner;
   }

   /**
    * Metoda  vytvoří element formuláře
    * @return Form
    */
   protected function createForm(Model_ORM_Record $partner = null) {
      $form = new Form('partner_');

      $iName = new Form_Element_Text('name', $this->tr('Název'));
      $iName->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iName);

      $iText = new Form_Element_TextArea('text', $this->tr('Popis'));
      $form->addElement($iText);

      $iOrder = new Form_Element_Text('url', $this->tr('URL adresa'));
      $iOrder->addValidation(new Form_Validator_Url());
//      $iOrder->addFilter(new Form_Filter_Url());
      $form->addElement($iOrder);
      
      $iImage = new Form_Element_File('image', $this->tr('Obrázek'));
      $iImage->addValidation(new Form_Validator_FileExtension('jpg;png'));
      $iImage->setUploadDir($this->module()->getDataDir());
      $form->addElement($iImage);

      $elemDisabled = new Form_Element_Checkbox('disabled', $this->tr('Vypnout partnera'));
      $form->addElement($elemDisabled);
      
      if($partner instanceof Model_ORM_Record){
         $form->name->setValues($partner->{Partners_Model::COLUMN_NAME});
         $form->text->setValues($partner->{Partners_Model::COLUMN_TEXT});
         $form->url->setValues($partner->{Partners_Model::COLUMN_URL});
         $form->disabled->setValues($partner->{Partners_Model::COLUMN_DISABLED});
         // obrázek
         if($partner->{Partners_Model::COLUMN_IMAGE} != null){
            $elemRemImg = new Form_Element_Checkbox('imgdel', $this->tr('Odstranit uložený obrázek'));
            $elemRemImg->setSubLabel($this->tr('Uložen portrét').': '.$partner->{Partners_Model::COLUMN_IMAGE});
            $form->addElement($elemRemImg, null, 4);
         }
      }
      
      
      
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
      
      $model = new Partners_Model();
      $partners = $model->where(Partners_Model::COLUMN_ID_CATEGORY.' = :idc', array('idc' => $this->category()->getId()))
         ->order(array(
            Partners_Model::COLUMN_ORDER => Model_ORM::ORDER_ASC,
            Partners_Model::COLUMN_NAME => Model_ORM::ORDER_ASC,
         ))->records();

      $form = new Form('partners_order_');
      
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
         foreach ($ids as $index => $id) {
            $model->where(Partners_Model::COLUMN_ID." = :idp", array('idp' => $id))->update(array(Partners_Model::COLUMN_ORDER => $index+1));
         }
         $this->infoMsg()->addMessage($this->tr('Pořadí bylo uloženo'));
         $this->link()->route()->reload();
      }
      
      $this->view()->partners = $partners;
      $this->view()->form = $form;
   }


   /**
    * Smazání článků při odstranění kategorie
    * @param Category $category
    */
   public static function clearOnRemove(Category $category) {
      $model = new Partners_Model();
      $model->where(Partners_Model::COLUMN_ID_CATEGORY." = :idc", array('idc' => $category->getId()))->delete();
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
?>