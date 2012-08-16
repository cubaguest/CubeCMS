<?php

/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */
class CatsBulkEdit_View extends View {

   public function mainView() {
      $this->template()->addTplFile('main.phtml');
      Template_Module::setEdit(true);
   }

   public function editView() {
      Template_Module::setEdit(true);
      $this->template()->addTplFile('edit.phtml');
      Template_Navigation::addItem(sprintf( $this->tr('Úprava parametru %s'), $this->paramName ), $this->link(), null, null, null, true);
   }

}
?>