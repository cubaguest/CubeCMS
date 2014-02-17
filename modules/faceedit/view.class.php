<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class FAceEdit_View extends View {
	public function mainView() {
      $this->template()->addFile('tpl://main.phtml');
      Template_Module::setFullWidth(true);
   }

	public function editFileView() {
      $this->template()->addFile('tpl://edit.phtml');
      Template_Module::setFullWidth(true);
      Template_Navigation::addItem(sprintf($this->tr('Soubor %s'), $this->template()->fileName), $this->link(), null, null, null, true);
   }
   
}