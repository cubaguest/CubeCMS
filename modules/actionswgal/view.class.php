<?php
class Actionswgal_View extends Actions_View {
   public function showView() {
      $this->template()->addTplFile("detail.phtml");
      $this->createDetailToolbox();
      if($this->toolbox instanceof Template_Toolbox2){
         $this->toolboxPhotos = new Template_Toolbox2();
         $this->toolboxPhotos->setIcon(Template_Toolbox2::ICON_IMAGE_WRENCH);
         $toolPhotos = new Template_Toolbox2_Tool_PostRedirect('edit_action_photos', 
            $this->_("Upravit fotky"), $this->link()->route('editphotos'));
         $toolPhotos->setIcon('image_edit.png')->setTitle($this->_('Upravit fotky k akci'));
         $this->toolboxPhotos->addTool($toolPhotos);
      }
   }

   public function showPhotosView() {
      $this->template()->addTplFile("listPhotos.phtml");
      $this->showView();
   }

   public function editphotosView() {
      $this->template()->addTplFile("addimage.phtml", 'photogalery');
      $this->template()->addTplFile("editphotos.phtml", 'photogalery');
   }

   public function editphotoView() {
      $this->template()->addTplFile("editphoto.phtml", 'photogalery');
   }

   public function uploadFileView() {

   }
   public function checkFileView() {

   }

   public function archiveView() {
      $this->template()->addTplFile("archive.phtml", 'actions');
   }

   public function showDataView() {
      switch ($this->output) {
         case 'pdf':
            $c = $this->createPdf($this->action);
            // doplnění fotek
            $photosM = new PhotoGalery_Model_Images();
            $images = $photosM->getImages($this->category()->getId(), $this->action->{Actions_Model_Detail::COLUMN_ID});

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
                       .$this->action[Actions_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()].DIRECTORY_SEPARATOR
                       .Photogalery_Controller::DIR_MEDIUM
                       .DIRECTORY_SEPARATOR.$image->{PhotoGalery_Model_Images::COLUMN_FILE});

               if($sizes[0] > 80/0.27) {
                  $width = 80;
               } else {
                  $width = $sizes[0]*0.27;
               } // převody mezi px<>mm

               $c->pdf()->Image($this->category()->getModule()->getDataDir()
                       .$this->action[Actions_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()].DIRECTORY_SEPARATOR
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
            $c->flush($this->action->{Actions_Model_Detail::COLUMN_URLKEY}.'.pdf');

            break;
         case 'xml':
         default:
            $c = $this->createActionXml($this->action);
            break;
      }
   }

   public function editLabelView() {
      $this->template()->addTplFile('editlabel.phtml','actions');
   }
}

?>