<?php
class Photogalery_View extends View {
	/**
	 * Inicializace
	 */
	public function init() {
//		$this->template()->addCss('style.css');
	}
	
	public function editimagesView() {
      $this->view()->template()->addTplFile("editimages.phtml");
   }

   public function uploadFileView() {}
   public function checkFileView() {}

   public function editphotoView() {
      $this->view()->template()->addTplFile("editphoto.phtml");
   }
}

?>