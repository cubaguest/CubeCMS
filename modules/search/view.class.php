<?php
class Search_View extends View {
   public function mainView() {
      $this->template()->addTplFile("search.phtml");

      if($this->category()->getRights()->isControll()) {
         $toolbox = new Template_Toolbox();
         $toolbox->addTool('edit_search', $this->_("Upravit hledání"),
            $this->link()->route('editsapi'),
            $this->_("Upravit parametry hledání"), "magnifier_zoom_in.png");
         $this->toolbox = $toolbox;
      }
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
