<?php

class Lecturers_Controller extends Controller {
   const DEFAULT_IMAGE_WIDTH = 90;
   const DEFAULT_IMAGE_HEIGHT = 120;
   const DEFAULT_IMAGE_CROP = false;
   const DEFAULT_RECORDS_ON_PAGE = 10;

   const DATA_DIR = 'lecturers';

   protected function init()
   {
      parent::init();
      $this->module()->setDataDir(self::DATA_DIR);
   }

   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      $model = new Lecturers_Model();
      $model->where(Lecturers_Model::COLUMN_DELETED.' = 0', array())->order(array(
          Lecturers_Model::COLUMN_SURNAME => Model_ORM::ORDER_ASC,
          Lecturers_Model::COLUMN_NAME => Model_ORM::ORDER_ASC,
      ));

      if($this->getRequestParam('sid', null) != null){
         $all = $model->records();
         $page = 1;
         $counter = 1;
         foreach($all as $l) {
            if($counter > $this->category()->getParam('recordsonpage', self::DEFAULT_RECORDS_ON_PAGE)){
               $counter = 1;
               $page++;
            }
            if($l->{Lecturers_Model::COLUMN_ID} == $this->getRequestParam('sid')){
               $this->link()->param(Component_Scroll::GET_PARAM, $page)->anchor('lecturer-'.$l->{Lecturers_Model::COLUMN_ID})
               ->rmParam('sid')->reload();
            }
            $counter++;
         }


      }

      if($this->category()->getRights()->isWritable()){
         $formDel = new Form('lecture_del_');

         $elemId = new Form_Element_Hidden('id');
         $formDel->addElement($elemId);

         $elemSubmit = new Form_Element_Submit('delete', $this->_('Smazat'));
         $formDel->addElement($elemSubmit);

         if($formDel->isValid()){
            $model->delete($formDel->id->getValues());

            $this->infoMsg()->addMessage($this->_('Lektor byl smazán'));
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
      $this->view()->lecturers = $model->records();
   }

   /**
    * Kontroler pro přidání novinky
    */
   public function addController() {
      $this->checkWritebleRights();
      $addForm = $this->createForm();

      if ($addForm->isValid()) {
         $imgName = null;
         if ($addForm->image->getValues() != null) {
            $image = new File_Image($addForm->image);
            $image->getData()->resize(
               $this->category()->getParam('imgw', self::DEFAULT_IMAGE_WIDTH),
               $this->category()->getParam('imgh', self::DEFAULT_IMAGE_HEIGHT),
               $this->category()->getParam('cropimg', self::DEFAULT_IMAGE_CROP) ? File_Image_Base::RESIZE_CROP : File_Image_Base::RESIZE_AUTO
            )->save();
            $image->move($this->module()->getDataDir());
            $imgName = $image->getName();
         }

         $model = new Lecturers_Model();
         $model->saveLecturer($addForm->name->getValues(),
                         $addForm->surname->getValues(),
                         $addForm->degree->getValues(),
                         $addForm->degreeAfter->getValues(),
                         $addForm->text->getValues(),
                         $imgName);

         $this->infoMsg()->addMessage($this->_('Lektor byl uložen'));
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
      $model = new Lecturers_Model();
      $lecturer = $model->getLecturer($this->getRequest('id'));
      if($lecturer == false) return false;

      $editForm = $this->createForm();

      // element pro odstranění obrázku
      if($lecturer->{Lecturers_Model::COLUMN_IMAGE} != null){
         $elemRemImg = new Form_Element_Checkbox('imgdel', $this->_('Odstranit uložený portrét'));
         $elemRemImg->setSubLabel($this->_('Uložen portrét').': '.$lecturer->{Lecturers_Model::COLUMN_IMAGE});
         $editForm->addElement($elemRemImg, 'image');
      }


      $editForm->name->setValues($lecturer->{Lecturers_Model::COLUMN_NAME});
      $editForm->surname->setValues($lecturer->{Lecturers_Model::COLUMN_SURNAME});
      $editForm->degree->setValues($lecturer->{Lecturers_Model::COLUMN_DEGREE});
      $editForm->degreeAfter->setValues($lecturer->{Lecturers_Model::COLUMN_DEGREE_AFTER});
      $editForm->text->setValues($lecturer->{Lecturers_Model::COLUMN_TEXT});

      if ($editForm->isValid()) {
         $imgName = $lecturer->{Lecturers_Model::COLUMN_IMAGE};
         $oldFile = new File($imgName, $this->module()->getDataDir());
         if( ($editForm->image->getValues() != null OR ($editForm->haveElement('imgdel') AND $editForm->imgdel->getValues() == true) )
            AND $oldFile->exist()){
            $oldFile->delete();
            $imgName = null;
         }

         if ($editForm->image->getValues() != null) {
            $image = new File_Image($editForm->image);
            $image->getData()->resize(
               $this->category()->getParam('imgw', self::DEFAULT_IMAGE_WIDTH),
               $this->category()->getParam('imgh', self::DEFAULT_IMAGE_HEIGHT),
               $this->category()->getParam('cropimg', self::DEFAULT_IMAGE_CROP) ? File_Image_Base::RESIZE_CROP : File_Image_Base::RESIZE_AUTO
            )->save();
            $image->move($this->module()->getDataDir());
            $imgName = $image->getName();
         }

         $model->saveLecturer($editForm->name->getValues(),
                         $editForm->surname->getValues(),
                         $editForm->degree->getValues(),
                         $editForm->degreeAfter->getValues(),
                         $editForm->text->getValues(),
                         $imgName, $lecturer->{Lecturers_Model::COLUMN_ID});

         $this->infoMsg()->addMessage($this->_('Uloženo'));
         $this->link()->route()->reload();
      }
      $this->view()->form = $editForm;
      $this->view()->lecturer = $lecturer;
   }

   /**
    * Metoda  vytvoří element formuláře
    * @return Form
    */
   protected function createForm() {
      $form = new Form('lecturer_', true);

      $form->addGroup('basic', $this->_('Základní informace o lektorovi'));
      $form->addGroup('image', $this->_('Obrázek'));

      $iName = new Form_Element_Text('name', $this->_('Jméno'));
      $iName->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iName, 'basic');

      $iSurName = new Form_Element_Text('surname', $this->_('Přijmení'));
      $iSurName->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iSurName, 'basic');

      $iDegree = new Form_Element_Text('degree', $this->_('Titul'));
      $form->addElement($iDegree, 'basic');

      $iDegreeA = new Form_Element_Text('degreeAfter', $this->_('Titul za jménem'));
      $form->addElement($iDegreeA, 'basic');

      $iText = new Form_Element_TextArea('text', $this->_('Popis'));
      $iText->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iText, 'basic');

