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
      parent::showView();
      if($this->toolbox instanceof Template_Toolbox2){
         $this->toolbox->article_->setConfirmMeassage($this->tr('Opravdu smazat galerii?'));
         $this->toolbox->edit_article->setTitle($this->tr('Upravit galerii'))->setLabel($this->tr('Upravit galerii'));
      }
      Template_Navigation::addItem($this->article->{Articles_Model::COLUMN_NAME}, $this->link());
   }
   
   public function exportArticleHtmlView() {
      $this->template()->addFile('tpl://contentdetail.phtml');
   }
}

?>