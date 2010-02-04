<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class Panels_Controller extends Controller {
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
              .Panel::DATA_DIR.DIRECTORY_SEPARATOR.Panel::ICONS_DIR.DIRECTORY_SEPARATOR);
            $file->delete();
         }
         if($panel->{Model_Panel::COLUMN_BACK_IMAGE} != null){
            $file = new Filesystem_File($panel->{Model_Panel::COLUMN_BACK_IMAGE},
                    AppCore::getAppWebDir().VVE_DATA_DIR.DIRECTORY_SEPARATOR
              .Panel::DATA_DIR.DIRECTORY_SEPARATOR);
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
      $this->view()->panelM = $model;
   }

   public function addController() {
      $this->checkWritebleRights();

      $form = $this->createEditForm();
      // doplnění šablon panelů pro danou kategorii
      $categories = $form->panel_cat->getOptions();
      $tpls = $this->getPanelsTemplates(reset($categories));
      foreach ($tpls as $tpl) {
         $form->panel_template->setOptions(array($tpl => $tpl), true);
      }
      // odstranění checkboxů
      $form->removeElement('icon_delete');
      $form->removeElement('background_delete');

      if($form->isValid()) {
         $icon = null;
         if($form->icon->getValues() != null) {
            $f = $form->icon->getValues();
            $icon = $f['name'];
         }
         $backImage = null;
         if($form->background->getValues() != null) {
            $f = $form->background->getValues();
            $backImage = $f['name'];
         }

         $panelM = new Model_Panel();
         $panelM->savePanel($form->panel_cat->getValues(), $form->panel_box->getValues(),
                 $icon, $backImage, $form->panel_order->getValues(), $form->panel_template->getValues());

         $this->infoMsg()->addMessage($this->_('Panel byl uložen'));
         $this->link()->route()->reload();
      }
      $this->view()->form = $form;
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
                    .Panel::DATA_DIR.DIRECTORY_SEPARATOR.Panel::ICONS_DIR);
            $file->delete();
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
                    .Panel::DATA_DIR);
            $file->delete();
            $backImage = null;
         }
         if($form->background->getValues() != null) {
            $f = $form->background->getValues();
            $backImage = $f['name'];
         }

         $model->savePanel($form->panel_cat->getValues(), $form->panel_box->getValues(),
                 $icon, $backImage, $form->panel_order->getValues(), $form->panel_template->getValues(),
                 $form->id->getValues());
         $this->infoMsg()->addMessage($this->_('Panel byl uložen'));
         $this->link()->route()->reload();
      }

      $this->view()->panelIcon = $panel->{Model_Panel::COLUMN_ICON};
      $this->view()->panelbackImg = $panel->{Model_Panel::COLUMN_BACK_IMAGE};
      $this->view()->panelTplSel = $panel->{Model_Panel::COLUMN_TPL};
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
         .Template::TEMPLATES_DIR.DIRECTORY_SEPARATOR.'panel.phtml')) {
            $catArr[(string)$cat->{Model_Category::COLUMN_CAT_LABEL}] = $cat->{Model_Category::COLUMN_CAT_ID};
         }
      }

      $form = new Form('panel_');
      $form->addGroup('settings', $this->_('Základní'), $this->_('Přiřazení panelu ke kategorii a jeho umístění'));

      $panelCategory = new Form_Element_Select('panel_cat', $this->_('Panel kategorie'));
      $panelCategory->setOptions($catArr);
      $form->addElement($panelCategory,'settings');

      $panelType = new Form_Element_Select('panel_box', $this->_('Box panelu'));
      $panelType->setOptions($panelPositions);
      $form->addElement($panelType,'settings');

      $panelOrder = new Form_Element_Text('panel_order', $this->_('Řazení panelu'));
      $panelOrder->setValues(0);
      $form->addElement($panelOrder,'settings');

      $form->addGroup('view', $this->_('Vzhled'), $this->_('Nastavení šablony, pozadí a ikony panelu'));

      $elemIcon = new  Form_Element_File('icon', $this->_('Ikona'));
      $elemIcon->setUploadDir(AppCore::getAppWebDir().VVE_DATA_DIR.DIRECTORY_SEPARATOR
              .Panel::DATA_DIR.DIRECTORY_SEPARATOR.Panel::ICONS_DIR);
      $elemIcon->addValidation(new Form_Validator_FileExtension('jpg;png;gif'));
      $form->addElement($elemIcon,'view');

      $elemIconDelete = new Form_Element_Checkbox('icon_delete', $this->_('Smazat ikonu')."?");
      $form->addElement($elemIconDelete,'view');

      $elemBack = new  Form_Element_File('background', $this->_('Pozadí'));
      $elemBack->setUploadDir(AppCore::getAppWebDir().VVE_DATA_DIR.DIRECTORY_SEPARATOR
              .Panel::DATA_DIR);
      $elemBack->addValidation(new Form_Validator_FileExtension('jpg;png;gif'));
      $form->addElement($elemBack,'view');

      $elemBackDelete = new Form_Element_Checkbox('background_delete', $this->_('Smazat pozadí')."?");
      $form->addElement($elemBackDelete,'view');

      $panelTemplate = new Form_Element_Select('panel_template', $this->_('Šablona panelu'));
//      $panelTemplate->setOptions(array('panel.phtml' => 'panel.phtml'));
//      $panelTemplate->setOptions($catPanelsTpls[reset($categories)->{Model_Category::COLUMN_CAT_ID}], true);
      $form->addElement($panelTemplate,'view');

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

}

?>