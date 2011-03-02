<?php
class ArticlesWGal_View extends Articles_View {
   /**
    * Inicializace
    */
   public function mainView() {
      parent::mainView();
   }

   public function showView() {
      $this->createDetailToolbox();
      if($this->toolbox instanceof Template_Toolbox2){
         $this->toolbox->article_->setConfirmMeassage($this->_('Opravdu smazat galerii?'));

         $toolbox = new Template_Toolbox2();
         $toolbox->setIcon(Template_Toolbox2::ICON_IMAGE_WRENCH);
         $tool = new Template_Toolbox2_Tool_PostRedirect('edit_galery', $this->_("Upravit fotky"),
         $this->link()->route('editphotos'));
         $tool->setIcon('image_edit.png')->setTitle($this->_('Upravit fotky galerie'));
         $toolbox->addTool($tool);
         $this->toolboxImages = $toolbox;
      }
      $this->template()->addFile("tpl://detail.phtml");
   }

   public function editphotosView() {
      $this->template()->addFile('tpl://photogalery:editphotos.phtml');
      Template_Module::setEdit(true);
   }

   public function editphotoView() {
      $this->template()->addFile("tpl://photogalery:editphoto.phtml");
      Template_Module::setEdit(true);
   }

   public function checkFileView() {

   }
   public function uploadFileView() {

   }

   /**
    * Metoda vytvoří xml s článkem a odešlě
    * @param Object $article
    */
   protected function createArticleXml() {
      $api = new Component_Api_VVEArticle();
      $api->setCategory($this->category()->getName(), $this->link()->clear());
      $article = $this->article;
      if($article != null OR $article != false){
         $image = $this->images->fetch();
         $img = null;
         if($image != false) {
            $img = $this->category()->getModule()->getDataDir(true)
                 .$article[Articles_Model_Detail::COLUMN_URLKEY][Locales::getLang()]
                 .URL_SEPARATOR.Photogalery_Controller::DIR_MEDIUM.URL_SEPARATOR
                 .$image->{PhotoGalery_Model_Images::COLUMN_FILE};
         }

         $api->setArticle($article->{Articles_Model_Detail::COLUMN_NAME},
             $this->link()->route('detail', array('urlkey'=>$article->{Articles_Model_Detail::COLUMN_URLKEY})),
             vve_tpl_truncate(vve_strip_tags($article->{Articles_Model_Detail::COLUMN_TEXT}),400), $img);
      }
      $api->flush();
   }

   protected function pdfFileCacheName() {
      return md5($this->article->{Articles_Model_Detail::COLUMN_ID}
              .'_'.(string)$this->article->{Articles_Model_Detail::COLUMN_TEXT_CLEAR}
              .'_'.$this->imagesCount).'.pdf';
   }

   /**
    * Metoda vytvoří pdf soubor
    * @param Object $article -- článek
    * @return Component_Tcpdf
    */
   protected function createPdf() {
      $article = $this->article;
      // komponenta TcPDF
      $c = parent::createPdf();

      if($this->imagesCount != 0) {
         $c->pdf()->AddPage();
         // nadpis
         $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, 'B', VVE_PDF_FONT_SIZE_MAIN);
         $c->pdf()->writeHTML("<h2>".$this->_('Fotky')."</h2>", true, 0, true, 0);
         $c->pdf()->Ln(5);
         // přibalení obrázků s galerie

         $c->pdf()->setJPEGQuality(75); // set JPEG quality
         $row = $coll = $maxHeight = 0;

         while ($image = $this->images->fetch()) {
            $sizes = getimagesize($this->category()->getModule()->getDataDir()
                    .$article[Articles_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()].DIRECTORY_SEPARATOR
                    .Photogalery_Controller::DIR_MEDIUM
                    .DIRECTORY_SEPARATOR.$image->{PhotoGalery_Model_Images::COLUMN_FILE});

            if($sizes[0] > 80/0.27) {
               $width = 80;
            } else {
               $width = $sizes[0]*0.27;
            } // převody mezi px<>mm

            $c->pdf()->Image($this->category()->getModule()->getDataDir()
                    .$article[Articles_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()].DIRECTORY_SEPARATOR
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
      }

      return $c;
   }
}

?>