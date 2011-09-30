<?php
class PhotogaleryMed_View extends ArticlesWGal_View {
   
   /**
    * Inicializace
    */
   public function mainView() {
      parent::mainView();
      if($this->toolbox instanceof Template_Toolbox2){
         $this->toolbox->add_article->setLabel($this->tr('Přidat galerii'));
         $this->toolbox->add_article->setTitle($this->tr('Přidat novou galerii'));
      }
   }

   public function showView() {
      $this->createDetailToolbox();

      if($this->toolbox instanceof Template_Toolbox2){
         $this->toolbox->article_->setConfirmMeassage($this->tr('Opravdu smazat galerii?'));
         $this->toolbox->edit_article->setTitle($this->tr('Upravit galerii'))->setLabel($this->tr('Upravit galerii'));

         $pView = new Photogalery_View($this->pCtrl);
         $pView->addImagesToolbox();
      }
      $this->template()->addFile('tpl://'.$this->category()->getParam(Photogalerymed_Controller::PARAM_TPL_DETAIL, 'detail.phtml'));
   }
   
   public function exportArticleHtmlView() {
      $this->template()->addFile('tpl://contentdetail.phtml');
   }
}

?>