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
      $this->setTinyMCE($this->formEdit->text, 'advanced');
      $this->setTinyMCE($this->formEdit->textPanel, 'advanced', array('height' => 300));
      Template_Module::setEdit(true);
   }
}