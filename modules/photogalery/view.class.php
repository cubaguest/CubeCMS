<?php
class Photogalery_View extends View {
   public function mainView() {
      $this->template()->addTplFile("list.phtml");
      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox();
         $toolbox->addTool('edit_text', $this->_("Upravit text"),
                 $this->link()->route('edittext'),
                 $this->_("Upravit text galerie"), "page_edit.png");
         $toolbox->addTool('edit_images', $this->_("Upravit fotky"),
                 $this->link()->route('editphotos'),
                 $this->_("Upravit fotky galerie"), "image_edit.png");
         $this->template()->toolbox = $toolbox;
      }
   }

   public function editphotosView() {
      $this->template()->addPageTitle($this->_('úprava obrázků'));
      $this->template()->addPageHeadline($this->_('úprava obrázků'));

      $this->template()->addTplFile("addimage.phtml");
//      $this->template()->addTplFile("testaddform.phtml");
      $this->template()->addTplFile("editphotos.phtml");

   }

   public function uploadFileView() {

   }
   public function checkFileView() {

   }

   public function editphotoView() {
      $this->template()->addTplFile("editphoto.phtml");
   }
}

?>