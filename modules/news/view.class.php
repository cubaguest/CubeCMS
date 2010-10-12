<?php
class News_View extends Articles_View {
   public function mainView() {
      $this->template()->addTplFile("list.phtml");
      $this->createListToolbox();
      if($this->toolbox != null){
         $this->toolbox->add_article->setLabel($this->_('Přidat novinku'))->setTitle($this->_('Přidat novou novinku'));
      }
   }
}
?>
