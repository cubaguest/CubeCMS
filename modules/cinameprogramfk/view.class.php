<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class CinameProgramFk_View extends View {
   public function mainView() {
      $model = new Text_Model_Detail();
      $this->text = $model->getText($this->category()->getId());

      $this->template()->addTplFile("list.phtml");

      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox();
         $toolbox->addTool('edit_text', $this->_("Upravit text"),
                 $this->link()->route('edittext'),
                 $this->_("Upravit text galerie"), "page_edit.png");
         $this->toolbox = $toolbox;
      }
   }

   public function editTextView() {
      $this->template()->addTplFile("edittext.phtml");
   }
}

?>