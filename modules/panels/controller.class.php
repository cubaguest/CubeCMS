<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class Panels_Controller extends Controller {
   private $editForm = null;

   private $panels = null;

   protected function init()
   {
      $panels = Face::getParamStatic('panels');
      if(empty($panels)){
         $panels = array();
         $panelPositions = vve_parse_cfg_value(VVE_PANEL_TYPES);
         foreach($panelPositions as $pos){
            $panels[$pos] = $pos;
         }
      }
      $this->view()->facePanels = $this->panels = $panels;
   }

   public function mainController() 
   {
      $this->checkWritebleRights();

      $model = new Model_Panel();

      $formRemove = $this->createRemoveForm();
      if($formRemove->isValid()){
         $panel = $model->record($formRemove->id->getValues());

         // smazání souborů
         if($panel->{Model_Panel::COLUMN_ICON} != null){
            $file = new Filesystem_File($panel->{Model_Panel::COLUMN_ICON},
                    AppCore::getAppWebDir().VVE_DATA_DIR.DIRECTORY_SEPARATOR
              .Panel_Obj::DATA_DIR.DIRECTORY_SEPARATOR.Panel_Obj::ICONS_DIR.DIRECTORY_SEPARATOR);
            $file->delete();
         }
         if($panel->{Model_Panel::COLUMN_BACK_IMAGE} != null){
            $file = new Filesystem_File($panel->{Model_Panel::COLUMN_BACK_IMAGE},
                    AppCore::getAppWebDir().VVE_DATA_DIR.DIRECTORY_SEPARATOR
              .Panel_Obj::DATA_DIR.DIRECTORY_SEPARATOR);
            $file->delete();
         }

         $model->delete($formRemove->id->getValues());

         $this->infoMsg()->addMessage($this->tr('Panel byl odstraněn'));
         $this->link()->reload();
      }
      $this->view()->formRemove = $formRemove;
      
      $formPriority = new Form('panel_');

      $elemId = new Form_Element_Hidden('id');
      $formPriority->addElement($elemId);

      $elemPos = new Form_Element_Text('position');
      $formPriority->addElement($elemPos);

      $elemSubmit = new Form_Element_SubmitImage('changepos');
      $formPriority->addElement($elemSubmit);

      if($formPriority->isValid()){
         $panel = $model->record($formPriority->id->getValues());
         $panel->{Model_Panel::COLUMN_ORDER} = $formPriority->position->getValues();
         $model->save($panel);
         $this->infoMsg()->addMessage($this->tr('Pozice byla uložena'));
         $this->link()->reload();
      }
      // view
      $this->view()->panels = $model->getPanelsList(0,false);

      // načtení individuálních panelů
      $modelCat = new Model_Category();
      
      $modelCat
         ->columns(array('*', 'numPanels' => 
               ' (SELECT count('.Model_Panel::COLUMN_ID.') FROM '.$model->getTableName()
               .' WHERE '.Model_Panel::COLUMN_ID_SHOW_CAT." = ".$modelCat->getTableShortName().'.'.Model_Category::COLUMN_ID
              ." OR ".Model_Panel::COLUMN_FORCE_GLOBAL." = 1 )"))
         ->where(Model_Category::COLUMN_INDIVIDUAL_PANELS." = 1", array());
      
      $this->view()->individualPanelCats = $modelCat->records();
   }

   public function addController() 
   {
      $this->checkWritebleRights();

      $this->createEditForm(true);

      if($this->editForm->isSend() AND $this->editForm->send->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }

      if($this->editForm->isValid()) {
         $model = new Model_Panel();
         $panel = $model->newRecord();
         
         $panel->{Model_Panel::COLUMN_ID_CAT} = $this->editForm->panel_cat->getValues();
         $panel->{Model_Panel::COLUMN_POSITION} = $this->editForm->panel_box->getValues();
         $panel->{Model_Panel::COLUMN_NAME} = $this->editForm->panel_name->getValues();
         $panel->{Model_Panel::COLUMN_ORDER} = $this->editForm->panel_order->getValues();
         $panel->{Model_Panel::COLUMN_FORCE_GLOBAL} = $this->editForm->forceGlobal->getValues();
         
         if(isset ($this->editForm->iconStored)){
            $panel->{Model_Panel::COLUMN_ICON} = $this->editForm->iconStored->getValues();
         }
         if(isset ($this->editForm->imageStored)){
            $panel->{Model_Panel::COLUMN_IMAGE} = $this->editForm->imageStored->getValues();
         }
         
         // ikona
         if($this->editForm->icon->getValues() != null) {
            $f = $this->editForm->icon->getValues();
            $panel->{Model_Panel::COLUMN_ICON} = $f['name'];
         }
         // obrázek
         if($this->editForm->image->getValues() != null) {
            $f = $this->editForm->image->getValues();
            $panel->{Model_Panel::COLUMN_IMAGE} = $f['name'];
         }
         
         if($this->editForm->panel_show_cat->getValues() == null || $this->editForm->forceGlobal->getValues() == true){
            $panel->{Model_Panel::COLUMN_ID_SHOW_CAT} = 0;
            $model->save($panel);
         } else {
            foreach ($this->editForm->panel_show_cat->getValues() as $id) {
               $panel->{Model_Panel::COLUMN_ID_SHOW_CAT} = $id;
               $pForSave = clone $panel;
               $model->save($pForSave);
            }
         }
         
         $this->log(sprintf('Přidán nový panel %s kategorie id:%s', $panel->{Model_Panel::COLUMN_NAME}, $panel->{Model_Panel::COLUMN_ID_CAT}));
         $this->infoMsg()->addMessage($this->tr('Panel byl uložen'));
         $this->link()->route()->reload();
      }
      $this->view()->form = $this->editForm;
   }

   public function editController()
   {
      $this->checkWritebleRights();
      $model = new Model_Panel();
      $panel = $model
         ->joinFK(Model_Panel::COLUMN_ID_CAT, array(Model_Category::COLUMN_NAME, Model_Category::COLUMN_URLKEY, Model_Category::COLUMN_MODULE))
         ->record($this->getRequest('id'));
      if($panel == false) return false;

      $this->createEditForm();

      $elemUpdateAll = new Form_Element_Checkbox('updateAll', $this->tr('Aktualizovat vše'));
      $elemUpdateAll->setSubLabel($this->tr('Aktualizovat všechny panely vybrané kategorie podle této úpravy.'));
      $this->editForm->addElement($elemUpdateAll, 'settings',5);
      
      $elemUpdateAllInBox = new Form_Element_Checkbox('updateAllInBox', $this->tr('Pouze ve vybraném boxu'));
      $elemUpdateAllInBox->setSubLabel($this->tr('Provede aktualizaci všech panelů pouze pro vybraný pox.'));
      $this->editForm->addElement($elemUpdateAllInBox, 'settings',6);
      
      $this->editForm->panel_cat->setValues($panel->{Model_Panel::COLUMN_ID_CAT});
      $this->editForm->panel_box->setValues($panel->{Model_Panel::COLUMN_POSITION});
      $this->editForm->panel_order->setValues($panel->{Model_Panel::COLUMN_ORDER});
      $this->editForm->panel_name->setValues($panel->{Model_Panel::COLUMN_NAME});
      $this->editForm->panel_show_cat->setValues($panel->{Model_Panel::COLUMN_ID_SHOW_CAT});
      $this->editForm->forceGlobal->setValues($panel->{Model_Panel::COLUMN_FORCE_GLOBAL});
      
      if(isset ($this->editForm->iconStored)){
         $this->editForm->iconStored->setValues($panel->{Model_Panel::COLUMN_ICON});
      }
      
      if(isset ($this->editForm->imageStored)){
         $this->editForm->imageStored->setValues($panel->{Model_Panel::COLUMN_IMAGE});
      }

      $elemId = new Form_Element_Hidden('id');
      $elemId->setValues($panel->{Model_Panel::COLUMN_ID});
      $this->editForm->addElement($elemId);

      if($this->editForm->isSend() AND $this->editForm->send->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }

      if($this->editForm->isValid()){
         
         $panel->{Model_Panel::COLUMN_ID_CAT} = $this->editForm->panel_cat->getValues();
         $panel->{Model_Panel::COLUMN_POSITION} = $this->editForm->panel_box->getValues();
         $panel->{Model_Panel::COLUMN_NAME} = $this->editForm->panel_name->getValues();
         $panel->{Model_Panel::COLUMN_ORDER} = $this->editForm->panel_order->getValues();
         $panel->{Model_Panel::COLUMN_ID_SHOW_CAT} = $this->editForm->panel_show_cat->getValues();
         $panel->{Model_Panel::COLUMN_FORCE_GLOBAL} = $this->editForm->forceGlobal->getValues();
         if(isset ($this->editForm->iconStored)){
            $panel->{Model_Panel::COLUMN_ICON} = $this->editForm->iconStored->getValues();
         }
         if(isset ($this->editForm->imageStored)){
            $panel->{Model_Panel::COLUMN_IMAGE} = $this->editForm->imageStored->getValues();
         }

         if($panel->{Model_Panel::COLUMN_FORCE_GLOBAL} == true){
            $panel->{Model_Panel::COLUMN_ID_SHOW_CAT} = 0;
         }
         
         // ikona
         if($this->editForm->icon->getValues() != null) {
            $f = $this->editForm->icon->getValues();
            $panel->{Model_Panel::COLUMN_ICON} = $f['name'];
         }
         // pozadí
         if($this->editForm->image->getValues() != null) {
            $f = $this->editForm->image->getValues();
            $panel->{Model_Panel::COLUMN_IMAGE} = $f['name'];
         }
         
         $model->save($panel);
         
         if($this->editForm->updateAll->getValues() == true){
            if($this->editForm->updateAllInBox->getValues()){
               $model->where(Model_Panel::COLUMN_ID_CAT." = :idc AND ".Model_Panel::COLUMN_POSITION." = :boxname", 
                     array('idc' => $this->editForm->panel_cat->getValues(), 'boxname' => $this->editForm->panel_box->getValues()));
            } else {
               $model->where(Model_Panel::COLUMN_ID_CAT." = :idc", array('idc' => $this->editForm->panel_cat->getValues()));
            }
               $model->update(array(
                  Model_Panel::COLUMN_NAME => $this->editForm->panel_name->getValues(),
                  Model_Panel::COLUMN_IMAGE => $panel->{Model_Panel::COLUMN_IMAGE},
                  Model_Panel::COLUMN_ICON => $panel->{Model_Panel::COLUMN_ICON},
                  Model_Panel::COLUMN_ORDER => $panel->{Model_Panel::COLUMN_ORDER},
               ));
            $this->log(sprintf('Upraveny panely kategorie id:%s', $panel->{Model_Panel::COLUMN_ID_CAT}));
         }

         $this->log(sprintf('Upraven panel %s kategorie id:%s', $panel->{Model_Panel::COLUMN_NAME}, $panel->{Model_Panel::COLUMN_ID_CAT}));
         $this->infoMsg()->addMessage($this->tr('Panel byl uložen'));
         $this->link()->route()->reload();
      }

      $this->view()->panelIcon = $panel->{Model_Panel::COLUMN_ICON};
      $this->view()->panelbackImg = $panel->{Model_Panel::COLUMN_BACK_IMAGE};
      $this->view()->panel = $panel;
      $this->view()->form = $this->editForm;
   }

   private function createEditForm($multipleCatSelect = false) 
   {
      // kategorie a šablony
      $struct = Category_Structure::getStructure(Category_Structure::ALL);
      $panelCats = $this->createArray($struct);

      $form = new Form('panel_'/*, true*/);
      $form->addGroup('settings', $this->tr('Základní'), $this->tr('Přiřazení panelu ke kategorii a jeho umístění'));

      $panelCategory = new Form_Element_Select('panel_cat', $this->tr('Panel kategorie'));
      foreach ($panelCats as $cat) {
         if(file_exists(AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
            .$cat['module'].DIRECTORY_SEPARATOR.'panel.class.php')) {
               $panelCategory->setOptions(array($cat['structname'] => $cat['id']), true);
         }
      }
      $form->addElement($panelCategory,'settings');

      // panel zobrazit u kategorie
      
      
      $panelForCats = $this->createArray($struct, true);

      $panelShowCategory = new Form_Element_Select('panel_show_cat', $this->tr('Zobrazit v'));
      $panelShowCategory->setOptions(array($this->tr('Globálně') => 0));
      foreach ($panelForCats as $cat) {
         $panelShowCategory->setOptions(array($cat['structname'] => $cat['id']), true);
      }
      
      $panelShowCategory->setSubLabel($this->tr('Pokud je nastaveno globálně, panel je
         zobrazen u všech kategorií, v opačném případě pouze u vybrané kategorie'));
      $panelShowCategory->setMultiple($multipleCatSelect);
      $form->addElement($panelShowCategory,'settings');

      if($this->getRequestParam('idcto') != null){
         $panelShowCategory->setValues($this->getRequestParam('idcto'));
      }
      
      $elemForceGlobal = new Form_Element_Checkbox('forceGlobal', $this->tr('Vynutit globálně'));
      $elemForceGlobal->setSubLabel($this->tr('Vynutí panelu zobrazení i v kategoriích s individuálními panely.'));
      $form->addElement($elemForceGlobal, 'settings');
      
      $panelType = new Form_Element_Select('panel_box', $this->tr('Box panelu'));
      $panelType->setOptions(array_flip($this->panels));
      $form->addElement($panelType,'settings');

      $panelOrder = new Form_Element_Text('panel_order', $this->tr('Řazení panelu'));
      $panelOrder->setValues(0);
      $form->addElement($panelOrder,'settings');

      $form->addGroup('view', $this->tr('Vzhled'), $this->tr('Nastavení názvu, pozadí a ikony panelu'));

      $panelName = new Form_Element_Text('panel_name', $this->tr('Název panelu'));
      $panelName->setLangs();
      $panelName->setSubLabel($this->tr('Pokud není název zvolen, je použit název kategorie'));
      $form->addElement($panelName,'view');

      $elemIcon = new  Form_Element_File('icon', $this->tr('Ikona'));
      $elemIcon->setUploadDir(Panel_Obj::getIconDir(false));
      $elemIcon->addValidation(new Form_Validator_FileExtension('jpg;png;gif'));
      $form->addElement($elemIcon,'view');

      $dir = Panel_Obj::getIconDir(false);
      if(is_dir($dir)){
         $elemIconStored = new Form_Element_Select('iconStored', $this->tr('Uložené ikony'));
         $elemIconStored->addOption($this->tr('Žádný'), null);
         foreach (glob($dir."*.{jpg,jpeg,gif,png,JPG,JPEG,GIF,PNG}", GLOB_BRACE) as $filename) {
            $elemIconStored->addOption(basename($filename), basename($filename));
         }
         $form->addElement($elemIconStored,'view');
      }
      
      $elemImage = new  Form_Element_File('image', $this->tr('Obrázek panelu'));
      $elemImage->setSubLabel($this->tr('Obrázek panelu nebo pozadí dle vytvořené šablony.'));
      $elemImage->setUploadDir(Panel_Obj::getImgDir(false));
      $elemImage->addValidation(new Form_Validator_FileExtension('jpg;png;gif'));
      $form->addElement($elemImage,'view');
      
      $dir = Panel_Obj::getImgDir(false);
      if(is_dir($dir)){
         $elemImgStored = new Form_Element_Select('imageStored', $this->tr('Uložené obrázky'));
         $elemImgStored->addOption($this->tr('Žádný'), null);
         foreach (glob($dir."*.{jpg,jpeg,gif,png,JPG,JPEG,GIF,PNG}", GLOB_BRACE) as $filename) {
            $elemImgStored->addOption(basename($filename), basename($filename));
         }
         $form->addElement($elemImgStored,'view');
      }

      $submitButton = new Form_Element_SaveCancel('send');
      $form->addElement($submitButton);

      $this->editForm = $form;
   }

   protected function createRemoveForm()
   {
      $formRemove = new Form('panelDelete');
      $elemId = new Form_Element_Hidden('id');
      $formRemove->addElement($elemId);

      $elemSubmit = new Form_Element_Submit('delete', $this->tr('Smazat'));
      $formRemove->addElement($elemSubmit);
      return $formRemove;
   }
   
   protected function createArray(Category_Structure $struct, $onlyIndividual = false, $level = 0)
   {
      $a = array();
      foreach ($struct as $cat) {
         if($onlyIndividual == false || $cat->getCatObj()->isIndividualPanels() ){
            $a[] = array(
               'id' => $cat->getId(), 
               'name' => $cat->getCatObj()->getName(),
               'structname' => str_repeat('. ', $level).$cat->getCatObj()->getName()." ID:".$cat->getId(),
               'module' => (string)$cat->getCatObj()->getModule());
            
         }
         
         if(!$cat->isEmpty()){
            $a = array_merge($a, $this->createArray($cat, $onlyIndividual, $level+1));
         }
      }
      return $a;
   } 
   
   private function catsToArrayForForm($categories) 
   {
      // pokud je hlavní kategorie
      if($categories->getLevel() != 0) {
         $this->categoriesArray[str_repeat('&nbsp;', $categories->getLevel()*3).
                         (string)$categories->getCatObj()->{Model_Category::COLUMN_CAT_LABEL}]
                 = (string)$categories->getCatObj()->{Model_Category::COLUMN_CAT_ID};
      } else {
         $this->categoriesArray[$this->tr('Kořen')] = 0;
      }
      if(!$categories->isEmpty()) {
         foreach ($categories as $cat) {
            $this->catsToArrayForForm($cat);
         }
      }
   }

   private function getPanelInfo($module, $panelName) 
   {
      $file = file_get_contents(AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
              .$module.DIRECTORY_SEPARATOR.Template::TEMPLATES_DIR.DIRECTORY_SEPARATOR.$panelName);



   }

   public function getPanelsController() 
   {
      $data = array('code' => true);
      //načtení panelů podel id Categorie
      $data['data'] = $this->getPanelsTemplates($this->getRequestParam('id'));
      if(empty ($data['data'])) return false;

      $this->view()->data = $data;
   }

   private function getPanelsTemplates($catId) 
   {
      $catModel = new Model_Category();
      $cat = $catModel->getCategoryById($catId);

      $tpls = array();
      foreach (new DirectoryIterator(AppCore::getAppLibDir().AppCore::MODULES_DIR
              .DIRECTORY_SEPARATOR.$cat[Model_Category::COLUMN_MODULE]
              .DIRECTORY_SEPARATOR.Template::TEMPLATES_DIR) as $fileInfo) {
         if($fileInfo->isDot()) continue;
         if(preg_match('/^panel.*\.phtml$/', $fileInfo->getFilename())) {
            array_push($tpls, $fileInfo->getFilename());
         }
      }
      return $tpls;
   }

   public function getPanelInfoController() 
   {
      $catModel = new Model_Category();
      $cat = $catModel->getCategoryById($this->getRequestParam('id'));
      $data = array('code' => true, 'data' => $this->tr('Žádné informace'));

      $cnt = file_get_contents(AppCore::getAppLibDir().AppCore::MODULES_DIR
              .DIRECTORY_SEPARATOR.$cat[Model_Category::COLUMN_MODULE]
              .DIRECTORY_SEPARATOR.Template::TEMPLATES_DIR.DIRECTORY_SEPARATOR.$this->getRequestParam('tplname'));
      $out = array();
      if (preg_match ("/<desc>(.*)<\/desc>/i", $cnt, $out)) {
         $data['data'] = $out[1];
      }
      $this->view()->data = $data;
   }

   public function getListPanelsController()
   {
      $this->checkWritebleRights();

      $model = new Model_Panel();
      $idc = $this->getRequestParam('idc', 0);
      
      $this->view()->panels = $model
         ->joinFK(Model_Panel::COLUMN_ID_CAT)
         ->where(Model_Panel::COLUMN_ID_SHOW_CAT." = :idc OR ".Model_Panel::COLUMN_FORCE_GLOBAL." = 1", array('idc' => $idc))
         ->order(array(Model_Panel::COLUMN_POSITION => Model_ORM::ORDER_ASC, Model_Panel::COLUMN_ORDER => Model_ORM::ORDER_DESC))
         ->records();
      
      $this->view()->formRemove = $this->createRemoveForm();
   }

   public function panelSettingsController()
   {
      $this->checkWritebleRights();

      $modelPanel = new Model_Panel();
      $panel = $modelPanel
      ->joinFK(Model_Panel::COLUMN_ID_CAT)->record($this->getRequest('id'));
      
      if($panel == false){ return false; }
      
      if((string)$panel->{Model_Panel::COLUMN_NAME} != null){
         $this->view()->panelName = $panel->{Model_Panel::COLUMN_NAME};
      } else {
         $this->view()->panelName = $panel->{Model_Category::COLUMN_CAT_LABEL};
      }
      
      $category = new Category($panel->{Model_Category::COLUMN_CAT_ID}, false, $panel);
      $class = ucfirst($category->getModule()->getName()).'_Panel';
      
      if(class_exists($class, true)){
//         $panelObj = new Panel($category, $this->routes());
         $panelObj = new $class($category, $this->routes(), null, $this->link() );
         $panelObj->viewSettingsController();
         $this->view()->form = $panelObj->_getTemplateObj()->form;
      }
   }

}

?>