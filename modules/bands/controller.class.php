<?php
class Bands_Controller extends Controller {
   const DEFAULT_BANDS_IN_PAGE = 5;

   const DEFAULT_IMAGE_WIDTH = 300;
   const DEFAULT_IMAGE_HEIGHT = 200;
   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController() {
       //		Kontrola práv
      $this->checkReadableRights();
      $model = new Bands_Model();

      $scrollComponent = new Component_Scroll();
      $scrollComponent->setConfig(Component_Scroll::CONFIG_CNT_ALL_RECORDS, $model->getCount(!$this->rights()->isWritable()));

      $scrollComponent->setConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE,
              $this->category()->getParam('scroll', self::DEFAULT_BANDS_IN_PAGE));

      $bands = $model->getList($scrollComponent->getStartRecord(),$scrollComponent->getRecordsOnPage(),
              !$this->rights()->isWritable());
      $this->view()->bands = $bands;
      $this->view()->scrollComp = $scrollComponent;
   }

   public function contentController(){
      //		Kontrola práv
      $this->checkReadableRights();

      // načtení článků
      $artModel = new Articles_Model_List();
      $articles = $artModel->getList($this->category()->getId(),0,100,!$this->rights()->isWritable());
      $this->view()->articles = $articles;
   }

   public function showController() {
      $this->checkReadableRights();

      $model = new Bands_Model();
      $band = $model->getBand($this->getRequest('urlkey'));
      if($band == false) return false;
      $this->view()->band = $band;
      $model->addShowCount($band->{Bands_Model::COLUMN_ID});
      if($this->category()->getRights()->isWritable()
              OR $this->category()->getRights()->isControll()) {
         $deleteForm = new Form('band_');
         $feId = new Form_Element_Hidden('id');
         $feId->addValidation(new Form_Validator_IsNumber());
         $deleteForm->addElement($feId);
         $feSubmit = new Form_Element_Submit('delete');
         $deleteForm->addElement($feSubmit);

         if($deleteForm->isValid()) {
            $model->deleteBand($deleteForm->id->getValues());
            $this->link()->route()->rmParam()->reload();
         }
      }
      // komponenta pro vypsání odkazů na sdílení
      $shares = new Component_Share();
      $shares->setConfig('url', (string)$this->link()->rmParam());
      $shares->setConfig('title', $band->{Bands_Model::COLUMN_NAME});

      $this->view()->shares=$shares;
      // odkaz zpět
      $this->view()->linkBack = $this->link()->route();
   }

   /**
    * Kontroler pro přidání novinky
    */
   public function addController() {
      $this->checkWritebleRights();
      $addForm = $this->createForm();
      // obrázek musí být vložen
      $addForm->image->addValidation(New Form_Validator_NotEmpty());

      if($addForm->isValid()) {
         $image = $addForm->image->createFileObject('Filesystem_File_Image');
         $image->resampleImage($this->category()->getParam('imgw', self::DEFAULT_IMAGE_WIDTH),
                 $this->category()->getParam('imgh', self::DEFAULT_IMAGE_HEIGHT), true);
         $image->save();
         $model = new Bands_Model();
         $lastId = $model->saveBand($addForm->name->getValues(), $addForm->text->getValues(),
                 $addForm->urlkey->getValues(), $image->getName(), $addForm->public->getValues());
         $band = $model->getBandById($lastId);
         $this->infoMsg()->addMessage($this->_('Skupina byla uložena'));
         $this->link()->route('detail',array('urlkey' => $band->{Bands_Model::COLUMN_URLKEY}))->reload();
      }
      $this->view()->form = $addForm;
   }

   /**
    * controller pro úpravu novinky
    */
   public function editController() {
      $this->checkWritebleRights();

      $editForm = $this->createForm();
      // doplnění id
      $iIdElem = new Form_Element_Hidden('band_id');
      $iIdElem->addValidation(new Form_Validator_IsNumber());
      $editForm->addElement($iIdElem);

      // načtení dat
      $model = new Bands_Model();
      $band = $model->getBand($this->getRequest('urlkey'));

      $editForm->name->setValues($band->{Bands_Model::COLUMN_NAME});
      $editForm->text->setValues($band->{Bands_Model::COLUMN_TEXT});
      $editForm->urlkey->setValues($band->{Bands_Model::COLUMN_URLKEY});
      $editForm->band_id->setValues($band->{Bands_Model::COLUMN_ID});
      $editForm->public->setValues($band->{Bands_Model::COLUMN_PUBLIC});

      if($editForm->isValid()) {
         $newImg = null;
         if($editForm->image->getValues() != null){
            // smaže se původní
            $oldImg = new Filesystem_File($band->{Bands_Model::COLUMN_IMAGE}, $this->category()->getModule()->getDataDir());
            $oldImg->remove();
            $image = $editForm->image->createFileObject('Filesystem_File_Image');
            $image->resampleImage($this->category()->getParam('imgw', self::DEFAULT_IMAGE_WIDTH),
                 $this->category()->getParam('imgh', self::DEFAULT_IMAGE_HEIGHT), true);
            $image->save();
            $newImg = $image->getName();
         }

         $model->saveBand($editForm->name->getValues(), $editForm->text->getValues(),
                 $editForm->urlkey->getValues(), $newImg,
                 $editForm->public->getValues(), $band->{Bands_Model::COLUMN_ID});
         $this->infoMsg()->addMessage($this->_('Uloženo'));
         // nahrání nové verze článku (kvůli url klíči)
         $band = $model->getBandById($band->{Bands_Model::COLUMN_ID});
         $this->link()->route('detail',array('urlkey' => $band->{Bands_Model::COLUMN_URLKEY}))->reload();
      }
      $this->view()->form = $editForm;
      $this->view()->band= $band;
   }

   /**
    * Metoda  vytvoří element formuláře
    * @return Form
    */
   protected function createForm() {
      $form = new Form('band_');

      $iName = new Form_Element_Text('name', $this->_('Název'));
      $iName->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iName);

      $iText = new Form_Element_TextArea('text', $this->_('Popis'));
      $iText->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iText);

      $iImage = new Form_Element_File('image', $this->_('Titulní obrázek'));
      $iImage->addValidation(new Form_Validator_FileExtension('jpg;png'));
      $iImage->setUploadDir($this->module()->getDataDir());
      $form->addElement($iImage);

      $iUrlKey = new Form_Element_Text('urlkey', $this->_('Url klíč'));
      $iUrlKey->setSubLabel($this->_('Pokud není klíč zadán, je generován automaticky'));
      $form->addElement($iUrlKey);

      $iPub = new Form_Element_Checkbox('public', $this->_('Veřejný'));
      $iPub->setSubLabel($this->_('Veřejný - viditelný všem návštěvníkům'));
      $iPub->setValues(true);
      $form->addElement($iPub);

      $iSubmit = new Form_Element_Submit('save', $this->_('Uložit'));
      $form->addElement($iSubmit);

      return $form;
   }

   /**
    * Smazání článků při odstranění kategorie
    * @param Category $category
    */
   public static function clearOnRemove(Category $category) {
      $model = new Articles_Model_Detail();
      $model->deleteArticleByCat($category->getId());
   }

   /**
    * Export detailu
    */
   public function exportBandController() {
      $this->checkReadableRights();
      $model = new Bands_Model();
      $band = $model->getBand($this->getRequest('urlkey'));
      if($band == false) return false;
      $this->view()->band = $band;
   }

   /**
    * Metoda pro nastavení modulu
    */
   public static function settingsController(&$settings,Form &$form) {
      $form->addGroup('basic', 'Základní nasatvení');

      $elemScroll = new Form_Element_Text('scroll', 'Počet skupin na stránku');
      $elemScroll->setSubLabel('Výchozí: '.self::DEFAULT_BANDS_IN_PAGE.' skupin');
      $elemScroll->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemScroll,'basic');

      $form->addGroup('images', 'Nasatvení obrázků');

      $elemImgW = new Form_Element_Text('imgw', 'Šířka titulního obrázku');
      $elemImgW->setSubLabel('Výchozí: '.self::DEFAULT_IMAGE_WIDTH.' px');
      $elemImgW->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemImgW,'images');

      $elemImgH = new Form_Element_Text('imgh', 'Výška titulního obrázku');
      $elemImgH->setSubLabel('Výchozí: '.self::DEFAULT_IMAGE_HEIGHT.' px');
      $elemImgH->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemImgH,'images');

      if(isset($settings['scroll'])) {
         $form->scroll->setValues($settings['scroll']);
      }
      if(isset($settings['imgw'])) {
         $form->imgw->setValues($settings['imgw']);
      }
      if(isset($settings['imgh'])) {
         $form->imgh->setValues($settings['imgh']);
      }
      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings['scroll'] = $form->scroll->getValues();
         $settings['imgw'] = $form->imgw->getValues();
         $settings['imgh'] = $form->imgh->getValues();
      }
   }
}
?>