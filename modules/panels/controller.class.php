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
         $panel = $model->getPanel($formRemove->id->getValues());

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

         $model->deletePanel($formRemove->id->getValues());

         $this->infoMsg()->addMessage($this->_('Panel byl odstraněn'));
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
         $model->savePanelPos($formPriority->id->getValues(), $formPriority->position->getValues());
         $this->infoMsg()->addMessage($this->_('Pozice byla uložena'));
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

      if($this->editForm == null){
         $this->editForm = $this->createEditForm();
      }
      // odstranění checkboxů
      $this->editForm->removeElement('icon_delete');
      $this->editForm->removeElement('background_delete');

      if($this->editForm->isValid()) {
         $icon = null;
         if($this->editForm->icon->getValues() != null) {
            $f = $this->editForm->icon->getValues();
            $icon = $f['name'];
         }
         $backImage = null;
         if($this->editForm->background->getValues() != null) {
            $f = $this->editForm->background->getValues();
            $backImage = $f['name'];
         }

         $panelM = new Model_Panel();
         $panelM->savePanel($this->editForm->panel_cat->getValues(), $this->editForm->panel_box->getValues(),
                 $this->editForm->panel_name->getValues(), $icon, $backImage, 
                 $this->editForm->panel_order->getValues(), $this->editForm->panel_show_cat->getValues());

         $this->infoMsg()->addMessage($this->_('Panel byl uložen'));
         $this->link()->route()->reload();
      }
      $this->view()->form = $this->editForm;
   }

   public function editController(){
      $this->checkWritebleRights();
      $model = new Model_Panel();
      $panel = $model->getPanel($this->getRequest('id'));
      if($panel == false) return false;

      $form = $this->createEditForm();

      $form->panel_cat->setValues($panel->{Model_Panel::COLUMN_ID_CAT});
      $form->panel_box->setValues($panel->{Model_Panel::COLUMN_POSITION});
      $form->panel_order->setValues($panel->{Model_Panel::COLUMN_ORDER});
      $form->panel_name->setValues($panel->{Model_Panel::COLUMN_NAME});
      $form->panel_show_cat->setValues($panel->{Model_Panel::COLUMN_ID_SHOW_CAT});
      if($panel->{Model_Panel::COLUMN_ICON} == null){
         $form->removeElement('icon_delete');
      }
      if($panel->{Model_Panel::COLUMN_BACK_IMAGE} == null){
         $form->removeElement('background_delete');
      }

      $elemId = new Form_Element_Hidden('id');
      $elemId->setValues($panel->{Model_Panel::COLUMN_ID});
      $form->addElement($elemId);

      if($form->isValid()){
         $icon = $panel->{Model_Panel::COLUMN_ICON};
         if($icon != null AND ($form->icon->getValues() != null
                 OR ($form->haveElement('icon_delete') AND $form->icon_delete->getValues() == true))){
            $file = new Filesystem_File($icon, AppCore::getAppWebDir().VVE_DATA_DIR.DIRECTORY_SEPARATOR
                    .Panel_Obj::DATA_DIR.DIRECTORY_SEPARATOR.Panel_Obj::ICONS_DIR);
            if($file->exist()) $file->delete();
            $icon = null;
         }
         if($form->icon->getValues() != null) {
            $f = $form->icon->getValues();
            $icon = $f['name'];
         }

         $backImage = $panel->{Model_Panel::COLUMN_BACK_IMAGE};
         if($backImage != null AND ($form->background->getValues() != null
                 OR ($form->haveElement('background_delete') AND $form->background_delete->getValues() == true))){
            $file = new Filesystem_File($backImage,
                    AppCore::getAppWebDir().VVE_DATA_DIR.DIRECTORY_SEPARATOR
                    .Panel_Obj::DATA_DIR);
            $file->delete();
            $backImage = null;
         }
         if($form->background->getValues() != null) {
            $f = $form->background->getValues();
            $backImage = $f['name'];
         }

         $model->savePanel($form->panel_cat->getValues(), $form->panel_box->getValues(), $form->panel_name->getValues(),
                 $icon, $backImage, $form->panel_order->getValues(),
                 $form->panel_show_cat->getValues(), $form->id->getValues());
         $this->infoMsg()->addMessage($this->_('Panel byl uložen'));
         $this->link()->route()->reload();
      }

      $this->view()->panelIcon = $panel->{Model_Panel::COLUMN_ICON};
      $this->view()->panelbackImg = $panel->{Model_Panel::COLUMN_BACK_IMAGE};
      $this->view()->form = $form;
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

      $form = new Form('panel_');
      $form->addGroup('settings', $this->_('Základní'), $this->_('Přiřazení panelu ke kategorii a jeho umístění'));

      $panelCategory = new Form_Element_Select('panel_cat', $this->_('Panel kategorie'));
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
      $showCat = array_merge(array ($this->_('Globálně') => 0),$arr);


      $panelShowCategory = new Form_Element_Select('panel_show_cat', $this->_('Určení pro'));
      $panelShowCategory->setOptions($showCat);
      $panelShowCategory->setSubLabel($this->_('Pokud je nastaveno globálně, panel je
         zobrazen u všech kategorií, v opačném případě pouze u vybrané kategorie'));
      $form->addElement($panelShowCategory,'settings');

      $panelType = new Form_Element_Select('panel_box', $this->_('Box panelu'));
      $panelType->setOptions($panelPositions);
      $form->addElement($panelType,'settings');

      $panelOrder = new Form_Element_Text('panel_order', $this->_('Řazení panelu'));
      $panelOrder->setValues(0);
      $form->addElement($panelOrder,'settings');

      $form->addGroup('view', $this->_('Vzhled'), $this->_('Nastavení názvu, pozadí a ikony panelu'));

      $panelName = new Form_Element_Text('panel_name', $this->_('Název panelu'));
      $panelName->setLangs();
      $panelName->setSubLabel($this->_('Pokud není název zvolen, je použit název kategorie'));
      $form->addElement($panelName,'view');

      $elemIcon = new  Form_Element_File('icon', $this->_('Ikona'));
      $elemIcon->setUploadDir(Panel_Obj::getIconDir(false));
      $elemIcon->addValidation(new Form_Validator_FileExtension('jpg;png;gif'));
      $form->addElement($elemIcon,'view');

      $elemIconDelete = new Form_Element_Checkbox('icon_delete', $this->_('Smazat ikonu')."?");
      $form->addElement($elemIconDelete,'view');

      $elemBack = new  Form_Element_File('background', $this->_('Pozadí'));
      $elemBack->setUploadDir(Panel_Obj::getBackImgDir(false));
      $elemBack->addValidation(new Form_Validator_FileExtension('jpg;png;gif'));
      $form->addElement($elemBack,'view');

      $elemBackDelete = new Form_Element_Checkbox('background_delete', $this->_('Smazat pozadí')."?");
      $form->addElement($elemBackDelete,'view');

      $submit = new Form_Element_Submit('send', $this->_('Uložit'));
      $form->addElement($submit);

      return $form;
   }

   private function catsToArrayForForm($categories) {
      // pokud je hlavní kategorie
      if($categories->getLevel() != 0) {
         $this->categoriesArray[str_repeat('&nbsp;', $categories->getLevel()*3).
                         (string)$categories->getCatObj()->{Model_Category::COLUMN_CAT_LABEL}]
                 = (string)$categories->getCatObj()->{Model_Category::COLUMN_CAT_ID};
      } else {
         $this->categoriesArray[$this->_('Kořen')] = 0;
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
      $data = array('code' => true, 'data' => $this->_('Žádné informace'));

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

      $form = new Form('settings_');
      $form->addGroup('basic', 'Základní nasatvení');
      $md5FormEmpty = md5(serialize($form));

      if($panel->{Model_Panel::COLUMN_PARAMS}!= null){
         $settings = unserialize($panel->{Model_Panel::COLUMN_PARAMS});
      } else {
         $settings = array();
      }

      call_user_func_array($func, array(&$settings, &$form));

      // pokud je formulář prázdný
      if($md5FormEmpty == md5(serialize($form))){
         $form = null;
      } else {
         $form->addGroup('buttons');
         $elemSend = new Form_Element_Submit('send', 'Odeslat');
         $form->addElement($elemSend, 'buttons');
      }

      if($form != null AND $form->isValid()){
         // čištění nulových hodnot
         foreach ($settings as $key => $option){
            if($option === null OR empty ($option) OR $option === 0){
               unset($settings[$key]);
            }
         }
         $panelM->saveParams($this->getRequest('id'), serialize($settings));

         $this->infoMsg()->addMessage($this->_('Uloženo'));
         $this->link()->route()->reload();
      }

      $this->view()->form = $form;
   }

}

?>