<?php
class Actionswgal_View extends Actions_View {
   public function showView() {
      $this->template()->addFile("tpl://detail.phtml");
      $this->createDetailToolbox();
      $this->addMetaTags($this->action);
      if($this->toolbox instanceof Template_Toolbox2){
         $pView = new Photogalery_View($this->pCtrl);
         $pView->addImagesToolbox();
      }
   }

   public function showPhotosView() {
      $this->template()->addFile("tpl://listPhotos.phtml");
      $this->showView();
   }

   public function archiveView() {
      $this->template()->addFile("tpl://actions:archive.phtml");
   }

   public function showDataView() {
      switch ($this->output) {
         case 'pdf':
            $c = $this->createPdf($this->action);
            // doplnění fotek
            $photosM = new PhotoGalery_Model_Images();
            $images = $photosM->getImages($this->category()->getId(), $this->action->{Actions_Model::COLUMN_ID});

            $c->pdf()->AddPage();
            // nadpis
            $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, 'B', VVE_PDF_FONT_SIZE_MAIN);
            $c->pdf()->writeHTML("<h2>".$this->_('Fotky')."</h2>", true, 0, true, 0);
            $c->pdf()->Ln(5);
            // přibalení obrázků s galerie

            $c->pdf()->setJPEGQuality(75); // set JPEG quality
            $row = $coll = $maxHeight = 0;

            while ($image = $images->fetch()) {
               $sizes = getimagesize($this->category()->getModule()->getDataDir()
                       .$this->action[Actions_Model::COLUMN_URLKEY][Locales::getDefaultLang()].DIRECTORY_SEPARATOR
                       .Photogalery_Controller::DIR_MEDIUM
                       .DIRECTORY_SEPARATOR.$image->{PhotoGalery_Model_Images::COLUMN_FILE});

               if($sizes[0] > 80/0.27) {
                  $width = 80;
               } else {
                  $width = $sizes[0]*0.27;
               } // převody mezi px<>mm

               $c->pdf()->Image($this->category()->getModule()->getDataDir()
                       .$this->action[Actions_Model::COLUMN_URLKEY][Locales::getDefaultLang()].DIRECTORY_SEPARATOR
                       .Photogalery_Controller::DIR_MEDIUM.DIRECTORY_SEPARATOR.$image->{PhotoGalery_Model_Images::COLUMN_FILE},
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
            $c->flush($this->action->{Actions_Model::COLUMN_URLKEY}.'.pdf');

            break;
         case 'xml':
         default:
            $c = $this->createActionXml($this->action);
            break;
      }
   }
}

?>