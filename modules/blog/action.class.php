<?php
/**
 * Třída pro obsluhu akcí v modulu
 *
 */
class ModuleAction extends Action {
	

	public function actions() {
		$this->addAction("addsection", "as");
		$this->addAction("addblog", "ab");
		
		$this->addAction("editsection", "es");
		$this->addAction("editblog", "eb");
	}
	
}
?>