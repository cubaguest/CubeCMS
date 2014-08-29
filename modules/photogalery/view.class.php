<?php
class Photogalery_View extends View {
   public function mainView() {
      $this->template()->addFile($this->getTemplate());
      if($this->category()->getRights()->isWritable()) {
         $this->toolboxText = new Template_Toolbox2();
         $this->toolboxText->setIcon(Template_Toolbox2::ICON_PEN);
         $toolEditText = new Template_Toolbox2_Tool_PostRedirect('edit_text', $this->tr("Upravit text"),
         $this->link()->route('edittext'));
         $toolEditText->setIcon('page_edit.png')->setTitle($this->tr('Upravit text galerie'));
         $this->toolboxText->addTool($toolEditText);

         $this->toolbox = $this->toolboxText;
         
         if($this->text != false){
            $toolLangLoader = new Template_Toolbox2_Tool_LangLoader($this->text->{Text_Model::COLUMN_TEXT});
            $this->toolboxText->addTool($toolLangLoader);
         }
         
         if(isset ($_GET['l']) AND isset ($this->text[Text_Model::COLUMN_TEXT][$_GET['l']])){
            $l = $_GET['l'];
            $this->text->{Text_Model::COLUMN_TEXT} = $this->text[Text_Model::COLUMN_TEXT][$l];
            if($this->text[Text_Model::COLUMN_LABEL][$l] != null){
               $this->text->{Text_Model::COLUMN_LABEL} = $this->text[Text_Model::COLUMN_LABEL][$l];
            } else {
               $obj = Category::getSelectedCategory()->getCatDataObj();
               $this->text->{Text_Model::COLUMN_LABEL} = $obj[Model_Category::COLUMN_NAME][$l];
               unset ($obj);
            }
         }
      }
      $this->addImagesToolbox();
   }
   
   public function addImagesToolbox()
   {
      if($this->category()->getRights()->isWritable()){
         $toolbox = new Template_Toolbox2();
         $toolbox->setIcon(Template_Toolbox2::ICON_IMAGE_WRENCH);
         $toolEditImages = new Template_Toolbox2_Tool_PostRedirect('edit_images', $this->tr("Upravit obrázky"),
         $this->link()->route('editphotos'));
         $toolEditImages->setIcon('image_edit.png')->setTitle($this->tr('Upravit obrázky'));
         $toolbox->addTool($toolEditImages);
         
//         $toolSortImages = new Template_Toolbox2_Tool_PostRedirect('sort_images', $this->tr("Řadit obrázky"),
//         $this->link()->route('sortphotos'));
//         $toolSortImages->setIcon(Template_Toolbox2::ICON_MOVE)->setTitle($this->tr('Řadit obrázky'));
//         $toolbox->addTool($toolSortImages);
         $this->template()->toolboxImages = $toolbox;
      }
   }

   public function editphotosView() {
      Template_Module::setEdit(true);
      if((string)$this->name != null){
         Template_Navigation::addItem($this->name, $this->link()->route('detail'));
         $this->template()->addPageTitle($this->name);
      }
      
      $this->template()->addPageTitle($this->tr('úprava obrázků'));
      $this->template()->addFile("tpl://photogalery:editphotos.phtml");
      Template_Navigation::addItem($this->tr('Úprava obrázků'), $this->link());
      
      $toolbox = new Template_Toolbox2();
      $toolbox->setIcon(Template_Toolbox2::ICON_IMAGE_WRENCH);
      
      $toolShow = new Template_Toolbox2_Tool_Button('preview', $this->tr("Náhled"));
      $toolShow->setIcon('search')->setTitle($this->tr('Náhled'));
      $toolbox->addTool($toolShow);
      
      $toolEditLabels = new Template_Toolbox2_Tool_Button('edit_texts', $this->tr("Upravit text"));
      $toolEditLabels->setIcon(Template_Toolbox2::ICON_PEN)->setTitle($this->tr('Upravit texty'));
      $toolbox->addTool($toolEditLabels);
      
      $toolRotateLeft = new Template_Toolbox2_Tool_Button('rotate_left', $this->tr("Otočit doleva"));
      $toolRotateLeft->setIcon('rotate-left')->setTitle($this->tr('Otočit doleva'));
      $toolbox->addTool($toolRotateLeft);
      
      $toolRotateRight = new Template_Toolbox2_Tool_Button('rotate_right', $this->tr("Otočit doleva"));
      $toolRotateRight->setIcon('rotate-right')->setTitle($this->tr('Otočit doleva'));
      $toolbox->addTool($toolRotateRight);
      
      
      $toolDelete = new Template_Toolbox2_Tool_Button('delete', $this->tr("Smazat"));
      $toolDelete->setIcon('delete')->setTitle($this->tr('Smazat'));
//      $toolDelete->setConfirmMeassage($this->tr('Opravdu smazat?'));
      $toolDelete->setImportant(true);
      $toolDelete->setIcon(Template_Toolbox2::ICON_DELETE);
      $toolbox->addTool($toolDelete);
      
      $this->imageToolbox = $toolbox;
   }

   public function sortphotosView() {
      Template_Module::setEdit(true);
      if((string)$this->name != null){
         Template_Navigation::addItem($this->name, $this->link()->route('detail'));
         $this->template()->addPageTitle($this->name);
      }
      $this->template()->addPageTitle($this->tr('úprava pořadí obrázků'));
      $this->template()->addFile("tpl://photogalery:sortphotos.phtml");
      Template_Navigation::addItem($this->tr('Řazení obrázků'), $this->link());
   }

   public function imageUploadView() {
      $this->template()->addFile("tpl://photogalery:upload-complete.phtml");
   }
   
   public function checkFileView() {}

   public function editphotoView() {
      Template_Module::setEdit(true);
      $this->template()->addFile("tpl://photogalery:editphoto.phtml");
   }

   public function edittextView() {
      Template_Module::setEdit(true);
      $this->setTinyMCE($this->form->text, $this->category()->getParam(Photogalery_Controller::PARAM_EDITOR_TYPE, 'advanced'));
      $this->template()->addFile("tpl://edittext.phtml");
      Template_Navigation::addItem($this->tr('Úprava úvodního textu'), $this->link());
   }
}

?>