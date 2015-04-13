<?php
class Contact_View extends View {
   public function mainView() {
      if($this->rights()->isWritable()) {
         $this->toolbox = new Template_Toolbox2();
         $this->toolbox->setIcon(Template_Toolbox2::ICON_PEN);

         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('contact_edit', $this->tr("Upravit kontakt"),
                 $this->link()->route('edit'));
         $toolEdit->setIcon('page_edit.png')->setTitle($this->tr('Upravit kontakt'));
         $this->toolbox->addTool($toolEdit);

         if($this->category()->getRights()->isControll()){
            $this->toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
            $toolEView = new Template_Toolbox2_Tool_PostRedirect('edit_view', $this->tr("Nastavení"),
            $this->link()->route(Routes::MODULE_SETTINGS));
            $toolEView->setIcon('wrench.png')->setTitle($this->tr('Upravit nastavení kategorie'));
            $this->toolbox->addTool($toolEView);
         }
      }
      $this->template()->addTplFile("main.phtml");
      $this->markers = $this->category()->getParam(Contact_Controller::PARAM_MAP_POINTS);
      $this->urlParams = $this->category()->getParam(Contact_Controller::PARAM_MAP_URL_PARAMS);
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      $this->template()->addTplFile('edit.phtml');
      $this->setTinyMCE($this->formEdit->text, 'advanced');
      $this->setTinyMCE($this->formEdit->textPanel, 'advanced', array('height' => 300));
      if(isset($this->formEdit->textFooter)){
         $this->setTinyMCE($this->formEdit->textFooter, 'advanced', array('height' => 300));
      }
      Template_Module::setEdit(true);
   }
   
   public static function getFooter($idCat = false)
   {
      // je zadáno id kategorie - vybej jej
      if($idCat){
         $category = Category_Structure::getStructure(Category_Structure::ALL)->getCategory($idCat)->getCatObj();
         $link = Url_Link::getCategoryLink($idCat);
         
         $text = $modelText
             ->where(Text_Model::COLUMN_SUBKEY." = :subkey AND ".Text_Model::COLUMN_ID_CATEGORY." = :idc",
                 array('subkey' => Contact_Controller::TEXT_KEY_FOOTER, 'idc' => $idCat))
             ->record();
         
      } 
      // není zadáno id, použij první nalezený zýznam pro kontakt
      else {
         $modelText = new Text_Model();
         
         $text = $modelText
             ->where(Text_Model::COLUMN_SUBKEY." = :subkey "
                 . " AND ".Text_Model::COLUMN_TEXT_CLEAR." != ''"
                 . " AND ".Model_Category::COLUMN_MODULE.' = \'contact\'',
                 array('subkey' => Contact_Controller::TEXT_KEY_FOOTER))
             ->joinFK(Text_Model::COLUMN_ID_CATEGORY)
             ->record();
         if($text){
            $category = Category_Structure::getStructure(Category_Structure::ALL)->getCategory($text->{Text_Model::COLUMN_ID_CATEGORY})->getCatObj();
            $link = Url_Link::getCategoryLink($text->{Text_Model::COLUMN_ID_CATEGORY});
         }
      }
      if($text && $category){
         
         $template = new Template_Module($link, $category);
         $template->addFile('tpl://footer.phtml');
         $template->text = $text;
         return $template;
      }
      return null;
   }
}