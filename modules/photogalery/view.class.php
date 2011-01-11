<?php
class Photogalery_View extends View {
   public function mainView() {
      $this->template()->addTplFile("list.phtml");
      if($this->category()->getRights()->isWritable()) {
         $this->toolboxText = new Template_Toolbox2();
         $this->toolboxText->setIcon(Template_Toolbox2::ICON_PEN);
         $toolEditText = new Template_Toolbox2_Tool_PostRedirect('edit_text', $this->_("Upravit text"),
         $this->link()->route('edittext'));
         $toolEditText->setIcon('page_edit.png')->setTitle($this->_('Upravit text galerie'));
         $this->toolboxText->addTool($toolEditText);

         $toolbox = new Template_Toolbox2();
         $toolbox->setIcon(Template_Toolbox2::ICON_IMAGE_WRENCH);
         $toolEditText = new Template_Toolbox2_Tool_PostRedirect('edit_text', $this->_("Upravit fotky"),
         $this->link()->route('editphotos'));
         $toolEditText->setIcon('image_edit.png')->setTitle($this->_('Upravit fotky'));
         $toolbox->addTool($toolEditText);

         if($this->category()->getRights()->isControll()){
            $this->toolboxText->setIcon(Template_Toolbox2::ICON_WRENCH);
            $toolEView = new Template_Toolbox2_Tool_PostRedirect('edit_view', $this->tr("Nastavení"),
            $this->link()->route(Routes::MODULE_SETTINGS));
            $toolEView->setIcon('wrench.png')->setTitle($this->tr('Upravit nastavení kategorie'));
            $this->toolboxText->addTool($toolEView);
         }

         $this->toolboxImages = $toolbox;
      }
   }

   public function editphotosView() {
      Template_Module::setEdit(true);
      $this->template()->addPageTitle($this->_('úprava obrázků'));
      $this->template()->addPageHeadline($this->_('úprava obrázků'));

      $this->template()->addTplFile("addimage.phtml");
//      $this->template()->addTplFile("testAddform.phtml");
//      $this->template()->addTplFile("editphotos.phtml");

   }

   public function uploadFileView() {

   }
   public function checkFileView() {

   }

   public function editphotoView() {
      Template_Module::setEdit(true);
      $this->template()->addTplFile("editphoto.phtml");
   }
}

?>