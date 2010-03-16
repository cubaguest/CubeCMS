<?php
class Search_View extends View {
   public function mainView() {
      $this->template()->addTplFile("search.phtml");
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editsapiView() {
      $this->template()->addPageHeadline($this->_('úprava api hledání'));
      $this->template()->addPageTitle($this->_('úprava api hledání'));
      $this->template()->addTplFile("list.phtml");
   }
}

?>
