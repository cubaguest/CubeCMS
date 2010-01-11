<?php
class Photogalerymed_View extends Articles_View {
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


   public function editphotosView() {
      $this->template()->addPageTitle($this->template()->article->{Articles_Model_Detail::COLUMN_NAME}
         ." - ".$this->_('úprava obrázků'));
      $this->template()->addPageHeadline($this->template()->article->{Articles_Model_Detail::COLUMN_NAME}
         ." - ".$this->_('úprava obrázků'));
      $this->template()->addTplFile('addimage.phtml', 'photogalery');
      $this->template()->addTplFile('editphotos.phtml', 'photogalery');
   }

   public function editphotoView() {
      $this->template()->addTplFile("editphoto.phtml", 'photogalery');
   }
}

?>