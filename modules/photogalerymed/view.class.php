<?php
class Photogalerymed_View extends Articles_View {
   /**
    * Inicializace
    */
   public function mainView() {
      $this->createListToolbox();
      if($this->toolbox instanceof Template_Toolbox2){
         $this->toolbox->add_article->setLabel($this->_('Přidat galerii'));
         $this->toolbox->add_article->setTitle($this->_('Přidat novou galerii'));
      }
      $this->template()->addFile('tpl://'.$this->category()->getParam(Photogalerymed_Controller::PARAM_TPL_LIST, 'list.phtml'));
   }

   public function edittextView() {
      $this->template()->addFile('tpl://edittext.phtml');
   }

   public function showView() {
      $this->createDetailToolbox();

      if($this->toolbox instanceof Template_Toolbox2){
         $this->toolbox->article_->setConfirmMeassage($this->_('Opravdu smazat galerii?'));
         $this->toolbox->edit_article->setTitle($this->_('Upravit galerii'))->setLabel($this->_('Upravit galerii'));

         $toolbox = new Template_Toolbox2();
         $toolbox->setIcon(Template_Toolbox2::ICON_IMAGE_WRENCH);
         $tool = new Template_Toolbox2_Tool_PostRedirect('edit_galery', $this->_("Upravit fotky"),
         $this->link()->route('editphotos'));
         $tool->setIcon('image_edit.png')->setTitle($this->_('Upravit fotky galerie'));
         $toolbox->addTool($tool);
         $this->toolboxImages = $toolbox;
      }
      $this->template()->addFile('tpl://'.$this->category()->getParam(Photogalerymed_Controller::PARAM_TPL_DETAIL, 'detail.phtml'));
   }

   public function addView() {
      $this->editView();
   }

   public function editphotosView() {
      $this->template()->addPageTitle($this->template()->article->{Articles_Model_Detail::COLUMN_NAME}
              ." - ".$this->_('úprava obrázků'));
      $this->template()->addFile('tpl://photogalery:addimage.phtml');
      $this->template()->addFile('tpl://photogalery:editphotos.phtml');
   }

   public function editphotoView() {
      $this->template()->addFile('tpl://photogalery:editphoto.phtml');
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
      $this->template()->addFile('tpl://contentdetail.phtml');
   }
}

?>