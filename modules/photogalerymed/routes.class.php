<?php
class Photogalerymed_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('add', "add", 'add', "add/");
      $this->addRoute('edittext', "::urlkey::/edittext", 'edit','{urlkey}/edittext/');
      $this->addRoute('editphoto', "::urlkey::/editphotos/editphoto-::id::", 'editphoto','{urlkey}/editphotos/editphoto-{id}/');
      $this->addRoute('editphotos', "::urlkey::/editphotos", 'editphotos','{urlkey}/editphotos/');

      $this->addRoute('detail', "::urlkey::", 'show','{urlkey}/');
      $this->addRoute('detailpdf', "::urlkey::.pdf", 'showPdf','{urlkey}.pdf');

      $this->addRoute('uploadFile', "::urlkey::/uploadFile.php", 'uploadFile','{urlkey}/uploadFile.php');
      $this->addRoute('checkFile', "::urlkey::/checkFile.php", 'checkFile','{urlkey}/checkFile.php');

      $this->addRoute('normal', null, 'main', null);
	}
}

?>