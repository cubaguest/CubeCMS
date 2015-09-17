<?php
class MapLocations_Controller extends Controller {
   const MAP_TYPE_NORMAL = "ROADMAP";
   const MAP_TYPE_SATELLITE = "SATELLITE";
   const MAP_TYPE_HYBRID = "HYBRID";
   const MAP_TYPE_TERRAIN = "TERRAIN";
   
   const PARAM_MAP_X = "m_x"; 
   const PARAM_MAP_Y = "m_y";
   const PARAM_MAP_TYPE = "m_t";
   const PARAM_MAP_ZOOM = "m_z";
   
   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController() {
      //        Kontrola práv
      $this->checkReadableRights();

      // načtení pozic
      $model = new MapLocations_Model();
      
      $this->view()->locations = $model->where(MapLocations_Model::COLUMN_ID_CATEGORY.' = :idc', 
              array('idc' => $this->category()->getId()))->records();
   }

   /**
    * Kontroler pro zobrazení novinek
    */
   public function listController() {
      //        Kontrola práv
      $this->checkReadableRights();
      
      // načtení pozic
      $model = new MapLocations_Model();
      $this->view()->locations = $model->where(MapLocations_Model::COLUMN_ID_CATEGORY.' = :idc', 
              array('idc' => $this->category()->getId()))->records();
      
      $formDel = new Form('location_del');
      
      $elemId = new Form_Element_Hidden('id');
      $formDel->addElement($elemId);
      
      $elemSend = new Form_Element_Submit('send', $this->tr('Smazat'));
      $formDel->addElement($elemSend);
      
      if($formDel->isValid()){
         /* @TODO možná mazat i obrázky */
         $loc = $model->record($formDel->id->getValues());
         
         $model->delete($formDel->id->getValues());
         $dir = new FS_Dir($this->module()->getDataDir().'location-'.$loc->getPK().DIRECTORY_SEPARATOR);
         if($dir->exist()){
            $dir->delete();
         }
         
         $this->infoMsg()->addMessage($this->tr('Místo bylo smazáno'));
         $this->link()->route('list')->reload();
      }
      
      $this->view()->formDel = $formDel;
   }