      $iImage = new Form_Element_File('image', $this->_('Portrét'));
      $iImage->addValidation(new Form_Validator_FileExtension('jpg;png'));
//      $iImage->setUploadDir($this->module()->getDataDir());
      $form->addElement($iImage, 'image');

      $iSubmit = new Form_Element_SaveCancel('save');
      $form->addElement($iSubmit);

      return $form;
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
   public static function settingsController(&$settings, Form &$form) {
      $eOnPage = new Form_Element_Text('numOnPage', 'Počet lektorů na stránku');
      $eOnPage->addValidation(new Form_Validator_IsNumber());
      $eOnPage->setSubLabel(sprintf('Výchozí: %s lektorů na stránku', self::DEFAULT_RECORDS_ON_PAGE));
      $form->addElement($eOnPage, 'view');


      $form->addGroup('images', 'Nasatvení obrázků');

      $elemImgW = new Form_Element_Text('imgw', 'Šířka portrétu');
      $elemImgW->setSubLabel('Výchozí: ' . self::DEFAULT_IMAGE_WIDTH . ' px');
      $elemImgW->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemImgW, 'images');

      $elemImgH = new Form_Element_Text('imgh', 'Výška portrétu');
      $elemImgH->setSubLabel('Výchozí: ' . self::DEFAULT_IMAGE_HEIGHT . ' px');
      $elemImgH->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemImgH, 'images');

      $elemCropImage = new Form_Element_Checkbox('cropimg', 'Ořezávat portréty');
      $form->addElement($elemCropImage, 'images');

      if (isset($settings['imgw'])) {
         $form->imgw->setValues($settings['imgw']);
      }
      if (isset($settings['imgh'])) {
         $form->imgh->setValues($settings['imgh']);
      }
      if (isset($settings['cropimg'])) {
         $form->imgh->setValues($settings['cropimg']);
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