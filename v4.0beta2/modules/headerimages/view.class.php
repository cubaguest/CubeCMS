<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class HeaderimagesView extends View {
	public function mainView() {
		if($this->container()->getData('IMAGE') != null){
			$this->template()->addTpl("image.tpl");
		}
		
		if($this->getRights()->isWritable()){
			if($this->container()->getData('IMAGE') == null){
				$this->template()->addTpl("addForm.tpl");
			} else {
				$this->template()->addTpl("delForm.tpl");
			}
			
			$this->template()->addVar('BUTTON_SEND', _("Uložit"));
			$this->template()->addVar('BUTTON_DELETE', _("Smazat"));
			$this->template()->addVar('ADD_IMAGE_LABEL', _("Přidání obrázku"));
		}
	}
	/*EOF mainView*/
}

?>