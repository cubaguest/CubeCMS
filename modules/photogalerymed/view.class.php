<?php
class Photogalerymed_View extends Articles_View {
   /**
    * Inicializace
    */
   public function mainView() {
      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox();
         $toolbox->addTool('add_galery', $this->_("Přidat galerii"),
                 $this->link()->route('add'),
                 $this->_("Přidat galerii"), "image_add.png");
         $this->template()->toolbox = $toolbox;
      }

      $this->template()->addTplFile("list.phtml");
   }

   public function contentView() {
      $this->template()->addTplFile("contentlist.phtml");
   }

   public function edittextView() {
      $this->template()->addTplFile("edittext.phtml");
   }

   public function showView() {
      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox();
         $toolbox->addTool('edit_galery', $this->_("Upravit galerii"),
                 $this->link()->route('edittext', array('urlkey' => $this->article->{Articles_Model_Detail::COLUMN_URLKEY})),
                 $this->_("Upravit galerii"), "page_edit.png");
         $toolbox->addTool('edit_images', $this->_("Upravit fotky"),
                 $this->link()->route('editphotos', array('urlkey' => $this->article->{Articles_Model_Detail::COLUMN_URLKEY})),
                 $this->_("Upravit fotky galerie"), "image_edit.png");
         $toolbox->addTool('article_delete', $this->_("Smazat"),
                 $this->link(), $this->_("Smazat zobrazenou galerii"), "page_delete.png",
                 'article_id', (int)$this->article->{Articles_Model_Detail::COLUMN_ID},
                 $this->_('Opravdu smazat galerii?'));
         $this->template()->toolbox = $toolbox;
      }
      $this->template()->addTplFile("detail.phtml");
   }

   public function addView() {
      $this->template()->addTplFile('edittext.phtml');
   }

   public function editphotosView() {
      $this->template()->addPageTitle($this->template()->article->{Articles_Model_Detail::COLUMN_NAME}
              ." - ".$this->_('úprava obrázků'));
      $this->template()->addPageHeadline($this->template()->article->{Articles_Model_Detail::COLUMN_NAME}
              ." - ".$this->_('úprava obrázků'));
      $this->template()->addTplFile('addimage.phtml', 'photogalery');
//      $this->template()->addTplFile('testaddform.phtml', 'photogalery');
      $this->template()->addTplFile('editphotos.phtml', 'photogalery');
   }

   public function editphotoView() {
      $this->template()->addTplFile("editphoto.phtml", 'photogalery');
   }

   protected function createPdf() {
      $article = $this->article;
      if($article == false) return false;
      $c = parent::createPdf();

      // doplnění fotek
      $photosM = new PhotoGalery_Model_Images();
      $images = $photosM->getImages($this->category()->getId(), $article->{Articles_Model_Detail::COLUMN_ID});
//      $c->pdf()->AddPage();
      // nadpis
      $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, 'B', VVE_PDF_FONT_SIZE_MAIN);
      $c->pdf()->writeHTML("<h2>".$this->_('Fotky')."</h2>", true, 0, true, 0);
      $c->pdf()->Ln(5);
      // přibalení obrázků s galerie

      $c->pdf()->setJPEGQuality(75); // set JPEG quality
      $row = $coll = $maxHeight = 0;

      while ($image = $images->fetch()) {
         $sizes = getimagesize($this->category()->getModule()->getDataDir()
                 .$article[Articles_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()]
                 .DIRECTORY_SEPARATOR.Photogalery_Controller::DIR_MEDIUM
                 .DIRECTORY_SEPARATOR.$image->{PhotoGalery_Model_Images::COLUMN_FILE});

         if($sizes[0] > 80/0.27) {
            $width = 80;
         } else {
            $width = $sizes[0]*0.27;
         } // převody mezi px<>mm

         $c->pdf()->Image($this->category()->getModule()->getDataDir()
                 .$article[Articles_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()]
                 .DIRECTORY_SEPARATOR.Photogalery_Controller::DIR_MEDIUM
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
      return $c;
   }

   public function checkFileView() {

   }
   public function uploadFileView() {

   }

   public function exportArticleHtmlView() {
      $this->template()->addTplFile('contentdetail.phtml');
   }
}

?>