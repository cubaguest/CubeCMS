<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class Panels_Controller extends Controller {
   private $editForm = null;

   public function mainController() {
      $this->checkWritebleRights();

      $model = new Model_Panel();

      $formRemove = new Form('panel_');
      $elemId = new Form_Element_Hidden('id');
      $formRemove->addElement($elemId);

      $elemSubmit = new Form_Element_SubmitImage('delete');
      $formRemove->addElement($elemSubmit);

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
      $this->view()->gobalpanels = $model->getPanelsList(0,false);;

      // načtení individuálních panelů
      $modelCat = new Model_Category();
      $this->view()->individualPanelCats = $modelCat->getCategoriesWithIndPanels();


   }

   public function addController() {
      $this->checkWritebleRights();

      $this->createEditForm();
      // odstranění checkboxů
      $this->editForm->removeElement('icon_delete');
      $this->editForm->removeElement('background_delete');

      if($this->editForm->isSend() AND $this->editForm->send->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }

      if($this->editForm->isValid()) {
         $model = new Model_Panel();
         $panel = $model->newRecord();
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


         $panel->{Model_Panel::COLUMN_ID_CAT} = $this->editForm->panel_cat->getValues();
         $panel->{Model_Panel::COLUMN_POSITION} = $this->editForm->panel_box->getValues();
         $panel->{Model_Panel::COLUMN_NAME} = $this->editForm->panel_name->getValues();
         $panel->{Model_Panel::COLUMN_ORDER} = $this->editForm->panel_order->getValues();
         $panel->{Model_Panel::COLUMN_ID_SHOW_CAT} = $this->editForm->panel_show_cat->getValues();

         $model->save($panel);
         $this->log(sprintf('Přidán nový panel %s kategorie %s do aplikace', $panel->{Model_Panel::COLUMN_NAME}, $panel->{Model_Panel::COLUMN_ID_CAT}));
         $this->infoMsg()->addMessage($this->tr('Panel byl uložen'));
         $this->link()->route()->reload();
      }
      $this->view()->form = $this->editForm;
   }

   public function editController(){
      $this->checkWritebleRights();
      $model = new Model_Panel();
      $panel = $model->record($this->getRequest('id'));
      if($panel == false) return false;

      $this->createEditForm();

      $this->editForm->panel_cat->setValues($panel->{Model_Panel::COLUMN_ID_CAT});
      $this->editForm->panel_box->setValues($panel->{Model_Panel::COLUMN_POSITION});
      $this->editForm->panel_order->setValues($panel->{Model_Panel::COLUMN_ORDER});
      $this->editForm->panel_name->setValues($panel->{Model_Panel::COLUMN_NAME});
      $this->editForm->panel_show_cat->setValues($panel->{Model_Panel::COLUMN_ID_SHOW_CAT});
      if($panel->{Model_Panel::COLUMN_ICON} == null){
         $this->editForm->removeElement('icon_delete');
      }
      if($panel->{Model_Panel::COLUMN_IMAGE} == null){
         $this->editForm->removeElement('image_delete');
      }

      $elemId = new Form_Element_Hidden('id');
      $elemId->setValues($panel->{Model_Panel::COLUMN_ID});
      $this->editForm->addElement($elemId);

      if($this->editForm->isSend() AND $this->editForm->send->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }

      if($this->editForm->isValid()){
         // ikona
         if($panel->{Model_Panel::COLUMN_ICON} != null AND ($this->editForm->icon->getValues() != null
                 OR ($this->editForm->haveElement('icon_delete') AND $this->editForm->icon_delete->getValues() == true))){
            $file = new Filesystem_File($icon, Panel_Obj::getIconDir(false));
            if($file->exist()) $file->delete();
            $panel->{Model_Panel::COLUMN_ICON} = null;
         }
         if($this->editForm->icon->getValues() != null) {
            $f = $this->editForm->icon->getValues();
            $panel->{Model_Panel::COLUMN_ICON} = $f['name'];
         }
         // pozadí
         if($panel->{Model_Panel::COLUMN_IMAGE} != null AND ($this->editForm->image->getValues() != null
                 OR ($this->editForm->haveElement('image_delete') AND $this->editForm->image_delete->getValues() == true))){
            $file = new Filesystem_File($image, Panel_Obj::getImgDir(false));
            $file->delete();
            $panel->{Model_Panel::COLUMN_IMAGE} = null;
         }
         if($this->editForm->image->getValues() != null) {
            $f = $this->editForm->image->getValues();
            $panel->{Model_Panel::COLUMN_IMAGE} = $f['name'];
         }

         $panel->{Model_Panel::COLUMN_ID_CAT} = $this->editForm->panel_cat->getValues();
         $panel->{Model_Panel::COLUMN_POSITION} = $this->editForm->panel_box->getValues();
         $panel->{Model_Panel::COLUMN_NAME} = $this->editForm->panel_name->getValues();
         $panel->{Model_Panel::COLUMN_ORDER} = $this->editForm->panel_order->getValues();
         $panel->{Model_Panel::COLUMN_ID_SHOW_CAT} = $this->editForm->panel_show_cat->getValues();

         $model->save($panel);

         $this->log(sprintf('Přidán nový panel %s kategorie %s do aplikace', $panel->{Model_Panel::COLUMN_NAME}, $panel->{Model_Panel::COLUMN_ID_CAT}));
         $this->infoMsg()->addMessage($this->tr('Panel byl uložen'));
         $this->link()->route()->reload();
      }

      $this->view()->panelIcon = $panel->{Model_Panel::COLUMN_ICON};
      $this->view()->panelbackImg = $panel->{Model_Panel::COLUMN_BACK_IMAGE};
      $this->view()->form = $this->editForm;
   }

   private function createEditForm() {
      // kategorie a šablony
      $panelPositions = vve_parse_cfg_value(VVE_PANEL_TYPES);

      $catModel = new Model_Category();
      $categories = $catModel->getCategoryList(true);
      $catArr = array();
      foreach ($categories as $cat) {
         if(file_exists(AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
         .$cat->{Model_Category::COLUMN_MODULE}.DIRECTORY_SEPARATOR
         .DIRECTORY_SEPARATOR.'panel.class.php')) {
            $catArr[(string)$cat->{Model_Category::COLUMN_CAT_LABEL}] = $cat->{Model_Category::COLUMN_CAT_ID};
         }
      }
      ksort($catArr);//řazení

      $form = new Form('panel_', true);
      $form->addGroup('settings', $this->tr('Základní'), $this->tr('Přiřazení panelu ke kategorii a jeho umístění'));

      $panelCategory = new Form_Element_Select('panel_cat', $this->tr('Panel kategorie'));
      $panelCategory->setOptions($catArr);
      $form->addElement($panelCategory,'settings');

      // panel zobrazit u kategorie
      $showCat = array ();

      $individualPanelsCats = $catModel->getCategoriesWithIndPanels();
      $arr = array();
      foreach ($individualPanelsCats as $cat) {
         $arr[(string)$cat->{Model_Category::COLUMN_CAT_LABEL}] = $cat->{Model_Category::COLUMN_CAT_ID};
      }
      ksort($arr); // řazení
      $showCat = array_merge(array ($this->tr('Globálně') => 0),$arr);


      $panelShowCategory = new Form_Element_Select('panel_show_cat', $this->tr('Určení pro'));
      $panelShowCategory->setOptions($showCat);
      $panelShowCategory->setSubLabel($this->tr('Pokud je nastaveno globálně, panel je
         zobrazen u všech kategorií, v opačném případě pouze u vybrané kategorie'));
      $form->addElement($panelShowCategory,'settings');

      $panelType = new Form_Element_Select('panel_box', $this->tr('Box panelu'));
      $panelType->setOptions($panelPositions);
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

      $elemIconDelete = new Form_Element_Checkbox('icon_delete', $this->tr('Smazat ikonu')."?");
      $form->addElement($elemIconDelete,'view');

      $elemImage = new  Form_Element_File('image', $this->tr('Obrázek panelu'));
      $elemImage->setSubLabel($this->tr('Obrázek panelu nebo pozadí dle vytvořené šablony.'));
      $elemImage->setUploadDir(Panel_Obj::getImgDir(false));
      $elemImage->addValidation(new Form_Validator_FileExtension('jpg;png;gif'));
      $form->addElement($elemImage,'view');

      $elemImageDelete = new Form_Element_Checkbox('image_delete', $this->tr('Smazat obrázek?'));
      $form->addElement($elemImageDelete,'view');

      $submitButton = new Form_Element_SaveCancel('send');
      $form->addElement($submitButton);

      $this->editForm = $form;
   }

   private function catsToArrayForForm($categories) {
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

   private function getPanelInfo($module, $panelName) {
      $file = file_get_contents(AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
              .$module.DIRECTORY_SEPARATOR.Template::TEMPLATES_DIR.DIRECTORY_SEPARATOR.$panelName);



   }

   public function getPanelsController() {
      $data = array('code' => true);
      //načtení panelů podel id Categorie
      $data['data'] = $this->getPanelsTemplates($this->getRequestParam('id'));
      if(empty ($data['data'])) return false;

      $this->view()->data = $data;
   }

   private function getPanelsTemplates($catId) {
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

   public function getPanelInfoController() {
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

   public function getListPanelsController(){
      $this->checkWritebleRights();

      $model = new Model_Panel();

      $this->view()->panels = $model->getPanelsList($this->getRequestParam('idc', 0));

   }

   public function panelSettingsController(){
      $this->checkWritebleRights();

      $panelM = new Model_Panel();
      $panel = $panelM->getPanel($this->getRequest('id'));
      if($panel === false) return false;
      if((string)$panel->{Model_Panel::COLUMN_NAME} != null){
         $this->view()->panelName = $panel->{Model_Panel::COLUMN_NAME};
      } else {
         $this->view()->panelName = $panel->{Model_Category::COLUMN_CAT_LABEL};
      }
      $this->view()->moduleName = $panel->{Model_Category::COLUMN_MODULE};

      $func = array(ucfirst($panel->{Model_Category::COLUMN_MODULE}).'_Panel','settingsController');

      $form = new Form('settings_', true);
      $form->addGroup('basic', 'Základní nasatvení');
      $md5FormEmpty = md5(serialize($form));

      if($panel->{Model_Panel::COLUMN_PARAMS}!= null){
         $settings = unserialize($panel->{Model_Panel::COLUMN_PARAMS});
      } else {
         $settings = array();
      }
      $settings['_module'] = $panel->{Model_Category::COLUMN_MODULE};
      call_user_func_array($func, array(&$settings, &$form));
      unset($settings['_module']);

      // pokud je formulář prázdný
      if($md5FormEmpty == md5(serialize($form))){
         $form = null;
      } else {
         $form->addGroup('buttons');
         $elemSend = new Form_Element_SaveCancel('send');
         $form->addElement($elemSend, 'buttons');
      }

      if($form != null AND $form->isSend() AND $form->send->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }

      if($form != null AND $form->isValid()){
         // čištění nulových hodnot
         foreach ($settings as $key => $option){
            if($option === null OR $option === ''){
               unset($settings[$key]);
            }
         }
         $panelM->saveParams($this->getRequest('id'), serialize($settings));

         $this->infoMsg()->addMessage($this->tr('Uloženo'));
         $this->link()->route()->reload();
      }

      $this->view()->form = $form;
   }

}

?>