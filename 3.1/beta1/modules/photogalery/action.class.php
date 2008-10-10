<?php
/**
 * Třída pro obsluhu akcí v modulu
 *
 */
class ModuleAction extends Action {
	public function actions(){
		$this->addAction("addsection", "as");
		$this->addAction("addgalery", "ag");
		$this->addAction("addphotos", "ap");
		
		$this->addAction("editsection", "es");
		$this->addAction("editphoto", "ep");
		$this->addAction("editphotos", "eps");
		$this->addAction("editgalery", "eg");
	}
	
}
?>