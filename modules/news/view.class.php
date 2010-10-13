<?php
class News_View extends Articles_View {
   public function mainView() {
      $this->template()->addTplFile("list.phtml");
      $this->createListToolbox();
      if($this->toolbox != null){
         $this->toolbox->add_article->setLabel($this->_('Přidat novinku'))->setTitle($this->_('Přidat novou novinku'));
      }
   }

   public function showView() {
      parent::showView();
      if($this->toolbox instanceof Template_Toolbox2){
         $this->toolbox->article_->setConfirmMeassage($this->_('Opravdu smazat novinku?'));
         $this->toolbox->edit_article->setTitle($this->_('Upravit novinku'))->setLabel($this->_('Upravit novinku'));
      }
   }
}
?>
