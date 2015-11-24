<?php
class Search_View extends View {
   public function mainView() {
      $this->template()->addTplFile("search.phtml");

      if($this->category()->getRights()->isControll()) {
         $toolbox = new Template_Toolbox2();
         $toolbox->setIcon(Template_Toolbox2::ICON_PEN);
         
         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('edit_search', $this->tr("Upravit hledání"),
                 $this->link()->route('editsapi'));
         $toolEdit->setIcon('page_edit.png')->setTitle($this->tr("Upravit hledání"));
         $toolbox->addTool($toolEdit);
         
         $this->toolbox = $toolbox;
      }
      Template_Navigation::addItem($this->tr('Hledání'), $this->link()->clear());
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editsapiView() {
      $this->template()->addPageTitle($this->tr('úprava api hledání'));
      $this->template()->addTplFile("list.phtml");
   }
}
