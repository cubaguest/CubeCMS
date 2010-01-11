<?php
class Photogalery_View extends View {
	/**
	 * Inicializace
	 */
	public function init() {
//		$this->template()->addCss('style.css');
	}

	public function mainView() {
      $this->template()->addTplFile("list.phtml");
	}
	
	public function editphotosView() {
      $this->template()->addPageTitle($this->_('úprava obrázků'));
      $this->template()->addPageHeadline($this->_('úprava obrázků'));

      $this->template()->addTplFile("addimage.phtml");
      $this->template()->addTplFile("editphotos.phtml");

   }

   public function uploadFileView() {}
   public function checkFileView() {}

   public function editphotoView() {
      $this->template()->addTplFile("editphoto.phtml");
   }
}

?>