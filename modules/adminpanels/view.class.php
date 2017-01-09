<?php

/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */
class AdminPanels_View extends View {

   public function mainView()
   {
      $this->template()->addTplFile('list.phtml');
      $this->createPanelsToolboxes($this->panels);

      Template_Module::setFullWidth(true);
   }

   protected function createPanelsToolboxes($panels)
   {
      $toolboxPanel = new Template_Toolbox2();
      $toolboxPanel->setTemplate(Template_Toolbox2::TEMPLATE_INLINE);

      $toolSettings = new Template_Toolbox2_Tool_Redirect('settings', $this->tr('Nastavení panelu'));
      $toolSettings->setIcon(Template_Toolbox2::ICON_WRENCH);
      $toolboxPanel->addTool($toolSettings);

      $toolEdit = new Template_Toolbox2_Tool_PostRedirect('edit', $this->tr('Úprava panelu'));
      $toolEdit->setIcon(Template_Toolbox2::ICON_PEN);
      $toolboxPanel->addTool($toolEdit);

      $toolRemove = new Template_Toolbox2_Tool_Form($this->formRemove);
      $toolRemove->setIcon(Template_Toolbox2::ICON_DELETE);
      $toolRemove->setImportant(true);
      $toolRemove->setConfirmMeassage($this->tr('Opravdu smazat panel?'));
      $toolboxPanel->addTool($toolRemove);

      foreach ($panels as $panel) {
         $toolboxPanel->settings->setAction($this->link()->clear()->route('settings', array('id' => $panel->{Model_Panel::COLUMN_ID})));
         $toolboxPanel->edit->setAction($this->link()->clear()->route('edit', array('id' => $panel->{Model_Panel::COLUMN_ID})));
         $toolboxPanel->panelDelete->getForm()->setAction($this->link()->clear()->route());
         $toolboxPanel->panelDelete->getForm()->id->setValues($panel->{Model_Panel::COLUMN_ID});

         $panel->toolbox = clone $toolboxPanel;
      }
   }

   public function addView()
   {
      Template_Navigation::addItem($this->tr('Přidání panelu'), $this->link(), null, null, null, true);
      $this->template()->addTplFile('edit.phtml');
      Template_Module::setFullWidth(true);
   }

   public function editView()
   {
      Template_Navigation::addItem($this->tr('Úprava panelu'), $this->link(), null, null, null, true);
      $this->template()->addTplFile('edit.phtml');
      Template_Module::setFullWidth(true);
      $this->edit = true;
   }

   public function getPanelsView()
   {
      print (json_encode($this->data));
   }

   public function getPanelInfoView()
   {
      print (json_encode($this->data));
   }

   public function panelSettingsView()
   {
      $this->template()->addTplFile('panel_settings.phtml');
      if (file_exists(AppCore::getAppLibDir() . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR
              . $this->moduleName . DIRECTORY_SEPARATOR . Template::TEMPLATES_DIR
              . DIRECTORY_SEPARATOR . 'panel_settings.phtml')) {
         $tpl = new Template_Module($this->link(), $this->category());
         $tpl->addTplFile('panel_settings.phtml', $this->moduleName);
         $this->includeTpl = $tpl;
      }
      Template_Navigation::addItem($this->tr('Nastavení panelu'), $this->link(), null, null, null, true);
      Template_Module::setEdit(true);
   }

   public function getListPanelsView()
   {
      $this->createPanelsToolboxes($this->panels);
      $this->template()->addTplFile('listtpl.phtml');
      Template_Module::setFullWidth(true);
   }

}