   /**
    * Kontroler pro přidání novinky
    */
   public function addController() {
      $this->checkWritebleRights();
      $form = $this->createForm();
      $this->view()->form = $form;
      
      if($form->isSend() AND $form->save->getValues() == false){
         $this->link()->route('list')->reload();
      }

      if($form->isValid()) {
         $model = new MapLocations_Model();
         $rec = $model->newRecord();
         
         $pos = explode(':', $form->position->getValues());
         
         $rec->{MapLocations_Model::COLUMN_ID_CATEGORY} = $this->category()->getId();
         $rec->{MapLocations_Model::COLUMN_COORDINATE_X} = trim($pos[0]);
         $rec->{MapLocations_Model::COLUMN_COORDINATE_Y} = trim($pos[1]);
         $rec->{MapLocations_Model::COLUMN_NAME} = $form->name->getValues();
         $rec->{MapLocations_Model::COLUMN_TEXT} = $form->text->getValues();
         $rec->{MapLocations_Model::COLUMN_TEXT_CLEAR} = strip_tags($form->text->getValues());
         $rec->save();
         
         // image
         if($form->image->getValues() != null){
            $imagesUploaded = $form->image->getValues();
            $imagesPaths = array();
            $dataDir = $this->module()->getDataDir().'location-'.$rec->getPK().DIRECTORY_SEPARATOR;
            
            foreach ($imagesUploaded as $file) {
               $fObj = new File($file);
               $fObj->move($dataDir);
               $imagesPaths[] = $fObj->getName();
            }
            $rec->{MapLocations_Model::COLUMN_IMAGE} = $imagesPaths;
         }
         
         $rec->save();
         
         $this->infoMsg()->addMessage($this->tr('Místo bylo uloženo'));
         $this->link()->route('list')->reload();
      }

      $this->view()->form = $form;
      $this->view()->edit = false;
   }
   /**
    * controller pro úpravu novinky
    */
   public function editController() {
      $this->checkWritebleRights();

      $form = $this->createForm();
      // doplnění id
      $iIdElem = new Form_Element_Hidden('loc_id');
      $iIdElem->addValidation(new Form_Validator_IsNumber());
      $form->addElement($iIdElem,'place');
      
      // načtení dat
      $model = new MapLocations_Model();
      $location = $model->where(MapLocations_Model::COLUMN_ID.' = :id',
            array('id' => $this->getRequest('id')))->record();

      $form->image->setUploadDir($this->module()->getDataDir().'location-'.$location->getPK().DIRECTORY_SEPARATOR);
      
      if($location == false){ return false; }
      
      $form->name->setValues($location->{MapLocations_Model::COLUMN_NAME});
      $form->text->setValues($location->{MapLocations_Model::COLUMN_TEXT});
      $form->image->setValues($location->{MapLocations_Model::COLUMN_IMAGE});
      $form->position->setValues($location->{MapLocations_Model::COLUMN_COORDINATE_X}.':'.$location->{MapLocations_Model::COLUMN_COORDINATE_Y});

      if($form->isSend() AND $form->save->getValues() == false){
         $this->link()->route('list')->reload();
      }

      if($form->isValid()) {
         $pos = explode(':', $form->position->getValues());
         $location->{MapLocations_Model::COLUMN_COORDINATE_X} = $pos[0];
         $location->{MapLocations_Model::COLUMN_COORDINATE_Y} = $pos[1];
         $location->{MapLocations_Model::COLUMN_NAME} = $form->name->getValues();
         $location->{MapLocations_Model::COLUMN_TEXT} = $form->text->getValues();
         $location->{MapLocations_Model::COLUMN_TEXT_CLEAR} = strip_tags($form->text->getValues());

         // image
         if($form->image->getValues() != null){
            $imagesUploaded = $form->image->getValues();
            $imagesPaths = array();
            $dataDir = $this->module()->getDataDir().'location-'.$location->getPK().DIRECTORY_SEPARATOR;
            
            foreach ($imagesUploaded as $file) {
               $fObj = new File($file);
               $fObj->move($dataDir);
               $imagesPaths[] = $fObj->getName();
            }
            $location->{MapLocations_Model::COLUMN_IMAGE} = $imagesPaths;
         }
         
         $location->save();
         
         $this->infoMsg()->addMessage($this->tr('Místo bylo uloženo'));
         $this->link()->route('list')->reload();
      }
      $this->view()->form = $form;
      $this->view()->edit = true;
   }

   public function editTextController() {
      $this->checkControllRights();
      $form = new Form('list_text_');

      $elemText = new Form_Element_TextArea('text', $this->_('Text'));
      $elemText->setLangs();
      $form->addElement($elemText);

      $elemS = new Form_Element_SaveCancel('save');
      $form->addElement($elemS);

      if($form->isSend() AND $form->save->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Úpravy textu byly zrušeny'));
         $this->link()->route()->reload();
      }
      
      if($form->isValid()) {
         $textM = new Text_Model();
         
         $text = $this->loadText();
         $text->{Text_Model::COLUMN_TEXT} = $form->text->getValues(); 
         $text->{Text_Model::COLUMN_TEXT_CLEAR} = Utils_Html::stripTags($form->text->getValues());
         $text->{Text_Model::COLUMN_ID_CATEGORY} = $this->category()->getId(); 
         
         $textM->save($text);

         $this->infoMsg()->addMessage($this->_('Úvodní text byl uložen'));
         $this->link()->route()->reload();
      }

      // načtení textu
      $text = $this->loadText();
      if($text != false) {
         $form->text->setValues($text->{Text_Model_Detail::COLUMN_TEXT});
      }
      $this->view()->form = $form;
   }
   
   protected function loadText() {
      $textM = new Text_Model();
      $text = $textM->where(Text_Model::COLUMN_ID_CATEGORY.' = :idc', array('idc' =>  $this->category()->getId()))->record();
      if($text != false){
         return $text;
      }
      return $textM->newRecord();
   }

   /**
    * Metoda  vytvoří element formuláře
    * @return Form
    */
   protected function createForm() {
      $form = new Form('location_');
      $fGrpBase = $form->addGroup('place', $this->tr('Informace o místu'));

      $iName = new Form_Element_Text('name', $this->tr('Název'));
      $iName->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iName, $fGrpBase);

