<?php
class Photogalerymed_View extends View {
	/**
	 * Inicializace
	 */
	public function init() {
//		$this->template()->addCss('style.css');
	}
	
	public function mainView() {
      $this->template()->addTplFile("list.phtml");
   }

	public function edittextView() {
      $this->template()->addTplFile("edittext.phtml");
   }

	public function showView() {
      $this->template()->addTplFile("detail.phtml");
   }

   public function addView() {
      $this->template()->addTplFile('edittext.phtml');
   }

   public function uploadFileView() {}
   public function checkFileView() {}


   public function editimagesView() {
      $this->template()->addTplFile('addimage.phtml', 'photogalery');
      $this->template()->addTplFile('editimages.phtml', 'photogalery');
   }

   public function editphotoView() {
      $this->template()->addTplFile("editphoto.phtml", 'photogalery');
   }
}

?>