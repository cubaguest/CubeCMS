<?php
class HPSlideShowAdv_View extends View {
   public function mainView() {
      $this->template()->addFile('tpl://main.phtml');
      Template_Module::setEdit(true);
      if($this->formEdit && $this->category()->getParam('wysiwyg', false)){
         $this->setTinyMCE($this->formEdit->label);
      }
      $this->createSlideToolboxes();
   }

   
   public function addSlideView()
   {
      $this->template()->addFile('tpl://edit.phtml');
   }
   
   public function editSlideView()
   {
      $this->template()->addFile('tpl://edit.phtml');
      
      
   }
   
   protected function createSlideToolboxes()
   {
      if(!$this->slides){
         return;
      }
      $toolbox = new Template_Toolbox2();
      $toolbox->setTemplate(Template_Toolbox2::TEMPLATE_INLINE);

      $toolState = new Template_Toolbox2_Tool_Button('changeState', $this->tr('Změnit stav'));
      $toolState->setIcon(Template_Toolbox2::ICON_ENABLE);
      $toolbox->addTool($toolState);
      
      $toolEdit = new Template_Toolbox2_Tool_PostRedirect('edit', $this->tr('Úprava slajdu'));
      $toolEdit->setIcon(Template_Toolbox2::ICON_PEN);
      $toolbox->addTool($toolEdit);

      $toolRemove = new Template_Toolbox2_Tool_Form($this->formRemove);
      $toolRemove->setIcon(Template_Toolbox2::ICON_DELETE);
      $toolRemove->setImportant(true);
      $toolRemove->setConfirmMeassage($this->tr('Opravdu smazat slajd?'));
      $toolbox->addTool($toolRemove);

      foreach ($this->slides as $slide) {
         $toolbox->edit->setAction($this->link()->clear()->route('editSlide', array('id' => $slide->getPK())));
         $toolbox->slideDelete->getForm()->setAction($this->link()->clear()->route());
         $toolbox->slideDelete->getForm()->id->setValues($slide->getPK());
         $toolbox->changeState->setIcon($slide->{HPSlideShowAdv_Model::COLUMN_ACTIVE} ? Template_Toolbox2::ICON_ENABLE : Template_Toolbox2::ICON_DISABLE);

         $slide->toolbox = clone $toolbox;
      }
   }
   
   /**
    * Metoda vrátí šablonu pro slideshow
    * return Template_Module
    */
   public static function getSlideshow($assignCss = true)
   {
      $tpl = new Template_Module(new Url_Link_Module(), new Category());

      $model = new HPSlideShowAdv_Model();
      $slides = $model
         ->where(HPSlideShowAdv_Model::COLUMN_ACTIVE." = 1", array())
         ->order(HPSlideShowAdv_Model::COLUMN_ORDER)
         ->records();

      if($slides){
         $tpl->slides = $slides;
         $tpl->imagesUrl = Url_Link::getWebURL().VVE_DATA_DIR."/".HPSlideShowAdv_Controller::DATA_DIR."/";
         $tpl->addFile('tpl://hpslideshowadv:slideshow.phtml');
         $tpl->addFile('js://hpslideshowadv:slider.js');
         if($assignCss){
            $tpl->addFile('css://hpslideshowadv:style.less');
         }
      }
      return $tpl;
   }
}
