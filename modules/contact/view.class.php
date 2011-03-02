<?php
class Contact_View extends View {
   public function mainView() {
      if($this->rights()->isWritable()) {
         $this->toolbox = new Template_Toolbox2();
         $this->toolbox->setIcon(Template_Toolbox2::ICON_PEN);

         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('contact_edit', $this->tr("Upravit kontakt"),
                 $this->link()->route('edit'));
         $toolEdit->setIcon('page_edit.png')->setTitle($this->tr('Upravit kontakt'));
         $this->toolbox->addTool($toolEdit);

         if($this->category()->getRights()->isControll()){
            $this->toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
            $toolEView = new Template_Toolbox2_Tool_PostRedirect('edit_view', $this->tr("Nastavení"),
            $this->link()->route(Routes::MODULE_SETTINGS));
            $toolEView->setIcon('wrench.png')->setTitle($this->tr('Upravit nastavení kategorie'));
            $this->toolbox->addTool($toolEView);
         }
      }
      $this->template()->addTplFile("main.phtml");
      $this->markers = $this->category()->getParam(Contact_Controller::PARAM_MAP_POINTS);
      $this->urlParams = $this->category()->getParam(Contact_Controller::PARAM_MAP_URL_PARAMS);
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      $this->template()->addTplFile('edit.phtml');
      $this->formEdit->text->html()->addClass("mceEditor");
      $this->addTinyMCE('mceEditor');
      $this->formEdit->textPanel->html()->addClass("mceEditorSimple");
      $this->addTinyMCE('mceEditorSimple');
      Template_Module::setEdit(true);
   }

   private function addTinyMCE($selector) {
      $this->tinyMCE = new Component_TinyMCE();
      switch ($selector) {
         case 'mceEditorSimple':
            $settings = new Component_TinyMCE_Settings_AdvSimple();
            $settings->setSetting('editor_selector', $selector);
            break;
         case 'mceEditor':
         default:
            $settings = new Component_TinyMCE_Settings_Advanced();
            $settings->setSetting('height', '600');
            break;
      }
      $this->tinyMCE->setEditorSettings($settings);
      $this->tinyMCE->mainView();
   }
}
?>