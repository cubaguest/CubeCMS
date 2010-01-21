<?php
class Actionswgal_View extends Actions_View {
   public function init() {
      $this->template()->addCssFile("style.css");
   }


   public function mainView() {
      $this->template()->addTplFile("list.phtml", 'actions');
   }

   public function showView(){
      $this->template()->addTplFile("detail.phtml");
   }

   public function showPhotosView(){
      $this->template()->addTplFile("listPhotos.phtml");
      $this->showView();
   }

   public function editphotosView(){
      $this->template()->addTplFile("addimage.phtml", 'photogalery');
      $this->template()->addTplFile("editphotos.phtml", 'photogalery');
   }

   public function editphotoView(){
      $this->template()->addTplFile("editphoto.phtml", 'photogalery');
   }

   public function uploadFileView() {}
   public function checkFileView() {}

   

   public function archiveView(){
      $this->template()->addTplFile("archive.phtml", 'actions');
   }

   /**
    * Viewer pro přidání novinky
    */
   public function addView() {
      $this->editView();
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      $this->template()->addTplFile('edit.phtml', 'actions');
   }

   public function showPdfView() {
      // načtení článku
      $model = new Actions_Model_Detail();
      $action = $model->getAction($this->urlkey);

      if($action == false) return false;

      $c = $this->createPdf($action);

      // doplnění fotek
      $photosM = new PhotoGalery_Model_Images();
      $images = $photosM->getImages($this->category()->getId(), $action->{Actions_Model_Detail::COLUMN_ID});


      $c->pdf()->AddPage();
      // nadpis
      $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, 'B', VVE_PDF_FONT_SIZE_MAIN);
      $c->pdf()->writeHTML("<h2>".$this->_('Fotky')."</h2>", true, 0, true, 0);
      $c->pdf()->Ln(5);
      // přibalení obrázků s galerie

      $c->pdf()->setJPEGQuality(75); // set JPEG quality
      $row = $coll = $maxHeight = 0;

      while ($image = $images->fetch()) {
         $sizes = getimagesize($this->category()->getModule()->getDataDir().Photogalery_Controller::DIR_MEDIUM
                 .DIRECTORY_SEPARATOR.$image->{PhotoGalery_Model_Images::COLUMN_FILE});

         if($sizes[0] > 80/0.27){ $width = 80; } else { $width = $sizes[0]*0.27; } // převody mezi px<>mm

         $c->pdf()->Image($this->category()->getModule()->getDataDir().Photogalery_Controller::DIR_MEDIUM
                 .DIRECTORY_SEPARATOR.$image->{PhotoGalery_Model_Images::COLUMN_FILE},
                 $coll*100+VVE_PDF_MARGIN_LEFT, '', $width);

         $height = $sizes[1] * ($width/$sizes[0]);
         if($height > $maxHeight) $maxHeight = $height;

         $coll++;
         if($coll == 2) {
            $c->pdf()->Ln(($maxHeight)+3);
            $row++;
            $coll = $maxHeight = 0;
         }
      }

      // výstup
      $c->flush($action->{Actions_Model_Detail::COLUMN_URLKEY}.'.pdf');
   }
}

?>