      $iText = new Form_Element_TextArea('text', $this->tr('Text'));
      $iText->setSubLabel($this->tr('Pokud mají být vloženy obrázky do textu, vložte na jejich místo řetězec <em>[images]</em>'));
      $form->addElement($iText, $fGrpBase);

//      $eImage = new Form_Element_File('image', $this->tr('Obrázek'));
      $eImage = new Form_Element_ImagesUploader('image', $this->tr('Obrázky'));
      $eImage->setImagesKey('mapplaces');
//      $eImage->addValidation(new Form_Validator_FileExtension('jpg'));
//      $eImage->setUploadDir($this->category()->getModule()->getDataDir());
      $form->addElement($eImage, $fGrpBase );

      $iaddress = new Form_Element_Text('address', $this->tr('Adresa'));
      $iaddress->setSubLabel($this->tr('Slouží pouze pro vyhledání souřadnic.'));
      $form->addElement($iaddress, $fGrpBase);
      
      $elemMapPos = new Form_Element_Text('position', $this->tr('Souřadnice'));
      $elemMapPos->setSubLabel($this->tr('Souřadnice místa ve formátu X:Y. Pokud souřadnice nebudou zadány, nelze položku umístit na mapu.'));
      $elemMapPos->addValidation(new Form_Validator_Regexp('/^[0-9.]+:[0-9.]+$/'));
      $form->addElement($elemMapPos, $fGrpBase);

      $iSubmit = new Form_Element_SaveCancel('save');
      $form->addElement($iSubmit, $fGrpBase);

      return $form;
   }

   /**
    * Smazání článků při odstranění kategorie
    * @param Category $category
    */
   public static function clearOnRemove(Category $category) {
      $model = new MapLocations_Model();
      $model->where(MapLocations_Model::COLUMN_ID_CATEGORY, $category->getId())->delete();
   }

   /**
    * Metoda pro nastavení modulu
    */
   protected function settings(&$settings,Form &$form) {
      $fGrpMap = $form->addGroup('mapsettings', $this->tr('Nastavení mapy'));

      $elemMapPos = new Form_Element_Text('map_pos', $this->tr('Střed mapy'));
      $elemMapPos->setSubLabel($this->tr('Souřadnice mapy ve formátu X:Y'));
      $elemMapPos->addValidation(new Form_Validator_Regexp('/^[0-9.]+:[0-9.]+$/'));
      $elemMapPos->setValues('49.823809:15.46875');
      $form->addElement($elemMapPos, $fGrpMap);

      if(isset($settings[self::PARAM_MAP_X])) {
         $form->map_pos->setValues($settings[self::PARAM_MAP_Y].':'.$settings[self::PARAM_MAP_X]);
      }
      
      // řazení
      $elemMapType = new Form_Element_Select('map_type', $this->tr('Vzhled mapy'));
      $elemMapType->setOptions(array(
         $this->tr('Normální') => self::MAP_TYPE_NORMAL,
         $this->tr('Satelitní') => self::MAP_TYPE_SATELLITE,
         $this->tr('Hybridní') => self::MAP_TYPE_HYBRID,
         $this->tr('Terénní') => self::MAP_TYPE_TERRAIN
      ));
      if(isset($settings[self::PARAM_MAP_TYPE])) {
         $elemMapType->setValues($settings[self::PARAM_MAP_TYPE]);
      }
      $form->addElement($elemMapType, $fGrpMap);
      
      // zoom
      $elemMapZoom = new Form_Element_Select('map_zoom', $this->tr('Výchozí zvětšení'));
      $elemMapZoom->setValues(8);
      $elemMapZoom->setOptions(array_combine(range(1, 21), range(1, 21)));
      if(isset($settings[self::PARAM_MAP_ZOOM])) {
         $elemMapZoom->setValues($settings[self::PARAM_MAP_ZOOM]);
      }
      $form->addElement($elemMapZoom, $fGrpMap);

      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $pos = explode(':', $form->map_pos->getValues());
         $settings[self::PARAM_MAP_X] = $pos[0];
         $settings[self::PARAM_MAP_Y] = $pos[1];
         $settings[self::PARAM_MAP_TYPE] = $form->map_type->getValues();
         $settings[self::PARAM_MAP_ZOOM] = (int)$form->map_zoom->getValues();
      }
   }
}
?>
