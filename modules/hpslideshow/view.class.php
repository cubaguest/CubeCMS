<?php
class HPSlideShow_View extends View {
   public function mainView() {
      $this->template()->addFile('tpl://main.phtml');
      Template_Module::setEdit(true);
   }

   /**
    * Metoda vrátí šablonu pro slideshow
    * return Template_Module
    */
   public static function getSlideshow($assignCss = true)
   {
      $tpl = new Template_Module(new Url_Link_Module(), new Category());

      $model = new HPSlideShow_Model();
      $images = $model
         ->where(HPSlideShow_Model::COLUMN_ACTIVE." = 1", array())
         ->joinFK(HPSlideShow_Model::COLUMN_ID_CAT)
         ->order(HPSlideShow_Model::COLUMN_ORDER)
         ->records();

      if($images){
         $tpl->images = $images;
         $tpl->imagesUrl = Url_Link::getWebURL().VVE_DATA_DIR."/hpslideshow/";
         $tpl->addFile('tpl://hpslideshow:slideshow.phtml');
         $tpl->addFile('js://hpslideshow:slider.js');
         if($assignCss){
            $tpl->addFile('css://hpslideshow:style.less');
         }
      }
      return $tpl;
   }
}
