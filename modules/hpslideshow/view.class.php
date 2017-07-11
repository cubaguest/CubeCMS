<?php

class HPSlideShow_View extends View {

   public function mainView()
   {
      $this->template()->addFile('tpl://main.phtml');
      Template_Module::setEdit(true);
      if ($this->formEdit && $this->category()->getParam('wysiwyg', false)) {
         $this->setTinyMCE($this->formEdit->label);
      }
   }

   /**
    * Metoda vrátí šablonu pro slideshow
    * return Template_Module
    */
   public static function getSlideshow($assignCss = true)
   {
      $tpl = new Template_Module(new Url_Link_Module(), new Category());

      $model = new HPSlideShow_Model();
      $model->where(HPSlideShow_Model::COLUMN_ACTIVE . ' = 1 '
                      . ' AND ' . HPSlideShow_Model::COLUMN_VALID_FROM . ' <=  NOW() '
                      . ' AND ( ' . HPSlideShow_Model::COLUMN_VALID_TO . ' IS NULL OR ' . HPSlideShow_Model::COLUMN_VALID_TO . ' >=  NOW())', array())
              ->joinFK(HPSlideShow_Model::COLUMN_ID_CAT)
              ->order(array(HPSlideShow_Model::COLUMN_VALID_FROM, HPSlideShow_Model::COLUMN_ORDER));
      $slides = Face::getCurrent()->getParam('', 'hpslideshow', false);

      if($slides){
         $model->limit(0, (int)$slides);
      }
      
      $images = $model->records();

      if ($images) {
         $tpl->images = $images;
         $tpl->imagesUrl = Url_Link::getWebURL() . VVE_DATA_DIR . "/" . HPSlideShow_Controller::DATA_DIR . "/";
         $tpl->addFile('tpl://hpslideshow:slideshow.phtml');
         $tpl->addFile('js://hpslideshow:slider.js');
         if ($assignCss) {
            $tpl->addFile('css://hpslideshow:style.less');
         }
      }
      return $tpl;
   }

}
